<?php
require_once(WEB_ROOT."../lib/UserWallet.php");
$wallet = new UserWallet($args["walletID"], false);
$wallet->authenticate($args["token"], getClientIP());

$retArr = array(
  "status" => "ok",
  "tx" => $wallet->get_spend_tx()
);
