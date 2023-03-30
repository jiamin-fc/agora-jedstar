<?php
require_once(WEB_ROOT."../lib/database.php");
class Product{
  private $productinfo;
  private $base_type;

  function __construct($productId){
    $prodsql = "SELECT idproducts, name, usd_base_price, idartists, description FROM products WHERE idproducts = ?";
    $prodres = db_pquery($prodsql, array(array("i", $productId)));
    if (!$prodres["status"] || count($prodres["data"]) != 1 ){
      throw new Exception("Database error or invalid product ".json_encode($prodres));
    }
    $this->productinfo = $prodres["data"][0];
  }

  function get_price($ticker){
    switch ($ticker){
      //stables
      case "DAI":
      case "USDC":
      case "USDT":
      case "BUSD":
      case "AGORA":
        $multiplier = 1;
        break;
      //KRED
      case "KRED":
        $multiplier = KRED_PER_USD;
        break;

      default:
        throw new Exception("Unsupported currency specified - ".$ticker);
        break;
    }
    return $this->productinfo["usd_base_price"] * $multiplier;
  }

  function get_respec_price($ticker){
    switch ($ticker){
      //stables
      case "DAI":
      case "USDC":
      case "USDT":
      case "BUSD":
      case "AGORA":
        $cost = 3;
        break;
      //KRED
      case "KRED":
        $cost = 30000; //approx $0.30
        break;

      default:
        throw new Exception("Unsupported currency specified - ".$ticker);
        break;
    }
    return $cost;
  }

  function get_all_prices(){
    return array(
      "DAI" => $this->get_price("DAI"),
      "USDC" => $this->get_price("USDC"),
      "USDT" => $this->get_price("USDT"),
      "BUSD" => $this->get_price("BUSD"),
      "KRED" => $this->get_price("KRED"),
      "AGORA" => $this->get_price("AGORA")
    );
  }

  function get_name(){
    return $this->productinfo["name"];
  }

  function get_id(){
    return $this->productinfo["idproducts"];
  }

  function get_artist(){
    require_once(WEB_ROOT."../lib/Artist.php");
    return new Artist($this->productinfo["idartists"]);
  }

  function get_description(){
    return $this->productinfo["description"];
  }

  function get_base_product_type(){
    if (!$this->base_type){
      require_once(WEB_ROOT."../lib/chain_functions.php");
      $this->base_type = productIdToBaseProductType($this->get_id());
      if (preg_match("/^[0-9]{77}$/", $this->base_type, $out) !== 1){
        //the values must be in hex, so they need to be converted
        $bn = new phpseclib\Math\BigInteger($this->base_type, 16);
        $this->base_type = $bn->toString();
      }
    }
    return $this->base_type;
  }

}
