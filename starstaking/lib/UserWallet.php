<?php
require_once(WEB_ROOT."../lib/database.php");
class UserWallet{
  private $walletID;
  private $accesstoken;
  private $ipaddress;
  private $last_used;
  private $spend_tx = false;
  private $deposit_tx = false;

  public const DECIMALS_DAI = 18;
  public const DECIMALS_USDT = 6;
  public const DECIMALS_USDC = 6;
  public const DECIMALS_KRED = 18;
  public const DECIMALS_BNB = 8;
  public const DECIMALS_MATIC = 18;
  public const DECIMALS_ETH = 18;
  public const DECIMALS_AGORA = 2;

  public const MAX_IDLE_TIME = 900; //15 mins of idle time is sufficient

  function __construct($walletID, $createIfNotExist = true){
    $sql = "SELECT wallet, accesstoken, ipaddress, UNIX_TIMESTAMP(last_used) as last_used_timestamp FROM user WHERE wallet = ?";
    $res = db_pquery($sql, array(
      array("s", $walletID)
    ));
    if ($res["status"] && count($res["data"]) == 1){
      $this->walletID = $res["data"][0]["wallet"];
      $this->accesstoken = $res["data"][0]["accesstoken"];
      $this->ipaddress = $res["data"][0]["ipaddress"];
      $this->last_used = $res["data"][0]["last_used_timestamp"];
    }else if ($createIfNotExist){
      $sqlins = "INSERT INTO user(wallet) VALUES (?)";
      $resins = db_pquery($sqlins, array(array("s", $walletID)));
      $this->walletID = $walletID;
    }else{
      throw new Exception("Invalid token or session expired. Please login.");
    }
  }

  function do_login($ipaddress, $usernonce){
    $check_nonce_sql = "SELECT count(*) as nonce_count FROM user_logins WHERE wallet = ? AND nonce = ?";
    $cn_res = db_pquery(
      $check_nonce_sql,
      array(
        array("s",$this->walletID),
        array("i",$usernonce)
      )
    );
    if ($cn_res["status"] && $cn_res["data"][0]["nonce_count"] == 0){
      $insert_nonce_sql = "INSERT INTO user_logins(wallet, nonce, ipaddress, createdwhen) VALUES (?,?,?,NOW())";
      $res = db_pquery($insert_nonce_sql, array(
        array("s",$this->walletID),
        array("i",$usernonce),
        array("s", $ipaddress)
      ));
      error_log(print_r($res, true));
    }else{
      throw new Exception("Your login request has expired. Please try to log in again.");
    }

    $token = sha1(rand(0,10000).$ipaddress.$this->walletID.$this->accesstoken.time().rand(0,500000).$usernonce);

    $sqlupd = "UPDATE user SET accesstoken=?, last_used=NOW(), ipaddress=? WHERE wallet=?";
    $updres = db_pquery($sqlupd, array(
      array("s", $token),
      array("s", $ipaddress),
      array("s", $this->walletID)
    ));
    if (!$updres["status"]){
      error_log("Wallet.php - There was an issue updating the user login table for wallet [".$this->walletID."]. The user will face issues with this login.");
    }
    return $token;
  }

  function authenticate($login_token, $ipaddress){
    if ($login_token != $this->accesstoken || time() - $this->last_used > UserWallet::MAX_IDLE_TIME){
      throw new Exception("Login invalid or expired. Please login again.");
    }else if ($this->ipaddress != $ipaddress){
      throw new Exception("Your IP address has changed since your last login. For security reasons your session has been terminated. Please log in again.");
    }
    $sql = "UPDATE user SET last_used=NOW() WHERE wallet=?";
    $res = db_pquery($sql, array(
      array("s", $this->walletID)
    ));
    if (!$res["status"]){
      error_log("Wallet.php : Trouble updating the last_used timestamp for user ".$this->walletID);
    }
    $this->last_used = time();
    return true;
  }

  function expire_token(){
    $sql = "UPDATE user SET accesstoken = ? WHERE wallet = ?";
    $res = db_pquery($sql, array(
      array("s", sha1(rand(0,99999).time().rand(2000,9000)."asdfasdfsadfa!!haz-29j..::".$this->walletID)),
      array("s", $this->walletID)
    ));
    if (!$res["status"] == true){
      error_log("Wallet.php : Trouble changing auth token for user ".$this->walletID);
    }
    return true;
  }

  function get_deposit_tx($network = null){
    if (!$this->deposit_tx){
      $sql = "SELECT network_id, txid, block, from_addr, value, decimals, ticker, human_value, block_time FROM blockchain_tx WHERE from_addr=? AND successful=1";
      $arr = array(array("s", $this->walletID));
      if ($network != null){
        $sql .= "AND network_id=?";
        $arr[] = array("i", $network);
      }
      $res = db_pquery($sql, $arr);

      if ($res["status"]){
        $this->deposit_tx = $res["data"];
      }else{
        throw new Exception("Unable to retrieve transactions for this user");
      }
    }
    return $this->deposit_tx;
  }

