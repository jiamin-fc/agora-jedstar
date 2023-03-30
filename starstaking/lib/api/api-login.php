<?php
if (($args["tstamp"]/1000) + 60 < time()){
  throw new Exception("Login request has expired. Please try to sign in again.");
}
/*if (!trim($args["token"])){
  throw new Exception ("A captcha token is needed!");
}
$captcha = curl_action(
  "https://www.google.com/recaptcha/api/siteverify",
  array("secret"=>RECAPTCHA_SEC_KEY, "response"=>$args["token"])
);
if (!$captcha["success"]){
  throw new Exception("Invalid captcha response.");
}*/

require_once(WEB_ROOT."../lib/MessageSigner.php");
$msg = "You are requesting to login to your Jedstar Agora account.\n\nPlease tap the Sign button within 60 seconds to authorise this login.\n\nUnique login: ".$args["tstamp"]."-".$args["nonce"];
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
require_once(WEB_ROOT."../lib/database.php");
require_once(WEB_ROOT."../lib/UserWallet.php");
$wallet = new UserWallet($args["wallet"]);
$token = $wallet->do_login(getClientIP(), $args["tstamp"].$args["nonce"]);
$retArr = array(
  "status" => "ok",
  "token" => $token
);
