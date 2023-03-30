<?php
require_once(WEB_ROOT."../lib/database.php");
class QueryCache{
  private $config;
  private $cachefile;
  private $data;
  function __construct($props){
    $this->config = $props;
    //if no validity period is supplied, default to 12 hours
    $this->config["validity_period"] = $this->config["validity_period"] ?: 43200;
    $this->cachefile = WEB_ROOT."objects/cache/".sha1($this->config["sql"].json_encode($this->config["vars"])).".qcache";
    if (!is_file($this->cachefile)){
      $res = $this->_execute_query();
      if ($res === false){
        return false;
      }
    }else{
      $fh = fopen($this->cachefile, "r");
      $filedata = json_decode(fread($fh, filesize($this->cachefile)), true);
      fclose($fh);
      if (time() - $filedata["built_when"] > $this->config["validity_period"]){
        //this file has expired, use the content for this read, but delete the file so it is not used next time
        unlink($this->cachefile);
        $this->_execute_query();
      }else{
        $this->data = $filedata;
      }
    }
  }
  function _execute_query(){
    $res = db_pquery($this->config["sql"], $this->config["vars"]);
    if ($res["status"] && count($res["data"]) > 0){
      $this->data = array(
        "result" => $res,
        "built_when" => time()
      );
      $fh = fopen($this->cachefile, "w");
      fwrite($fh, json_encode($this->data));
      fclose($fh);

      return $res["status"];
    }else{
      return false;
    }
  }

  function get_result(){
    return $this->data["result"];
  }

  function get_built_when(){
    return $this->data["built_when"];
  }

}
