<?php
require_once(WEB_ROOT."../lib/CryptoCurrencyPHP/AddressCodec.class.php");
require_once(WEB_ROOT."../lib/CryptoCurrencyPHP/Base58.class.php");
require_once(WEB_ROOT."../lib/CryptoCurrencyPHP/PointMathGMP.class.php");
require_once(WEB_ROOT."../lib/CryptoCurrencyPHP/PrivateKey.class.php");
require_once(WEB_ROOT."../lib/CryptoCurrencyPHP/SECp256k1.class.php");
require_once(WEB_ROOT."../lib/CryptoCurrencyPHP/Signature.class.php");
require_once(WEB_ROOT."../lib/CryptoCurrencyPHP/Wallet.class.php");
require_once(WEB_ROOT."../lib/Sha3.php");
require_once("Keccak.php");

use kornrunner\Keccak;
use Elliptic\EC;

class MessageSigner{
  private $pk;
  function __construct($pk_string){
    $this->pk = new PrivateKey($pk_string);
  }

  function btc_get_address(){
    $compressedpubkey = AddressCodec::Compress($this->pk->getPubKeyPoints());
    $keyhash = AddressCodec::Hash($compressedpubkey);
    $btcaddress = AddressCodec::Encode($keyhash);
    return $btcaddress;
  }

  function btc_sign_message($message){
    $wallet = new Wallet($this->pk);
    return $wallet->signMessage($message);
  }

  function eth_sign_message($message){
    require_once(WEB_ROOT."../lib/bn-php/BigInteger.php");
    require_once(WEB_ROOT."../lib/bn-php/BN.php");
    require_once(WEB_ROOT."../lib/bn-php/Red.php");
    require_once(WEB_ROOT."../lib/elliptic-php/EC/Signature.php");
    require_once(WEB_ROOT."../lib/elliptic-php/EC/KeyPair.php");
    require_once(WEB_ROOT."../lib/elliptic-php/Utils.php");
    require_once(WEB_ROOT."../lib/elliptic-php/EdDSA.php");
    require_once(WEB_ROOT."../lib/elliptic-php/EdDSA/Signature.php");
    require_once(WEB_ROOT."../lib/elliptic-php/EdDSA/KeyPair.php");
    require_once(WEB_ROOT."../lib/elliptic-php/Curve/BaseCurve/Point.php");
    require_once(WEB_ROOT."../lib/elliptic-php/Curve/BaseCurve.php");

    require_once(WEB_ROOT."../lib/elliptic-php/Curve/ShortCurve/Point.php");
    require_once(WEB_ROOT."../lib/elliptic-php/Curve/ShortCurve/JPoint.php");

    require_once(WEB_ROOT."../lib/elliptic-php/Curve/EdwardsCurve.php");
    require_once(WEB_ROOT."../lib/elliptic-php/Curve/EdwardsCurve/Point.php");

    require_once(WEB_ROOT."../lib/elliptic-php/Curve/PresetCurve.php");

    require_once(WEB_ROOT."../lib/elliptic-php/Curve/MontCurve/Point.php");
    require_once(WEB_ROOT."../lib/elliptic-php/Curve/MontCurve.php");
    require_once(WEB_ROOT."../lib/elliptic-php/Curve/ShortCurve.php");
    require_once(WEB_ROOT."../lib/elliptic-php/Curves.php");

    require_once(WEB_ROOT."../lib/elliptic-php/HmacDRBG.php");
    require_once(WEB_ROOT."../lib/elliptic-php/EC.php");

    //REFERENCE FOR THIS CODE: https://ethereum.stackexchange.com/questions/86485/create-signed-message-without-json-rpc-node-in-php
    $stringToSign = "\x19Ethereum Signed Message:\n".strlen($message).$message;
    $hash = Sha3::hash($stringToSign, 256);
    $ec = new EC('secp256k1');
    $ecPrivateKey = $ec->keyFromPrivate("0x".$this->pk->getPrivateKey(),'hex');
    $signature = $ecPrivateKey->sign($hash, ['canonical' => true]);
    $r = str_pad($signature->r->toString(16), 64, '0', STR_PAD_LEFT);
    $s = str_pad($signature->s->toString(16), 64, '0', STR_PAD_LEFT);
    $v = dechex($signature->recoveryParam + 27);
    $signedMessage = "0x".$r.$s.$v;
    return $signedMessage;
  }

  function eth_sign_message_old($message){
    $stringToSign = "\x19Ethereum Signed Message:\n".strlen($message).$message;
    $hash = Sha3::hash($stringToSign, 256);
    $signed = "0x".Signature::signHash($hash, $this->pk->getPrivateKey());
    return $signed;
  }

  function eth_get_address(){
    $ethaddress = "0x".substr(Sha3::hash(hex2bin($this->pk->getPubKeyPoints()['x'].$this->pk->getPubKeyPoints()['y']), 256), -40);
    return $ethaddress;
  }

  public static function get_hash($message){
    $hash = Sha3::hash($message, 256);
    return $hash;
  }

  public static function eth_verify_signature_by_message($message, $signedMessage, $isLedger=false){
    $signedMessageStrip = substr($signedMessage, 2);
    $prefix = "\x19Ethereum Signed Message:\n".strlen($message);
    $stringToSign = $prefix.$message;
    $messageHex = Sha3::hash($stringToSign, 256); //this matches web3.sha() output for the given message and prefix.
    $messageGmp = gmp_init("0x".$messageHex);

    $r = substr($signedMessageStrip, 0,64);
    $s = substr($signedMessageStrip, 64,64);
    $v = substr($signedMessageStrip, 128,2);
    if ($isLedger){
      if ($v == 00){
        $v = "1B";
      }else if ($v == 01){
        $v = "1C";
      }
    }
    $vChecksum = hexdec($v) - 27;
    if($vChecksum !== 0 && $vChecksum !== 1) { throw new Exception("Invalid checksum."); }

    $rGmp = gmp_init("0x".$r);
    $sGmp = gmp_init("0x".$s);

    $publicKey = Signature::recoverPublicKey($rGmp, $sGmp, $messageGmp, $vChecksum);
    $recovered = "0x".substr(Sha3::hash(hex2bin($publicKey['x'].$publicKey['y']), 256),24);

    return $recovered;
  }

  public static function eth_verify_signature_by_hash($hash, $signedMessage){
    $signedMessageStrip = substr($signedMessage, 2);
    $messageHex = substr($hash, 2); //this matches web3.sha() output for the given message and prefix.
    $messageGmp = gmp_init($hash);

    $r = substr($signedMessageStrip, 0,64);
    $s = substr($signedMessageStrip, 64,64);
    $v = substr($signedMessageStrip, 128,2);

    $vChecksum = hexdec($v) - 27;
    if($vChecksum !== 0 && $vChecksum !== 1) { throw new Exception("Invalid checksum."); }

    $rGmp = gmp_init("0x".$r);
    $sGmp = gmp_init("0x".$s);

    $publicKey = Signature::recoverPublicKey($rGmp, $sGmp, $messageGmp, $vChecksum);
    $recovered = "0x".substr(Sha3::hash(hex2bin($publicKey['x'].$publicKey['y']), 256),24);

    return $recovered;
  }
}
