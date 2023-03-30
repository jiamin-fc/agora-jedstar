<?php
require_once(WEB_ROOT."../lib/chain_functions.php");
require_once(WEB_ROOT."/../lib/SupportFunctions.php");
require_once(WEB_ROOT."/../vendor/autoload.php");

//Get list of tokens in this wallet from the chain
$tokens = getWalletTokens($args["wallet"]);
for ($i = 0; $i < count($tokens); $i++){
  if (preg_match("/^[0-9]{77}$/", $tokens[$i], $out) !== 1){
    //the values must be in hex, so they need to be decoded
    $bn = new phpseclib\Math\BigInteger($tokens[$i], 16);
    $tokens[$i] = $bn->toString();
  }
}

//Query blockchain for latest list of NFTs for this wallet

//Maintain list of NFT IDs and wallet addresses - ensuring that transfers are reflected properly

//Define list of possible benefits
$benefits = array(
  "SVBB"=> array("value"=>0, "provider"=>"default", "best"=>"h"), //bid boost
  "SVCA"=> array("value"=>[], "provider"=>"default", "best"=>"a"), //character avatars
  "SVCN"=> array("value"=>false, "provider"=>"default", "best"=>"h"), //custom name
  "SVHR"=> array("value"=>false, "provider"=>"default", "best"=>"h"), //high roller access
  "SVMB"=> array("value"=>0, "provider"=>"default", "best"=>"h"), //mining boost
  "SVRB"=> array("value"=>0, "provider"=>"default", "best"=>"h"), //referral boost
  "SVLT1"=> array("value"=>2, "provider"=>"default", "best"=>"l"), //lottery
  "SVLT2"=> array("value"=>12, "provider"=>"default", "best"=>"l"),
  "SVLT3"=> array("value"=>24, "provider"=>"default", "best"=>"l"),
  "SVWS1"=> array("value"=>2, "provider"=>"default", "best"=>"l"), //wheel spin
  "SVWS2"=> array("value"=>12, "provider"=>"default", "best"=>"l"),
  "SVWS3"=> array("value"=>24, "provider"=>"default", "best"=>"l")
);
$rarity_count = array();
//Create a unique list of providers
$token_seen = array();
$token_provider = array();
//Query nft.jedstar.com for stats and store the best benefit from each of them
for ($i = 0; $i < count($tokens); $i++){
  $cres = curl_action(NFTPUB_URL_BASE.$tokens[$i], array());
  if ($cres["agora"]){
    foreach ($benefits as $key => $criteria){
      $provider = false;
      switch ($criteria["best"]){
        case "h": // bigger numbers are better
          if ($cres["agora"][$key] > $criteria["value"]){
            $benefits[$key]["value"] = $cres["agora"][$key];
            $provider = true;
          }else if ($cres["agora"][$key] == $criteria["value"]){
            $provider = true;
          }
          break;
        case "a": //add to array
          for ($j = 0; $j < count($cres["agora"][$key]); $j++){
            if (!in_array($cres["agora"][$key][$j], $benefits[$key]["value"])){
              $benefits[$key]["value"][] = $cres["agora"][$key][$j];
              $provider = true;
            }
          }
          break;
        case "l": //smaller numbers are better
        default: //assume smaller numbers are better
          if (isset($cres["agora"][$key]) && $cres["agora"][$key] < $criteria["value"]){
            $benefits[$key]["value"] = $cres["agora"][$key];
            $provider = true;
          }else if (isset($cres["agora"][$key]) && $cres["agora"][$key] == $criteria["value"]){
            $provider = true;
          }
          break;
      }
      if ($provider){
        if (!is_array($benefits[$key]["provider"])) {
          $benefits[$key]["provider"] = array();
        }
        $benefits[$key]["provider"][] = $tokens[$i];
        if (!$token_seen[$tokens[$i]]){
          $token_seen[$tokens[$i]] = 1;
          $token_provider[] = $tokens[$i];
        }
      }
    }
  }
}

$retArr = array(
  "status" => "ok",
  "benefits" => $benefits,
  "providers" => $token_provider
);
error_log(print_r($retArr, true));
//Store benefits for this wallet ID with Silvervolt
