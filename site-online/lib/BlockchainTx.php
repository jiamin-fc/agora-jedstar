<?php
require_once(WEB_ROOT."../lib/MessageSigner.php");
require_once(WEB_ROOT."../lib/chain_functions.php");
use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Contract;
use Web3\Utils;
use Web3p\EthereumTx\Transaction;
use phpseclib\Math\BigInteger;
class BlockchainTx{
  static function signForJEDVerify($userWallet, $actionMsgArr, $nonce){
    $actionStr = "";
    for ($i = 0; $i < sizeof($actionMsgArr); $i++){
      if (substr($actionMsgArr[$i], 0, 2) == "0x"){
        $actionStr .= substr($actionMsgArr[$i], 2);
      }else{
        $actionStr .= bin2hex($actionMsgArr[$i]);
      }
    }
    $message = hex2bin(substr($userWallet, 2).$actionStr.bin2hex($nonce));
    $preppedHash = MessageSigner::get_hash($message);
    $msgHash = hex2bin($preppedHash);
    $signer = new MessageSigner(SERVER_SIGNING_KEY);
    $signedHash = $signer->eth_sign_message($msgHash);
    return $signedHash;
  }

  private static function _dynamicGasEscalation(&$eth, $txParams, $currGas, $maxGas){
    $txParams['gasPrice'] = '0x' . dechex($currGas);
    $tx = new Transaction($txParams);
    $signedTx = '0x' . $tx->sign(SERVER_SIGNING_KEY);
    $txHash = null;
    $txErr = null;
    $eth->sendRawTransaction($signedTx, function ($err, $txResult) use (&$txHash, &$txErr) {
      if($err) {
        error_log('transaction error: [' . $err->getMessage().']' . PHP_EOL);
        $txErr = $err;
      } else {
        $txHash = $txResult;
      }
    });
    if ($txErr != null){
      switch ($txErr->getCode()){
        case -32000:
          if ($currGas < $maxGas){
            $newGas = ceil($currGas + (($maxGas - $currGas) * 0.25));
            error_log("Insufficient gas - increasing proposal to ".$newGas);
            return BlockchainTx::_dynamicGasEscalation($eth, $txParams, $newGas, $maxGas);
          }else{
            //gas has exceeded acceptable limit
            return array("status"=>"fail", "message"=>"Transaction too expensive");
          }
          break;
        default:
          error_log("The transaction failed with error (".$txErr->getCode().") message ".$tx->getMessage().". I didn't know what to do.");
          return array("status"=>"fail", "code"=>$txErr->getCode(), "message"=>$txErr->getMessage());
      }
    }else{
      return array("status"=>"ok", "txHash"=>$txHash);
    }
  }

  static function execute(&$eth, $rawTx, $targetCtAddr){
    //create eth nonce
    $transactionCount = null;
    $eth->getTransactionCount(SERVER_SIGNING_ADDRESS, function ($err, $txCountRes) use (&$transactionCount){
      if ($err){
        error_log('getTransactionCount Error: '.$err->getMessage());
        $transactionCount = rand(99999, 999999);
      }else{
        $transactionCount = $txCountRes;
      }
    } );
    //prepare TX data
    $txParams = array(
      'nonce' => "0x".dechex($transactionCount->toString()),
      'from' => SERVER_SIGNING_ADDRESS,
      'to' => $targetCtAddr,
      'gas' => '0x'.dechex(8000000),
      'value' => '0x0',
      'data' => $rawTx
    );
    //Calculate gas
    $estimatedGas = null;
    $eth->estimateGas($txParams, function ($err, $gas) use (&$estimatedGas) {
      if ($err) {
        error_log('estimateGas error: ' . $err->getMessage() . PHP_EOL);
        $estimatedGas = new phpseclib\Math\BigInteger("9000000");
      } else {
        $estimatedGas = $gas;
      }
    });
    $estimatedGasInt = $estimatedGas->toString() * 1;
    error_log("\nEstimated gas: ".$estimatedGasInt."\n to mint on chain with ID ".MINTING_CHAINID);

    $gasPriceMultiplied = $estimatedGasInt * MINTING_GASMULTIPLIER;
    $txParams['chainId'] = MINTING_CHAINID;
    $mintRes = BlockchainTx::_dynamicGasEscalation($eth, $txParams, $gasPriceMultiplied, $gasPriceMultiplied*2);
    if ($mintRes["status"] != "ok"){
      throw new Exception("Failed to broadcast transaction: ".$mintRes["message"]);
    }
    $txHash = $mintRes["txHash"];

    error_log("txHash=".$txHash . PHP_EOL);

    $txReceipt = null;
    error_log("[".$txHash."] Waiting for transaction receipt");
    for ($i=0; $i <= MINTING_SECSTOWAIT; $i++) {
      try{
        $eth->getTransactionReceipt($txHash, function ($err, $txReceiptResult) use(&$txReceipt) {
          if($err) {
            error_log('['.$txHash.'] getTransactionReceipt error: ' . $err->getMessage() . PHP_EOL);
          } else {
            $txReceipt = $txReceiptResult;
          }
        });
      }catch(Exception $e){
        error_log("[".$txHash."] Error in collection receipt - will keep looking: ".$e->getMessage());
      }

      if ($txReceipt) {
        error_log("[".$txHash."] Receipt found");
        break;
      }
      sleep(1);
      error_log("[".$txHash."] Check for receipt...");
    }
    $txStatus = $txReceipt->status;

    return array("status" => $txStatus, "tx" => $txHash, "receipt"=>$txReceipt);
  }
}
