<?php
require_once(WEB_ROOT."../lib/database.php");
require_once(WEB_ROOT."../lib/UserWallet.php");
$wallet = new UserWallet($args["walletID"]);
$wallet->authenticate($args["token"], getClientIP());
$wallet->expire_token();
$retArr = array(
  "status" => "ok"
);
