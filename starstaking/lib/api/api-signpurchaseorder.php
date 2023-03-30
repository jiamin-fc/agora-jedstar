<?php
/**
 * This method generates a signature from the server that can be used to mint an NFT.
 * The act of signature creation is a chargeable event, and requires the user
 * to have sufficient balance in their account
 **/

require_once(WEB_ROOT."../lib/MessageSigner.php");
require_once(WEB_ROOT."../lib/chain_functions.php");

$msg = "You are requesting to purchase an item from Jedstar Agora. This will debit your Agora balance accordingly.\n\nItem: ".$args["item_name"]."\nCost: ".$args["ticker"]." ".$args["value"]."\n\nPlease tap the Sign button within 60 seconds to authorise this purchase.\n\nUnique purchase reference: ".$args["tstamp"]."-".$args["nonce"];
try{
  $recovered = MessageSigner::eth_verify_signature_by_message($msg,$args["signature"]);
}catch (Exception $e){
  error_log("Problem verifying signature for wallet [".$args["wallet"]."]: ".$e->getMessage());
  $recovered = sha1("a".rand(0,1010101010).time())."ZZZ"; //should be impossible to provide as a wallet
}
if (strtolower($recovered) != strtolower($args["wallet"])){
  //first time failed, maybe this is a ledger?
  error_log("[".$args["wallet"]."] Regular signature verification failed, testing to see if it is a ledger...");
  try{
    $ledger_recovered = MessageSigner::eth_verify_signature_by_message($msg, $args["signature"], true);
  }catch(Exception $e){
    error_log("Failed to validate as a ledger: ".$e->getMessage());
    $ledger_recovered = sha1("B".rand(0,123123123).time())."ZZZ"; //should be impossible to provide as a wallet
  }
  if (strtolower($ledger_recovered) != strtolower($args["wallet"])){
    throw new Exception("Your signature does not match your wallet. Please check and try again. I expected [".$args["wallet"]."] and I received [".$recovered."] or [".$ledger_recovered."]");
  }
}

//Authenticate the user based on wallet and login token that should have previously been generated
require_once(WEB_ROOT."../lib/UserWallet.php");
$uw = new UserWallet($args["wallet"], false);
$uw->authenticate($args["token"], getClientIP());

//Check product ID, item name, currency support and pricing
require_once(WEB_ROOT."../lib/Product.php");
$product = new Product($args["product_id"]);
$price = $product->get_price($args["ticker"]);
if ($price != $args["value"]){
  throw new Exception("Price mismatch. Please check the pricing and try again.");
}

$description = "Purchase NFT";
$config = json_encode(array(
  "productid" => $args["product_id"],
  "name" => $product->get_name()
));

//Authorise purchase into the transaction ledger to prevent another round of spending
$dbtxid = $uw->make_spend($args["ticker"], $price, $description, $config, true);

//prepare a nonce for use in minting
$nonce = $dbtxid.rand(10000,99999);

//All verifications have been completed, now proceed to prepare the signature
//Only one NFT can be minted per signature
$qty = 1;
$base_type = $product->get_base_product_type();

require_once(WEB_ROOT."../lib/BlockchainTx.php");
$sig = BlockchainTx::signForJEDVerify(SERVER_SIGNING_ADDRESS, array(
  $args["wallet"],
  $base_type,
  $qty
), $nonce);

$out = mintNewNFT($base_type, $args["wallet"], $nonce, $sig);

if ($out["status"] == "0x1"){
  //mark the tx as successful in the db
  $confirmed = $uw->update_purchase($dbtxid, $args["ticker"], $price, 1, array("txhash"=>$out["tx"]));
  //return the TX to the user so they can find it on the chain
  $retArr = array(
    "status" => "ok",
    "qty" => $qty,
    "product_base_type" => $base_type,
    "tx" => $out["tx"]
  );
}else{
  $reverted = $uw->update_purchase($dbtxid, $args["ticker"], $price, 3, array("txhash"=>$out["tx"]));
  $retArr = array(
    "status" => "fail",
    "res" => $out
  );
}

//$deposit_sql = "SELECT ticker, human_value"
