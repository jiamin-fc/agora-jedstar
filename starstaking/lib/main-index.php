<?php
//error_reporting(0);
require_once("Settings.php");
//include the Template
require_once("templates/agora.php");
require_once("SupportFunctions.php");

global $args, $blogroll;
$args = array();
foreach($_POST as $key=>$value){
  try{
    clean_vars($key, $value);
    $args[$key] = $value;
  }catch(Exception $e){
    $args[$key] = null;
  }
}
foreach ($_GET as $key=>$value){
  try{
    clean_vars($key, $value);
    $args[$key] = $value;
  }catch(Exception $e){
    $args[$key] = null;
  }
}

//cache check function
function check_cache_regen($cache_timeout, $cache_file){
    $regen = true;
    if (file_exists($cache_file)){
        $fh = fopen($cache_file, "r");
        $json_raw = fread($fh, filesize($cache_file));
        $cached = json_decode($json_raw, true);
        fclose($fh);
        if ($cached["last_created"] + $cache_timeout > time()){
            $regen = false;
        }
    }
    return array("regenerate" => $regen, "cache" => $cached);
}
//page list function
function get_page_list($filelist, $substitutes = array(), $prefix=""){
    $posts_available = array();
    for ($i=0; $i < sizeof($filelist); $i++){
        $entry = explode(".", $filelist[$i]);
        if ($entry[1] == "json" && $entry[0] != "404-not-found" && substr($entry[0],0,1) != "_"){
            $posts_available[] = isset($substitutes[$entry[0]]) ? $prefix.$substitutes[$entry[0]]["replacement"] : $prefix.$entry[0];
        }
    }
    return $posts_available;
}
//redirect candidate function
function apply_redirect_if_necessary($path, $redirect_instructions){
    foreach ($redirect_instructions as $stub => $url){
        if ($path == $stub || $path == $stub."/"){
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$url);
            exit();
        }
    }
}

function  order_by_publish_date ($posts, $dir) {
    $result = array();
    for($i = 0; $i < count($posts); $i++) {
        $fh = fopen($dir."/".$posts[$i].".json", "r");
        $json_raw = fread($fh, filesize($dir."/".$posts[$i].".json"));
        $json = json_decode($json_raw, true);
        fclose($fh);
        $publish_date = $json["author_data"]["publish_date"];
        if ($publish_date) {
            $fulldate = explode(" ", $publish_date);
            $month = $fulldate[0];
            $year = $fulldate[2];
            $date = str_replace(["st", "nd", "rd", "th", ","], "", $fulldate[1]);
            $dt = DateTime::createFromFormat('d/F/Y', $date."/".$month."/".$year);
            $timestamp = $dt->getTimestamp();
            $json["filename"]  = $posts[$i];
            $json["publish_timestamp"] = $timestamp;
            $result[] = $json;
        }
    }
    array_multisort (array_column($result, 'publish_timestamp'), SORT_DESC, $result);
    return $result;
}

function ml_from_array($arr){
  $ml = "";
  foreach ($arr as $node=>$elem){
    if (is_array($elem)){
      if (is_numeric($node)){
        $ml .= ml_from_array($elem)."\n";
      }else{
        $ml .= "<".$node.">".ml_from_array($elem)."</".$node.">\n";
      }
    }else if (!$elem){
      $ml .= "<".$node." />\n";
    }else{
      $ml .= "<".$node.">".$elem."</".$node.">\n";
    }
  }
  return $ml;
}

