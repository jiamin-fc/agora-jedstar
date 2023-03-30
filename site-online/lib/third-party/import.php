<?php

function discoverAllFiles($dir){
    $files = array_diff(scandir($dir), array('.', '..'));
    $filelist = array();
    $dirs = array();
    foreach($files as $key=>$path){
        if (is_dir($dir."/".$path)){
            $dirs[] = $dir."/".$path;
        }else{
            if ($path != "deploy.php" && substr($path, -4) == ".php")
                $filelist[] = $dir."/".$path;
        }
    }

    for ($j = 0; $j < count($dirs); $j++){
        $filelist = array_merge($filelist, discoverAllFiles($dirs[$j]));
    }
    return $filelist;
}

function import_library($lib_name){
  if (is_dir(WEB_ROOT."../lib/third-party/".$lib_name)){
    $targets = discoverAllFiles(WEB_ROOT."../lib/third-party/".$lib_name);
    for ($i = 0; $i < count($targets); $i++){
      require_once($targets[$i]);
    }
  }
}
