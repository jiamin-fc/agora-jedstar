<?php
require_once(WEB_ROOT."../lib/database.php");
class Artist{
  private $artistinfo;

  function __construct($artistId){
    $prodsql = "SELECT idartists, name, imgurl, blurb, blockchainGroupId FROM artists WHERE idartists = ?";
    $prodres = db_pquery($prodsql, array(array("i", $artistId)));
    if (!$prodres["status"] || count($prodres["data"]) != 1 ){
      throw new Exception("Database error or invalid artist ".json_encode($prodres));
    }
    $this->artistinfo = $prodres["data"][0];
  }

  function get_id(){
    return $this->artistinfo["idartists"];
  }

  function get_name(){
    return $this->artistinfo["name"];
  }

  function get_imgurl(){
    return $this->artistinfo["imgurl"];
  }

  function get_blurb(){
    return $this->artistinfo["blurb"];
  }

  function get_blockchainGroupId(){
    return $this->artistinfo["blockchainGroupId"];
  }
}
