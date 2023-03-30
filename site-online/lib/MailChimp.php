<?php

class MailChimp{

  private $server;
  private $api_key;
  private $list_id;
  private $tags;

  function __construct($config){
    $this->server = $config["server"];
    $this->api_key = $config["api_key"];
    $this->list_id = $config["list_id"];
    //TODO: Make more generic so it can be used by other businesses
    $this->tags = array(
      "Worksheets" => 3361214,
      "Customer" => 3358966,
      "Trial Class Completed" => 3358974,
      "Paid Class Completed" => 3358982
    );
  }

  function validate($props){
    if (!validate_email($props)){
      throw new Exception ("Invalid email address");
    }

    $merge_fields = array("EMAIL"=>$props["email"]);
    foreach ($props as $key=>$value){
      switch($key){
        case "status":
        case "registrationID":
        case "CAT":
          $merge_fields[$key] = $value;
          break;
      }
    }

    $mc_arr = array(
      "email_address"=>$props["email"],
      "status"=>($props["status"] == "paid" ? "paid" : "pending"),
      "merge_fields"=>$merge_fields,
      
    );

    return array("status"=>($mc_arr["status"]== "paid" ? "ok" : "fail"), "mc"=>$mc_arr);

  }



  function add_email_address($props){
    if (!validate_email($props)){
      throw new Exception ("Invalid email address");
    }

    $email_hash = md5(strtolower($props["email"]));

    $merge_fields = array("EMAIL"=>$props["email"]);
    foreach ($props as $key=>$value){
      switch($key){
        case "FNAME":
        case "LNAME":
        case "ADDRESS":
        case "PHONE":
        case "BIRTHDAY":
        case "CAT":
          $merge_fields[$key] = $value;
          break;
      }
    }

    $mc_arr = array(
      "email_address"=>$props["email"],
      "status"=>($props["status"] == "subscribed" ? "subscribed" : "unsubscribed"),
      "merge_fields"=>$merge_fields,
      //TODO - make the marketing permission ID part more generic so it can be used by other businesses
      "marketing_permissions"=>[["marketing_permission_id"=>"3126b69925","enabled"=>true]]
    );

    if (is_array($props["tags"])){
      for ($i=0; $i < count($props["tags"]); $i++){
        if ($this->tags[$props["tags"][$i]]){
          $mc_arr["tags"][] = $props["tags"][$i]; //array("id"=>$this->tags[$props["tags"][$i]], "name"=>$props["tags"][$i]);
        }
      }
      //$mc_arr
    }
    error_log(json_encode($mc_arr));
    $response = curl_action(
      "https://".$this->server.".api.mailchimp.com/3.0/lists/".$this->list_id."/members/".$email_hash,
      json_encode($mc_arr),
      false, false, 1, "PUT",
      "api:".$this->api_key
    );

    return array("status"=>($response["_http_code"] == 200 ? "ok" : "fail"), "mc"=>$response);
  }

  function get_member_details($input){
    if (strpos($input, '@')){
      $email_hash = md5(strtolower($input));
    }else{
      $email_hash = $input;
    }
    $res = curl_action(
      "https://".$this->server.".api.mailchimp.com/3.0/lists/".$this->list_id."/members/".$email_hash,
      array(),
      false, false, 1, 0,
      "api:".$this->api_key
    );

    return array("status"=>($res["_http_code"] == 200 ? "ok" : "fail"), "mc"=>$res);
  }

}