//check if the provided URL needs to be redirected
if (isset($REDIRECT_PATHS)){
  apply_redirect_if_necessary($_GET["c"], $REDIRECT_PATHS);
}
//sanitize the user request
$page_params = explode('/', $_GET["c"]);
if ($page_params[0] == "blog"){
    $blog = true;
    $tgt_content = $page_params[1];
}else{
    $blog = false;
    $tgt = $page_params[0];
    $special_css_file = $page_params[0];
    for ($i=1; $i < count($page_params); $i++){
        if (strlen($page_params[$i]) > 0){
            $special_css_file .= "-".$page_params[$i];
        }
    }
    $tgt_content = $page_params[0] == "index.php" ? "index" : $tgt;
}
nblog("User requested: ".$tgt_content);
if (strlen($tgt_content) > 0 && preg_match('/^[a-zA-Z0-9-_]+$/', $tgt_content, $out) !== 1){
    $tgt_content = "404-not-found";
    header('HTTP/1.0 404 Not Found');
}else if (strlen($tgt_content) < 3){
    //assume that the chars are pagination indexes
    $pagination_pos = intval($tgt_content);
    $tgt_content = "index";
}
nblog("Will load: ".$tgt_content);
//generate the list of available content files
$posts_available = array();
if ($blog){
    $content_dir = $POST_DIR;
}else{
    $content_dir = $PAGES_DIR;
}
$filelist = scandir($content_dir, SCANDIR_SORT_DESCENDING);
$posts_available = get_page_list($filelist);

