<?php
/**
 * This method allows the user to request withdrawal of funds
 **/

require_once(WEB_ROOT."../lib/MessageSigner.php");
require_once(WEB_ROOT."../lib/chain_functions.php");

$msg = "You are requesting to withdraw the full balance of funds for the currency ".$args["ticker"]." from Agora. You understand that these funds will be transferred back to the address they came from, on the most appropriate network, and that network gas fees will be borne by you.\n\nWithdrawals can take up to 72 hours, however your balance will still be usable in Agora until it is sent to you on the blockchain.\n\nUnique reference: ".$args["tstamp"]."-".$args["nonce"];
$recovered = MessageSigner::eth_verify_signature_by_message($msg, $args["signature"]);

if (strtolower($recovered) != strtolower($args["wallet"])){
  throw new Exception("Your signature does not match your wallet. Please check and try again.");
}
//Authenticate the user based on wallet and login token that should have previously been generated
require_once(WEB_ROOT."../lib/UserWallet.php");
$uw = new UserWallet($args["wallet"], false);
$uw->authenticate($args["token"], getClientIP());

$bals = $uw->get_balances();
if ($bals[$args["ticker"]] > 0){
  //Send notification down slack
  $rich_msg = format_slack_rich_message(
    "User ".$args["wallet"]." wants to withdraw ".$args["ticker"],
    array(
      "currency" => $args["ticker"],
      "balance" => $bals[$args["ticker"]],
      "wallet" => $args["wallet"]
    ),
    array(
      "icon_emoji" => ":money_with_wings:",
      "username" => "AGORA Wallet"
    )
  );
  $send_res = send_slack_message($rich_msg);
  $retArr = array(
    "status" => "ok",
    "debug" => "requested"
  );
}else{
  throw new Exception("You do not have any balance in the requested currency. Nothing will be done.");
}
