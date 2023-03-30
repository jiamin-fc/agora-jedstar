<?php

global $mysqli_res;

function db(){
  global $mysqli_res;
  if (!$mysqli_res){
    $mysqli_res = new mysqli(Settings::$db_server, Settings::$db_username, Settings::$db_password, Settings::$db_name);
    if ($mysqli_res->connect_errno){
      //Connection failure
      throw new Exception("Failed to connect to MySQL: ".$mysqli_res->connect_error);
    }else{
      $mysqli_res->query("SET time_zone = '+00:00';");
      $mysqli_res->set_charset("utf8mb4");
    }
  }
  return $mysqli_res;
}

/**
 * Expect $sql in prepared statement format
 * e.g. SELECT foo, bar FROM abc WHERE ref=? AND id=?
 *
 * Expect $params in ordered array format (type, var)
 * e.g. array( array('s', 'foo'), array('i', 3), ....)
 **/
function db_pquery($sql, $params=array()){
  $param_types = "";
  for ($i = 0; $i < count($params); $i++){
    $param_types .= $params[$i][0];
  }

  $param_list = array();
  $param_list[] = & $param_types;
  for ($i = 0; $i < count($params); $i++){
    $param_list[] = & $params[$i][1];
  }

  $stmt = db()->stmt_init();
  $prepared = $stmt->prepare($sql);
    if (!$prepared){
    error_log("Failing query: '".$sql."' with params: ".json_encode($params));
    error_log(print_r($stmt, true));
    return array("status"=>false, "error"=>$stmt->error, "errno"=>$stmt->errno);
  }
  if (count($params) > 0){
    call_user_func_array(array($stmt, 'bind_param'), $param_list);
  }
  $stmt->execute();

  $stmt->store_result();
  $meta = $stmt->result_metadata();

  if ($meta == null && $stmt->errno){
    //an error occurred
    $return = array("status"=>false, "errno"=>$stmt->errno, "error"=>$stmt->error);
  }else if ($meta == null && $stmt->errno == 0){
    //no error, but no result set
    $return = array("status" => true, "affected_rows"=>$stmt->affected_rows, "insert_id"=>$stmt->insert_id);
  }else{
    //there is a result set
    $vars = array();
    $results = array();

    while ($col = $meta->fetch_field()) {
      $vars[] = &$results[$col->name];
    }
    call_user_func_array(array($stmt, 'bind_result'), $vars);

    $dataset = array();
    while($stmt->fetch()){
      $row = array();
      foreach ($results as $col_name=>$res){
        $row[$col_name] = $res;
      }
      $dataset[] = $row;
    }
    $return = array("status"=>true, "data"=>$dataset);
  }

  $stmt->close();

  return $return;
}