  function get_spend_tx(){
    if (!$this->spend_tx){
      $sql = "SELECT spend_txid, ticker, value, decimals, human_value, description, config, status, createdwhen FROM user_tx_spend WHERE status < 3 AND wallet = ?";
      $res = db_pquery($sql, array(array("s", $this->walletID)));
      if ($res["status"]){
        $this->spend_tx = $res["data"];
      }else{
        throw new Exception("Unable to retrieve transactions for this user");
      }
    }
    return $this->spend_tx;
  }

  function make_spend($ticker, $value, $description, $config, $authorisation_only = false){
    //clean up the value entry to ensure it is only two decimals
    $value = round($value * 100)/100;

    //check the spend is possible
    if (!$this->check_possible_spend($ticker, $value)){
      throw new Exception("Insufficient balance");
    }

    //the input $value is expected to be depicted as decimal(18,2)
    $decimals = UserWallet::get_ticker_decimals($ticker);

    $full_value = round($value * 100)."";
    for ($i = 0; $i < $decimals - 2; $i++){
      $full_value .= "0";
    }

    $sql = "INSERT INTO user_tx_spend(wallet, ticker, value, decimals, human_value, description, config, status, createdwhen) VALUES (?,?,?,?,?,?,?,?,NOW())";
    $res = db_pquery($sql, array(
      array("s", $this->walletID),
      array("s", $ticker),
      array("s", $full_value),
      array("i", $decimals),
      array("d", $value),
      array("s", $description),
      array("s", $config),
      array("i", $authorisation_only ? 0 : 1)
    ));

    return $res["insert_id"];
  }

  function get_spend_transaction($spend_txid){
    $getdetails_sql = "SELECT spend_txid, wallet, ticker, value, decimals, human_value, description, config, status, createdwhen FROM user_tx_spend WHERE spend_txid = ?";
    $details = db_pquery($getdetails_sql, array(array("i", $spend_txid)));
    if ($details["status"] && count($details["data"]) == 1){
      return $details["data"][0];
    }else{
      throw new Exception("Purchase transaction not found");
    }
  }

  function update_purchase($spend_txid, $ticker, $value, $new_status, $config_items=array()){
    $tx = $this->get_spend_transaction($spend_txid);
    $config = json_decode($tx["config"], true);
    foreach ($config_items as $key=>$value){
      $config[$key] = $value;
    }
    $tx["config"] = json_encode($config);

    $updsql = "UPDATE user_tx_spend SET status=?, config=? WHERE spend_txid=?";
    $res = db_pquery($updsql, array(
      array("i", $new_status),
      array("s", $tx["config"]),
      array("i", $spend_txid)
    ));
    switch($new_status){
      case 1: //confirm purchase
        $rich_msg = format_slack_rich_message(
          "User ".$tx["wallet"]." has spent ".$tx["ticker"]." ".$tx["human_value"],
          array(
            "spend" => $tx["ticker"]." ".$tx["human_value"],
            "description" => $tx["description"],
            "wallet" => $tx["wallet"]
          ),
          array(
            "icon_emoji" => ":moneybag:",
            "username" => "AGORA Wallet",
            "channel" => SLACK_WALLET_MSG
          )
        );
        $send_res = send_slack_message($rich_msg);
        break;
      case 3: //failed purchase
        $rich_msg = format_slack_rich_message(
          "User ".$tx["wallet"]." tried to spend ".$tx["ticker"]." ".$tx["human_value"]." BUT IT FAILED",
          array(
            "spend" => $tx["ticker"]." ".$tx["human_value"],
            "description" => $tx["description"],
            "wallet" => $tx["wallet"]
          ),
          array(
            "icon_emoji" => ":rotating_light:",
            "username" => "AGORA Wallet",
            "channel" => SLACK_WALLET_MSG
          )
        );
        $send_res = send_slack_message($rich_msg);
        break;
    }
    return ($res["status"] && $res["affected_rows"] == 1);
  }

  function check_possible_spend($ticker, $value){
    $bals = $this->get_balances();
    return $bals[$ticker] >= $value;
  }

  function get_balances(){
    $in = $this->get_deposit_tx();
    $out = $this->get_spend_tx();
    $balances = array();

    for ($i = 0; $i < count($in); $i++){
      $balances[$in[$i]["ticker"]] += $in[$i]["human_value"];
    }
    for ($i = 0; $i < count($out); $i++){
      $balances[$out[$i]["ticker"]] -= $out[$i]["human_value"];
    }

    return $balances;
  }

  public static function get_ticker_decimals($ticker){
    switch($ticker){
      case "DAI":
        $decimals = UserWallet::DECIMALS_DAI;
        break;
      case "USDT":
        $decimals = UserWallet::DECIMALS_USDT;
        break;
      case "USDC":
        $decimals = UserWallet::DECIMALS_USDC;
        break;
      case "KRED":
        $decimals = UserWallet::DECIMALS_KRED;
        break;
      case "BNB":
        $decimals = UserWallet::DECIMALS_BNB;
        break;
      case "MATIC":
        $decimals = UserWallet::DECIMALS_MATIC;
        break;
      case "ETH":
        $decimals = UserWallet::DECIMALS_ETH;
        break;
      case "AGORA":
        $decimals = UserWallet::DECIMALS_AGORA;
        break;
      default:
        throw new Exception("Unrecognised ticker");
        break;
    }
    return $decimals;
  }
}
