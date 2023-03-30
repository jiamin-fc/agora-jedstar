<?php
/**
 * This method generates a signature from the server that can be used to regen an NFT.
 * The act of signature creation is a chargeable event, and requires the user
 * to have sufficient balance in their account
 **/

require_once(WEB_ROOT."../lib/MessageSigner.php");
require_once(WEB_ROOT."../lib/chain_functions.php");
use Web3\Utils;
//Authenicate request
$msg = "You are requesting to regen your NFT attributes. This will change the values of your attributes, modifying your utility boosts, and is a chargeable activity. All values are randomly generated and you can't regen them for at least another 15 minutes. This will debit your Agora balance as follows.\n\nItem: ".$args["item_name"]."\nCost: ".$args["ticker"]." ".$args["value"]."\nToken ID: ".$args["tokenId"]."\n\nPlease tap the Sign button within 60 seconds to authorise this regen.\n\nUnique purchase reference: ".$args["tstamp"]."-".$args["nonce"];

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
error_log("[".$args["wallet"]."] Signature verified");
//Authenticate the user based on wallet and login token that should have previously been generated
require_once(WEB_ROOT."../lib/UserWallet.php");
$uw = new UserWallet($args["wallet"], false);
$uw->authenticate($args["token"], getClientIP());
//Check product ID, item name, currency support and pricing
require_once(WEB_ROOT."../lib/Product.php");
$product = new Product($args["product_id"]);
$price = $product->get_respec_price($args["ticker"]);
if ($price != $args["value"]){
  throw new Exception("Price mismatch. Please check the pricing and try again.");
}

//Check when the last respec was
$last_gen = getTokenLastGenTime($args["tokenId"]);
if (time() - $last_gen < 900){ //must be at least 15 mins
  throw new Exception("You need to wait at least 15 minutes from your last regen before you can request another one");
}

//Authorise purchase into the transaction ledger to prevent another round of spending
$description = "Regen NFT";
$config = json_encode(array(
  "tokenid" => $args["tokenId"]
));
//Check balance, and create bill
$dbtxid = $uw->make_spend($args["ticker"], $price, $description, $config, true);
//Support for 10 trillion NFTs to be created before issues can present
$nonce = $dbtxid.rand(100,999)."00000000000000000";
$time = time();
require_once(WEB_ROOT."../lib/BlockchainTx.php");
$timeHex = Utils::toHex($time);
for ($i=64 - strlen($timeHex); $i > 0; $i--){
  $timeHex = "0".$timeHex;
}

$sig = BlockchainTx::signForJEDVerify(SERVER_SIGNING_ADDRESS, array(
  strtolower($args["wallet"]),
  "0x".$timeHex,
  "0x".Utils::toHex($args["tokenId"])
), $nonce);

$out = respecNFT($args["tokenId"], $args["wallet"], $time, $nonce, $sig);
if ($out["status"] == "0x1"){
  //mark the tx as successful in the db
  $confirmed = $uw->update_purchase($dbtxid, $args["ticker"], $price, 1, array("txhash"=>$out["tx"]));
  $cache_res = curl_action(NFTCACHE_URL, array(
    "tokenId" => $args["tokenId"],
    "auth" => sha1($args["tokenId"]."jedstar")
  ));
  $retArr=array(
    "status" => "ok",
    "tx" => $out["tx"]
  );
}else{
  $reverted = $uw->update_purchase($dbtxid, $args["ticker"], $price, 3, array("txhash"=>$out["tx"]));
  $retArr = array(
    "status" => "fail",
    "tx" => $out,
  );
}
