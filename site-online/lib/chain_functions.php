<?php
global $JNFTct, $JGct, $JDct, $JNct, $JMINTct;
require_once(WEB_ROOT."../vendor/autoload.php");
$abifiles = array(
  JEDSTAR_BASE_NFT_CT => array("path"=>WEB_ROOT."/content/abi/".JEDSTAR_BASE_NFT_CT.".json"),
  JEDSTAR_GAMING_CT => array("path"=>WEB_ROOT."/content/abi/".JEDSTAR_GAMING_CT.".json"),
  JEDSTAR_DELEGATION_CT => array("path"=>WEB_ROOT."/content/abi/".JEDSTAR_DELEGATION_CT.".json"),
  JEDSTAR_NUMBERS_CT => array("path"=>WEB_ROOT."/content/abi/".JEDSTAR_NUMBERS_CT.".json"),
  JEDSTAR_MINTER_CT => array("path"=>WEB_ROOT."/content/abi/JedstarNFTMinter.json")
);
foreach ($abifiles as $ct => $arr){
  $fh = fopen($arr["path"], "r");
  $data = fread($fh, filesize($arr["path"]));
  $abifiles[$ct]["data"] = $data;
  fclose($fh);
}
use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Contract;
use Web3\Utils;
use Web3p\EthereumTx\Transaction;
$JNFTct = new Contract(POLYGON_RPC.POLYGON_APIKEY, $abifiles[JEDSTAR_BASE_NFT_CT]["data"]);
$JGct = new Contract(POLYGON_RPC.POLYGON_APIKEY, $abifiles[JEDSTAR_GAMING_CT]["data"]);
$JDct = new Contract(POLYGON_RPC.POLYGON_APIKEY, $abifiles[JEDSTAR_DELEGATION_CT]["data"]);
$JNct = new Contract(POLYGON_RPC.POLYGON_APIKEY, $abifiles[JEDSTAR_NUMBERS_CT]["data"]);
$JMINTct = new Contract(POLYGON_RPC.POLYGON_APIKEY, $abifiles[JEDSTAR_MINTER_CT]["data"]);

function getWalletTokens($walletID){
  global $JNFTct;
  $tokens = array();
  $JNFTct->at(JEDSTAR_BASE_NFT_CT)->call(
    "getTokenIdsByWallet",
    $walletID,
    function ($err, $res) use (&$tokens){
      for ($i=0; $i < count($res[0]); $i++){
        $tokens[] = $res[0][$i]->toString();
      }
    }
  );
  return $tokens;
}

function convertTokenId($tokenId){
  global $JNct;
  $tok = array(
    "product_id"=>null,
    "fungible"=>null,
    "token_num"=>null
  );
  $JNct->at(JEDSTAR_NUMBERS_CT)->call(
    "check_number",
    $tokenId,
    function ($err, $res) use (&$tok){
      $tok["product_id"] = $res[0]->toString();
      $tok["fungible"] = $res[1];
      $tok["token_num"] = $res[2]->toString();
    }
  );
  return $tok;
}

