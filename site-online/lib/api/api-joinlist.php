<?php
if (!trim($args["token"])){
  throw new Exception ("A captcha token is needed!");
}
$captcha = curl_action(
  "https://www.google.com/recaptcha/api/siteverify",
  array("secret"=>RECAPTCHA_SEC_KEY, "response"=>$args["token"])
);
if (!$captcha["success"]){
  error_log(json_encode($captcha));
  throw new Exception("Invalid captcha response.");
}
$res = validate_email(array("email"=>$args["email"]));
if ($res["status"]){
  $cr = curl_action(
    "https://api.sendinblue.com/v3/contacts/doubleOptinConfirmation",
    json_encode(array(
      "includeListIds"=>array(14),
      "email"=>$args["email"],
      "redirectionUrl"=>"https://www.silvervolt.app/blog",
      "templateId"=> 20
    )),
    false,false,
    true, //need the HTTP response code
    1,null,
    array(
      "Accept: application/json",
      "Content-Type: application/json",
      "api-key: ".SENDINBLUE_API_KEY
    )
  );
  if ($cr["_http_code"] == 201 || $cr["_http_code"] == 204){
    $retArr = array("status"=>"ok");
  }else{
    error_log(json_encode($cr));
    throw new Exception("Unable to add email");
  }
}else{
  throw new Exception("Invalid email address");
}
