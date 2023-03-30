<?php
require_once(WEB_ROOT."../lib/database.php");
require_once(WEB_ROOT."../lib/Product.php");
$product = new Product($args["productId"]);
$artist = $product->get_artist();
$retArr = array(
  "status" => "ok",
  "name" => $product->get_name(),
  "price" => $product->get_all_prices(),
  "description" => $product->get_description(),
  "artist" => array(
    "name" => $artist->get_name(),
    "imgurl" => $artist->get_imgurl(),
    "blurb" => $artist->get_blurb()
  )
);