//generate blogroll
$broll_cache = check_cache_regen($FILE_GENERATION_CACHE_TIMEOUT_SECS, $POST_DIR."/blogroll.cache");
//Only regenerate the blog roll if the page request is to a blog page - otherwise ignore and use the cache
if ($blog && $broll_cache["regenerate"]){
    $blogroll = "";
    for ($i=0; $i < $BLOGROLL_LIMIT && $i < sizeof($posts_available); $i++){
        if (file_exists($POST_DIR."/".$posts_available[$i].".json") && file_exists($POST_DIR."/".$posts_available[$i].".ml")){
            $fh = fopen($POST_DIR."/".$posts_available[$i].".json", "r");
            $json_raw = fread($fh, filesize($POST_DIR."/".$posts_available[$i].".json"));
            $json = json_decode($json_raw, true);
            fclose($fh);

            $blogroll .= '<div class="bloglist"><div class="bloglist-image"><a href="'.$BASE_BLOG_URL.$posts_available[$i].'"><img src="" loading="lazy" class="animated-placeholder" alt="blog-image" data-gfxtype="src" data-gfxdata="'.$json["post_graphic_url"].'"></a></div><div class="bloglist-title"><h2 class="capitalise"><a href="'.$BASE_BLOG_URL.$posts_available[$i].'">'.$json["title"].'</a></h2></div><div class="bloglist-desc"><p>'.$json["description"].'</p></div></div>';
        }
    }
    $broll = json_encode(array("last_created"=>time(), "ml"=>$blogroll));
    $fh = fopen($POST_DIR."/blogroll.cache", "w");
    fwrite($fh, $broll);
    fclose($fh);

    //also regenerate the sitemap
    $blogdir = scandir($POST_DIR, SCANDIR_SORT_DESCENDING);
    $pagedir = scandir($PAGES_DIR, SCANDIR_SORT_DESCENDING);
    $url_list = array_merge(get_page_list($pagedir, array("index"=>array("replacement"=>"")), $BASE_URL), get_page_list($blogdir, array(), $BASE_BLOG_URL));

    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
    "<?xml-stylesheet type=\"text/xsl\" ?>\n" .
    "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"\n" .
    "        xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n" .
    "        xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\n" .
    "        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n";
    for ($i=0; $i < sizeof($url_list); $i++){
        $xml .= "<url><loc>" . htmlentities ($url_list[$i]) ."</loc></url>\n";
    }
    $xml .= "</urlset>";
    $fh = fopen(WEB_ROOT."content".$SITEMAP_XML_TARGET, "w");
    fwrite($fh, $xml);
    fclose($fh);

    //check to see if the RSS feed needs to be rebuilt
    if (file_exists($POST_DIR."/rssfeed.filelist")){
      $fh = fopen($POST_DIR."/rssfeed.filelist", "r");
      $rssfeedfilelist = fread($fh, filesize($POST_DIR."/rssfeed.filelist"));
      fclose($fh);
    }else{
      $rssfeedfilelist = "[]";
    }
    $blog_page_list = get_page_list($blogdir, array(), $POST_DIR."/");
    if ($rssfeedfilelist != json_encode($blog_page_list)) {
      //new pages must exist, we need to determine which ones they are and then write the updated RSS feed
      if (file_exists($POST_DIR."/rssfeed.summaries")){
        $fh = fopen($POST_DIR."/rssfeed.summaries", "r");
        $summaries_json = fread($fh, filesize($POST_DIR."/rssfeed.summaries"));
        fclose($fh);
      }else{
        $summaries_json = "{}";
      }
      $page_summaries = json_decode($summaries_json, true);

      //prep RSS feed data structure
      $rss_array = array(
        "channel" => array(
          'atom:link href="https://www.silvervolt.app/feed.rss" rel="self" type="application/rss+xml"' => null,
          "title" => "Silvervolt - Get your gaming top up for free!",
          "link" => "https://www.silvervolt.app",
          "description" => "Get your gaming top up for free!",
          "lastBuildDate" => date("D, d M Y H:i:s O"),
          "language" => "en-US",
          "sy:updatePeriod" => "hourly",
          "sy:updateFrequency" => 1,
          "generator" => "Silvervolt RSS Feed Creator v1,0",
          "item" => array()
        )
      );
      //iterate through each file in the dir and rebuild the list
      //TODO optimise for when this list is large with hundreds of pages
      for ($i = 0; $i < sizeof($blog_page_list); $i++){
        if (!$page_summaries[$blog_page_list[$i]]){
          $fh = fopen($blog_page_list[$i].".json", "r");
          $pageinfo_json = fread($fh, filesize($blog_page_list[$i].".json"));
          fclose($fh);

          $pageinfo = json_decode($pageinfo_json, true);
          $page_summaries[$blog_page_list[$i]] = array(
            "title" => $pageinfo["title"],
            "link" => $pageinfo["canonical_url"],
            "dc:creator" => "<![CDATA[".$pageinfo["author_data"]["author_name"]."]]>",
            "pubDate" => $pageinfo["author_data"]["publish_date"],
            "guid" => $pageinfo["canonical_url"],
            "description" => $pageinfo["description"]
          );
        }
        $rss_array["channel"]["item"][] = $page_summaries[$blog_page_list[$i]];
      }
      //write the update page summaries cache
      $fh = fopen($POST_DIR."/rssfeed.summaries", "w");
      fwrite($fh, json_encode($page_summaries));
      fclose($fh);

      //write the updated filelist
      $fh = fopen($POST_DIR."/rssfeed.filelist", "w");
      fwrite($fh, json_encode($blog_page_list));
      fclose($fh);

      //write the public RSS file for ATOM readers
      $rssxml = '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"
  xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	>
';
      $rssxml .= ml_from_array($rss_array);
      $rssxml .= "\n</rss>";
      $fh = fopen(WEB_ROOT."content".RSS_TARGET, "w");
      fwrite($fh, $rssxml);
      fclose($fh);
    }
}else{
    $blogroll = $broll_cache["cache"]["ml"];
}