function getTokenProps($tokenId){
  //Do not cache this info as it is dynamic
  global $JNFTct;
  $properties = array();
  $JNFTct->at(JEDSTAR_BASE_NFT_CT)->call(
    "getItemProperties",
    $tokenId,
    function($err, $res) use (&$properties) {
      for ($i = 0; $i < count($res[0]); $i++){
        $properties[] = $res[0][$i]->toString();
      }
    }
  );
  return $properties;
}
function getTokenLastGenTime($tokenId){
  //Do not cache this info as it is dynamic
  global $JNFTct;
  $tstamp = null;
  $JNFTct->at(JEDSTAR_BASE_NFT_CT)->call(
    "getItemLastGenTime",
    $tokenId,
    function($err, $res) use (&$tstamp) {
      $tstamp = $res[0]->toString();
    }
  );
  return $tstamp;
}
function getTotalProducts(){
  //Do not cache this info as it can be dynamic
  global $JGct;
  $prodcount = null;
  $JGct->at(JEDSTAR_GAMING_CT)->call(
    "getTotalProducts",
    "",
    function($err, $res) use (&$prodcount) {
      $prodcount = $res[0];
    }
  );
  return $prodcount->toString();
}
function mintNewNFT($baseProductType, $wallet, $nonce, $signature){
  global $JMINTct;

  //Grab provider reference
  $eth = $JMINTct->eth;
  //Capture raw tx data
  $rawTx = '0x'.$JMINTct->at(JEDSTAR_MINTER_CT)->getData(
    "mint",
    $wallet, $baseProductType, 1, '0x0000', $nonce, $signature
  );
  require_once(WEB_ROOT."../lib/BlockchainTx.php");
  return BlockchainTx::execute($eth, $rawTx, JEDSTAR_MINTER_CT);
}
function respecNFT($tokenId, $wallet, $time, $nonce, $sig){
  global $JNFTct;
  //Get the provider
  $eth = $JNFTct->eth;
  //Assemble the raw tx
  $rawTx = '0x'.$JNFTct->at(JEDSTAR_BASE_NFT_CT)->getData(
    "regenProperties",
    $tokenId, "0x0000000000000000000000000000000000000000", $nonce, $wallet, $time, $sig
  );
  require_once(WEB_ROOT."../lib/BlockchainTx.php");
  return BlockchainTx::execute($eth, $rawTx, JEDSTAR_BASE_NFT_CT);
}
function cacheOrPull($prefix, $var, $pullFn){
  if (file_exists(WEB_ROOT."/cache/chain-".$prefix."-".$var.".txt")){
    error_log("Loading from cache: ".$prefix."-".$var);
    $fh = fopen(WEB_ROOT."/cache/chain-".$prefix."-".$var.".txt", "r");
    $data = fread($fh, filesize(WEB_ROOT."/cache/chain-".$prefix."-".$var.".txt"));
    fclose($fh);
  }else{
    $data = $pullFn($var);
    try{
      $fh = fopen(WEB_ROOT."/cache/chain-".$prefix."-".$var.".txt", "w");
      if ($fh){
        fwrite($fh, $data);
        fclose($fh);
      }
    }catch (Exception $e){
      error_log("Unable to write to ".WEB_ROOT."/cache/chain-".$prefix."-".$var.".txt");
    }
  }
  return $data;
}
function productIdToBaseProductType($productId){
  return cacheOrPull("prodIdToBase",$productId, "_productIdToBaseProductType");
  // return _productIdToBaseProductType($productId);
}
function _productIdToBaseProductType($productId){
  global $JNct;
  $basetype = null;
  $JNct->at(JEDSTAR_NUMBERS_CT)->call(
    "make_number",
    $productId, false, 0,
    function ($err, $res) use (&$basetype) {
      $basetype = $res[0];
    }
  );

  return $basetype->toString();
}
function getProductName($productId){
  return cacheOrPull("productname", $productId, "_getProductName");
}
function _getProductName($productId){
  global $JGct;
  $JGct->at(JEDSTAR_GAMING_CT)->call(
    "getProductName",
    $productId,
    function ($err, $res) use (&$name) {
      $name = $res[0];
    }
  );
  return $name;
}

function getProductTier($productId){
  return cacheOrPull("productTier", $productId, "_getProductTier");
}
function _getProductTier($productId){
  global $JGct;
  $tier = null;
  $JGct->at(JEDSTAR_GAMING_CT)->call(
    "getProductTier",
    $productId,
    function ($err, $res) use (&$tier) {
      $tier = $res[0];
    }
  );
  return $tier;
}

function getProductGroupId($productId){
  return cacheOrPull("productgroup", $productId, "_getProductGroupId");
}
function _getProductGroupId($productId){
  global $JGct;
  $groupId = null;
  $JGct->at(JEDSTAR_GAMING_CT)->call(
    "getProductGroupId",
    $productId,
    function ($err, $res) use (&$groupId) {
      $groupId = $res[0];
    }
  );
  return $groupId;
}

function getGroupName($groupId){
  return cacheOrPull("groupname", $groupId, "_getGroupName");
}
function _getGroupName($groupId){
  global $JGct;
  $groupName = null;
  $JGct->at(JEDSTAR_GAMING_CT)->call(
    "getGroupName",
    $groupId,
    function ($err, $res) use (&$groupName) {
      $groupName = $res[0];
    }
  );
  return $groupName;
}
function getProductMintLimits($productId){
  return cacheOrPull("productmintlimits", $productId, "_getProductMintLimits");
}
function _getProductMintLimits($productId){
  global $JGct;
  $mintlimits = null;
  $JGct->at(JEDSTAR_GAMING_CT)->call(
    "getProductMintLimits",
    $productId,
    function ($err, $res) use (&$mintlimits) {
      $mintlimits = $res[0];
    }
  );
  return $mintlimits->toString();
}
function getGroupTypeId($groupId){
  return cacheOrPull("grouptypeid", $groupId, "_getGroupTypeId");
}
function _getGroupTypeId($groupId){
  global $JGct;
  $groupTypeId = null;
  $JGct->at(JEDSTAR_GAMING_CT)->call(
    "getGroupTypeId",
    $groupId,
    function ($err, $res) use (&$groupTypeId) {
      $groupTypeId = $res[0];
    }
  );
  return $groupTypeId->toString();
}
