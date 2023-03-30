<?php
require_once("../../../lib/Settings.php");
require_once("../../../lib/SupportFunctions.php");
//header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Max-Age: 86400');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header('Content-Type: application/json');

//regex strings
// define("BLACKLIST_SPECIAL_CHARS", "/^[^`|'$#\\\\<>\\/]*$/");
// define("BASIC_REGEX_VALIDATION", "/^[a-zA-Z0-9-_\. !?:]*$/");

$retArr = array();
try{
  $args = array();
  foreach($_POST as $key=>$value){
    clean_vars($key, $value);
    $args[$key] = $value;
  }
  foreach ($_GET as $key=>$value){
    clean_vars($key, $value);
    $args[$key] = $value;
  }

  switch($args["action"]){
    case "joinlist":
    case "login":
    case "logout":
    case "signpurchaseorder":
    case "getspendhistory":
    case "getdeposithistory":
    case "getaccountbalances":
    case "getprice":
    case "regenrequest":
    case "withdraw":
    case "refreshnftbenefits":
      require_once(WEB_ROOT."../lib/api/api-".$args["action"].".php");
      break;
    default:
    throw new Exception("Unknown action", 418);
    break;
  }
}catch (Exception $e){
  $retArr = array();
  $retArr["status"] = "fail";
  switch ($e->getCode()){
    case 401:
      header('HTTP/1.0 401 Unauthorized');
      $retArr["debug"] = "Unauthorized";
      $retArr["msg"] = $e->getMessage();
      break;
    case 418:
      header('HTTP/1.0 418 I\'m a teapot');
      $retArr["debug"] = "I'm a teapot";
      $retArr["msg"] = $e->getMessage();
      break;
    case 503:
      header("HTTP/1.0 503 Service Unavailable");
      $retArr["debug"] = "Service Unavailable";
      $retArr["msg"] = $e->getMessage();
      break;
    default:
      $retArr["debug"]="Forbidden";
      header('HTTP/1.0 403 Forbidden');
      $retArr["msg"] = $e->getMessage();
      break;
  }
  error_log("!!! The following request [".json_encode($args)."] failed with this message from the system [".$e->getMessage()."]");
}
echo json_encode($retArr);
