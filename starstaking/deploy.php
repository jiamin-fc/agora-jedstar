<?php
$PROJECT = array(
  "name"=>"Agora",
  "proddir" => "/var/www/agora",
  "ignores" => array("lib/Settings.php", "deploy.php"),
  "git_branch" => "main",
  "settings" => null //"lib/Settings.prod.php"
);

if (isset($argv) && isset($argv[1]) && $argv[1] == "prepare"){
  exec('git ls-tree -r '.$PROJECT["git_branch"].' --name-only', $git_filelist);
  $fh = fopen("filelist.json", "w");
  if (!$fh){
    echo "\nFAIL - Unable to write filelist. ABORTING.\n";
    die(1);
  }
  fwrite($fh, json_encode($git_filelist));
  fclose($fh);
  echo "\n".count($git_filelist)." files specified in the repo. File list written.\nYou can now deploy.\n";
  die(0);
}else{
  $fh = fopen("filelist.json", "r");
  if (!$fh){
    echo "\nFAIL - Unable to open git filelist. Please run 'php deploy.php prepare' in the git repo first.\nABORTING.\n";
    die(0);
  }
  $git_filelist = json_decode(fread($fh, filesize("filelist.json")));
  fclose($fh);
  if (count($git_filelist) < 1){
    echo "\nFAIL - There do not seem to be any files specified in the repo. Was 'prepare' run correctly?\nABORTING.\n";
    die(1);
  }
}



function discoverAllFiles($dir){
    $files = array_diff(scandir($dir), array('.', '..'));
    $filelist = array();
    $dirs = array();
    foreach($files as $key=>$path){
        if (is_dir($dir."/".$path)){
            $dirs[] = $dir."/".$path;
        }else{
            if ($path != "deploy.php")
                $filelist[] = $dir."/".$path;
        }
    }

    for ($j = 0; $j < count($dirs); $j++){
        $filelist = array_merge($filelist, discoverAllFiles($dirs[$j]));
    }
    return $filelist;
}

echo "Deploying ".$PROJECT["name"]." website...\n";
echo "Please specify target deployment directory [".$PROJECT["proddir"]."]: ";
$target = rtrim(fgets(STDIN),"\n");
if (!$target || $target == ""){
    $target = $PROJECT["proddir"];
}
if (substr($target, -1) == '/'){
  $target = substr($target, 0, -1);
}
$check = is_file($target."/deploy-target.ok");
if (!$check){
    echo "WARNING: The provided location (".$target.") does not appear to have been deployed into before. Please confirm that you want to deploy into this location.\n\nCONFIRM? Y/N\n";
    $resp = rtrim(fgets(STDIN),"\n");
    if ($resp != "Y" && $resp != "y"){
      echo "\n'Y' was required to proceed - it was not found. ABORTING.\n";
      die(1);
    }else{
      $fh = fopen($target."/deploy-target.ok", "w");
      if (!$fh){
        echo "Unable to write to target location. ABORTING.\n";
        die(1);
      }
      fwrite($fh, time());
      fclose($fh);
    }
}else{
  $fh = fopen($target."/deploy-target.ok", "r");
  if (!$fh){
    echo "No read permissions to location. ABORTING.\n";
    die(1);
  }
  $lastdeploy = fread($fh, filesize($target."/deploy-target.ok"));
  fclose($fh);
  $date = new DateTime("@".trim($lastdeploy));
  echo "Last successful deployment was executed on ".$date->format("H:i:s d M Y")."\n";
}
if (!$PROJECT["settings"] == null){
  echo "What Settings file should be used for the deployment (use relative path)? [".$PROJECT["settings"]."] \n";
  $settings = rtrim(fgets(STDIN),"\n");
  if (!$settings || $settings == ""){
    $settings = $PROJECT["settings"];
  }
  if (!is_file($settings)){
    echo "The file you specified (".$settings.") could not be found. ABORTING.\n";
    die(1);
  }
}
echo "Starting deployment...";
$cwd = getcwd();
echo "Generating list of files to copy... ";

$source_files = discoverAllFiles($cwd);
$git_files = array();
$ignores = 0;
for ($i = 0; $i < count($git_filelist); $i++){
  if (in_array($git_filelist[$i], $PROJECT["ignores"])){
    $ignores++;
  }else{
    $git_files[] = $cwd."/".$git_filelist[$i];
  }
}
echo count($source_files)." total files found,\n";
echo $ignores." repo files will be ignored,\n";
echo count($git_files)." files from the repo will be copied\n";

$target_files = str_replace($cwd, $target, $source_files);
echo "Copying files... ";
$copied = 0;
$failed = 0;
$ignored = 0;

//start the copying
for ($i=0; $i < count($source_files); $i++){
	if (!file_exists(dirname($target_files[$i])) && !mkdir(dirname($target_files[$i]),0775,true)) {
		die("Cannot create the folder ".dirname($target_files[$i]));
	}
  if (!in_array($source_files[$i], $git_files)){
    echo "Ignoring ".$source_files[$i]." as it is not a candidate\n";
    $ignored++;
  }else{
    $result = copy($source_files[$i], $target_files[$i]);
    if (!$result){
      echo "!!! WARN !!! Failed to copy file ".$source_files[$i]."\n";
      $failed++;
    }else{
      echo "Wrote ".$target_files[$i]."\n";
      $copied++;
    }
  }
}
echo $copied." files copied, ".$ignored." ignored, ".$failed." failed.\n";
if (!$PROJECT["settings"] == null){
  echo "Renaming specific files... ";
  $renames = array(
    array($settings, "/lib/Settings", ".php")
  );
  for ($i=0; $i < count($renames); $i++){
    if (file_exists($target.'/'.$renames[$i][1].$renames[$i][2])) {
      rename($target.'/'.$renames[$i][1].$renames[$i][2], $target.'/'.$renames[$i][1].".".date("Ymd",time()).$renames[$i][2]); //create backup
    }
    rename($target.'/'.$renames[$i][0], $target.'/'.$renames[$i][1].$renames[$i][2]); //move new file over
  }
  echo " done\n";
}
echo "Writing deploy timestamp... ";
$fh = fopen($target."/deploy-target.ok", "w");
if (!$fh){
  echo "Unable to write to target location. Unclean finish.\n";
  die(1);
}
fwrite($fh, time());
fclose($fh);
echo " done.\n";
