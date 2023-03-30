<?php

class PageCache{
  private $cachefile;
  private $validity;
  private $ml;
  function __construct($props = array()){
    $this->cachefile = WEB_ROOT."objects/cache/".$props["page"].".pcache";
    $this->validity = $props["validity_period"];
    $this->ml = false; // assume no file exists

    if (is_file($this->cachefile)){
      $fh = fopen($this->cachefile, "r");
      $filedata = json_decode(fread($fh, filesize($this->cachefile)), true);
      fclose($fh);
      if (time() - $filedata["built_when"] > $this->validity){
        //page has expired
        unlink($this->cachefile);
      }else{
        $this->ml = $filedata["ml"];
      }
    }
  }

  function cache_ml($ml){
    //overwrite existing cache file
    $this->ml = $ml;
    $fh = fopen($this->cachefile, "w");
    fwrite($fh, json_encode(array("ml"=>$ml, "built_when"=>time())));
    fclose($fh);
  }

  function get_ml(){
    return $this->ml;
  }
}