if ($blog && $tgt_content == "index"){
    //special treatment as the blog index needs to be generated
    if ($pagination_pos * POSTS_PER_PAGE > sizeof($posts_available)){
        $pagination_pos = 0;
    }
    $index_cache_filename = $POST_DIR."/index".$pagination_pos.".cache";
    $index_cache = check_cache_regen($FILE_GENERATION_CACHE_TIMEOUT_SECS, $index_cache_filename);
    if ($index_cache["regenerate"]){
        $entries = array();
        $images = array();
        //retrieve the JSON details for each post
        for ($i = $pagination_pos * POSTS_PER_PAGE; $i < sizeof($posts_available) && $i < ($pagination_pos+1) * POSTS_PER_PAGE; $i++){
            $fh = fopen($POST_DIR."/".$posts_available[$i].".json", "r");
            $json_raw = fread($fh, filesize($POST_DIR."/".$posts_available[$i].".json"));
            $json = json_decode($json_raw, true);
            $json["post-stub"] = $posts_available[$i];
            fclose($fh);
            $json["canonical_url"] = $BASE_BLOG_URL.$posts_available[$i];
            $entries[] = $json;
            $images[] = array("target"=>$posts_available[$i], "src"=>$json["post_graphic_url"]);
        }
        //read in the blogindex.css
        if (file_exists($POST_DIR."/blogindex.css")){
          $fh = fopen($POST_DIR."/blogindex.css", "r");
          $blogindex_css = fread($fh, filesize($POST_DIR."/blogindex.css"));
          fclose($fh);
        }
        //minify the CSS
        // $blogindex_css = preg_replace(
        //  array('/\s*(\w)\s*{\s*/','/\s*(\S*:)(\s*)([^;]*)(\s|\n)*;(\n|\s)*/','/\n/','/\s*}\s*/'), array('$1{ ','$1$3;',"",'} '),
        //  $blogindex_css
        // );
        //get the ML created from the JSON posts array
        $fp = array_splice($entries, 0, 1)[0];
        $blog_page = get_blog_index_ml($entries,sizeof($posts_available),$pagination_pos);
        $details = array(
            "canonical_url" => $BASE_BLOG_URL.$pagination_pos,
            "extra_css" => $blogindex_css,
            "blogindex" => true,
            "description" => "The Silvervolt Blog",
            "bodyclass" => $blog ? "blogpost" : "",
            "title" => "The Silvervolt Blog",
            "subtitle" => "It's electrifying!",
            "first_post" => array(
              "img_hi" => $fp["header_background_url"],
              "img_lo" => $fp["header_background_url_lo"],
              "publish_date" => $fp["publish_info"],
              "tag" => $fp["tag"],
              "title" => $fp["title"],
              "blurb" => $fp["description"],
              "stub" => $fp["canonical_url"]
            ),
            "read_description" => "",
            "twitter_meta_tags" => $json["twitter_meta_tags"],
            "header_background_url" => $fp["header_background_url"],
            "pagecontent" => $blog_page["ml"],
            "blogroll" => $blogroll,
            "additionalLoadFullJS" => false,
            "imgarr" => json_encode($blog_page["img"] ?: []),
            "js_files" => array("/js/blog.js"),
            "last_created" => time()
        );
        $fh = fopen($index_cache_filename, "w");
        fwrite($fh, json_encode($details));
        fclose($fh);
    }else{
        $details = $index_cache["cache"];
    }

}else{
    //load the individual post
    //when trying to read a post, check that it actually exists, else throw the 404 in there
    if (!file_exists($content_dir."/".$tgt_content.".json") || !file_exists($content_dir."/".$tgt_content.".ml")){
      nblog("404 - Requested for ".$content_dir."/".$tgt_content);
        $blog = false;
        $tgt_content = "404-not-found";
        header('HTTP/1.0 404 Not Found');
    }
  //find next blog for inner pagination
    $next = '';
    $prev = '';
    $next_title = '';
    $prev_title = '';
    $inner_pagination_title = function ($get_post, $dir) {
        if($get_post){
            $fh = fopen($dir."/".$get_post.".json", "r");
            $json_raw = fread($fh, filesize($dir."/".$get_post.".json"));
            $json = json_decode($json_raw,true);
            fclose($fh);
            return $json["title"];
        }
    };

    $fh = fopen($content_dir."/".$tgt_content.".json", "r");
    $json_raw = fread($fh, filesize($content_dir."/".$tgt_content.".json"));
    $json = json_decode($json_raw,true);
    fclose($fh);
    if ($json["requires-php"]===true){
        //this page is dynamic and needs processing
        nblog("Dynamic processing for: ".$content_dir."/".$tgt_content.".php");
        require_once($content_dir."/".$tgt_content.".php");
        $page_ml = render_dynamic_content($page_params);
        //if the page indicates a total failure, it is implied 404
        if ($page_ml === false){
          header("HTTP/1.0 404 Not Found");
          header("Location: /");
          die();
        }

        if ($json["dynamic-json"] === true){
            $json_mods = get_json_customisations($page_params);
            foreach ($json_mods as $key=>$value){
                $json[$key] = $value;
            }
        }
    }else{
        $fh = fopen($content_dir."/".$tgt_content.".ml","r");
        $page_ml = fread($fh, filesize($content_dir."/".$tgt_content.".ml"));
        fclose($fh);
    }

    $extra_css = false;
    if (file_exists($content_dir."/".$tgt_content.".css")){
      $fh = fopen($content_dir."/".$tgt_content.".css", "r");
      $extra_css = fread($fh, filesize($content_dir."/".$tgt_content.".css"));
      fclose($fh);
    }
    if ($special_css_file != $tgt_content && file_exists($content_dir."/".$special_css_file.".css")){
        $fh = fopen($content_dir."/".$special_css_file.".css","r");
        if ($extra_css == false){
          $extra_css = "";
        }
        $extra_css .= fread($fh, filesize($content_dir."/".$special_css_file.".css"));
        fclose($fh);
    }
    //minify the CSS
    // $extra_css = preg_replace(
    //   array('/\s*(\w)\s*{\s*/','/\s*(\S*:)(\s*)([^;]*)(\s|\n)*;(\n|\s)*/','/\n/','/\s*}\s*/'), array('$1{ ','$1$3;',"",'} '),
    //   $extra_css
    // );
    /*$extra_css = preg_replace(
      '/(\/\*).*(\*\/)/',
      '',
      $extra_css
    );*/
    $js_files = $json["js_files"] ?: array();
    if (!is_array($json["images"])){
      $json["images"] = [];
    }
    //if a blog entry, then add a standard blog JS file and sidebar image
    if ($blog){
      $js_files[] = "/js/blog.js";
    }

  $details = array(
        "canonical_url" => $json["canonical_url"] ?: ($blog ? $BASE_BLOG_URL : $BASE_URL).($tgt_content != "index" ? $tgt_content : ""),
        "description" => $json["description"],
        "post_graphic_url" => $json["post_graphic_url"],
        "bodyclass" => $blog ? "blogpost" : $json["body_class"],
        "title" => $json["title"],
        "subtitle" => $json["subtitle"],
        "read_description" => $json["read_description"],
        "header_background_url" => $json["header_background_url"] ?: $json["post_graphic_url"],
        "header_background_url_lo" => $json["header_background_url_lo"],
        "header_background_position" => $json["header_background_position"] ?: "center",
        "extra_css" => $extra_css,
        "pagecontent" => $page_ml,
        "blogroll" => $blogroll,
        "twitter_meta_tags" => $json["twitter_meta_tags"],
        "additionalLoadFullJS" => $json["additionalLoadFullJS"] ?: false,
        "js_files" => $js_files,
        "imgarr" => json_encode($json["images"]) && ($json["images"] != null) ? json_encode($json["images"]) : "[]",
        "last_created" => time(),
        // "suggested_class" => $suggested_class,
        "breadcrumbs" => $json["breadcrumbs"],
        "factoid_title" => $json["factoid_title"],
        "factoid_txt" => $json["factoid_txt"],
        "blogvideo" => $json["blogvideo"],
        "publish_info" => $json["publish_info"],
        "tag" => $json["tag"]
        //"next_prev" => handle_inner_pagination($next, $prev, $next_title, $prev_title, $blog),
        //share_button" => share_button($blog, $BASE_BLOG_URL.$tgt_content, $json, $is_features, $BASE_URL)
    );
}
print_page($details);
