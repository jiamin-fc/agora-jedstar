<?php
function print_page($details) {
  global $blogroll;
    ?><!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png"> -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/android-chrome-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512"  href="/android-chrome-512x512.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <!-- <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png"> -->
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/mstile-150x150.png">
    <meta name="theme-color" content="#ffffff">

    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@jedstar" />

    <!-- meta name="google-signin-client_id" content= <?php /*echo CLIENT_ID*/ ?> -->

    <meta name="description" content="<?php echo $details["description"]; ?>" />
    <meta name="twitter:description" content="<?php echo $details["description"]; ?>" />
    <meta property="og:description" content="<?php echo $details["description"]; ?>">
    <?php
    if (is_array($details["twitter_meta_tags"])){
      for ($i=0; $i < count($details["twitter_meta_tags"]); $i++){
        echo '<meta name="twitter:label'.($i+1).'" content="'.$details["twitter_meta_tags"][$i]["label"].'"/><meta name="twitter:data'.($i+1).'" content="'.$details["twitter_meta_tags"][$i]["data"].'"/>'."\n";
      }
    }
     ?>
    <meta name="twitter:image" content="<?php echo $details["post_graphic_url"]; ?>" />
    <meta property="og:image" content="<?php echo $details["post_graphic_url"]; ?>">

    <meta property="og:url" content="<?php echo $details["canonical_url"]; ?>">
    <link rel="canonical" href="<?php echo $details["canonical_url"]; ?>">

    <meta name="twitter:title" content="<?php echo $details["title"]; ?>" />
    <meta property="og:title" content="<?php echo $details["title"]; ?>">
    <?php if (is_countable($details["breadcrumbs"]) > 0){ ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": <?php echo json_encode($details["breadcrumbs"]); ?>
    }
    </script>
    <?php } ?>
    <title><?php echo $details["title"]; ?></title>
    <link rel="stylesheet" href="/css/home.css?a=20220501" />
    <?php if ($details["bodyclass"] == "blogpost" && !$details["blogindex"]){ ?>
      <link rel="stylesheet" href="/css/blogpost.css" />
    <?php } ?>
    <link rel="alternate" type="application/rss+xml" title="Silvervolt - Get your gaming top up for free &raquo; Feed" href="<?php echo PUBLIC_RSS_URL; ?>" />
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <?php if ($details["extra_css"] !== false){ ?>
        <style>
<?php echo $details["extra_css"]; ?>

        </style>
    <?php } ?>

</head>
<body class='<?php if ($details["bodyclass"]){ echo $details["bodyclass"]; } ?>'>
	<div class="fixed-head">
		<div class="trapezoid">
			<div class="trapezoid-left"></div>
			<div class="trapezoid-right"></div>
		</div>
		<div class="navigation-border"></div>
		<div class="navigation">
			<div class="logo">
				<a href="https://www.jedstar.com/"><img src="https://assets.jedstar.space/img/jedstar_logo_only.svg" alt="Jedstar logo"></a>
				<a href="/"><img src="img/starstaking_logo_transparent_horizontal_v01 1.png" alt="StarStaking logo"></a>
			</div>
			<div class="buttons">
				<a href="https://www.jedstar.com/about-us" target="_blank" class="capitalise"><div>About us</div></a>
				<a href="javascript:void(0)" class="dropdown capitalise"><div>Platforms</div></a>
				<!-- <button id="myagora" class="capitalise"><div>My Agora</div></button>
				<button id="mycredit" class="capitalise"><div>My Credit</div></button> -->
				<button id="connect_btn" class="capitalise"><div><img src="/img/agora/wallet.svg"> <span>Log in</span></div></button>
				<div id="connect_box"></div>
			</div>
			<div class="menu">
				<img src="https://assets.jedstar.space/img/menu.webp" alt="menu">
			</div>
		</div>
	</div>

<?php

if ($details["bodyclass"] == "blogpost" && !$details["blogindex"]){
  ?>

  <div class="section video">
		<h1 class="capitalise"><?php echo $details["title"]; ?></h1>
		<img src="<?php echo $details["header_background_url_lo"]; ?>" alt="<?php echo $details["title"]; ?>" class="upgradeimg" data-upgradeimg="<?php echo $details["header_background_url"]; ?>" >
	</div>
  <div class="section blog-content-container bg-black">
		<div class="blog-content-left">
			<div class="blog-info">
				<div class="blog-calendar capitalise">
					<img src="https://assets.jedstar.space/img/calendar grey.webp" alt="calendar"> <?php echo $details["publish_info"]; ?>
				</div>
				<div class="blog-tag capitalise">
					<img src="https://assets.jedstar.space/img/tag grey.webp" alt="tag"> <?php echo $details["tag"]; ?>
				</div>
			</div>
			<div class="blog-text">
        <?php echo $details["pagecontent"]; ?>
        <button class="back" onclick="history.back()">BACK</button>
      </div>
    </div>
    <div class="blog-content-right">
			<div class="blog-content-right-container">
				<div class="newsletter">
					<div class="news-line1">
						<h2 class="capitalise">Subscribe to our Newsletter</h2>
					</div>
					<div class="news-input">
						<input type="text" placeholder="Email">  <img id="joinnewsletter" src="https://assets.jedstar.space/img/gradient_right_arrow.webp" />
					</div>
				</div>
				<div class="download" onclick="window.location='https://signup.silvervolt.app';" style="cursor:pointer;">
					<div class="download-line1">
						<h2 class="capitalise">Charge. <br>Generate. <br>Win.</h2>
					</div>
					<div class="download-lower">
						<div class="download-line2">
							<h2>Download <span class="capitalise">SILVERVOLT </span>today.</h2>
						</div>
						<div class="download-line3">
							Top-up your games for FREE
						</div>
						<div class="download-links">
							<a href="/instructions"><img loading="lazy" src="https://assets.jedstar.space/img/frame_107.webp"></a>
						</div>
						<div class="download-button">
							<button class="capitalise">download now</button>
						</div>
					</div>
				</div>
				<div class="share">
					<h2 class="capitalise">share on</h2>
					<div class="social-links">
						<div class="share-icon">
							<a target="_blank" rel="nofollow" href="https://twitter.com/intent/tweet?url=<?php echo urlencode($details["canonical_url"]); ?>&text=<?php echo urlencode($details["title"]); ?>"><img loading="lazy" src="https://assets.jedstar.space/img/twitter.svg" alt="Twitter"></a>
						</div>
						<div class="share-icon">
							<a target="_blank" rel="nofollow" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($details["canonical_url"]); ?>" ?><img loading="lazy" src="https://assets.jedstar.space/img/fb.svg" alt="Facebook"></a>
						</div>
						<div class="share-icon">
							<a target="_blank" rel="nofollow" href="http://www.reddit.com/submit?url=<?php echo urlencode($details["canonical_url"]); ?>&title=<?php echo urlencode($details["title"]); ?>"><img loading="lazy" src="https://assets.jedstar.space/img/reddit.svg" alt="Reddit"></a>
						</div>
						<div class="share-icon">
							<a target="_blank" rel="nofollow" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($details["canonical_url"]); ?>"><img src="https://assets.jedstar.space/img/linkedin.svg" alt="LinkedIn"></a>
						</div>
					</div>
				</div>
			</div>
    </div>
  </div>

  <div class="section other-news bg-black">
    <div class="title">
      <h2 class="capitalise">in other news</h2>
    </div>
    <div class="news-container">
      <?php echo $blogroll; ?>
    </div>
  </div>
  <div id="recaptcha" class="g-recaptcha" data-sitekey="6Ld8G2oUAAAAANiSEV6cKf8Qt7U1Jn6pQ0qMQkXP" data-callback="recaptchaResult" data-size="invisible"></div>
  <?php
}else if ($details["bodyclass"] == "blogpost" && $details["blogindex"]){
  ?>

  	<div class="section blog">
  		<div class="title">
  			<h1>PRESS & NEWS</h1>
  		</div>
  		<div class="content">
  			<div class="blog-image">
  				<img src="<?php echo $details["first_post"]["img_lo"]; ?>" alt="<?php ?>" class="upgradeimg" data-upgradeimg="<?php echo $details["first_post"]["img_hi"]; ?>">
  			</div>
  			<div class="blog-text">
  				<div class="calendar">
  					<img src="/img/lossy/calendar grey.webp" alt="calendar" class="upgradeimg" data-upgradeimg="/img/calendar grey.webp"><span><?php echo $details["first_post"]["publish_date"]; ?></span>
  				</div>
  				<div class="tag">
  					<img src="/img/lossy/tag grey.webp" alt="TAG" class="upgradeimg" data-upgradeimg="/img/tag grey.webp"><span><?php echo $details["first_post"]["tag"]; ?></span>
  				</div>
  				<div class="text-title">
  					<h2 class="capitalise"><?php echo $details["first_post"]["title"]; ?></h2>
  				</div>
  				<div class="text-content">
  					<p><?php echo $details["first_post"]["blurb"]; ?></p>
  				</div>
  				<div class="read-more">
  					<a href="<?php echo $details["first_post"]["stub"]; ?>" class="capitalise readmore">Read More
							<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="26" height="27" viewBox="0 0 26 27" fill="none">
							<g clip-path="url(#clip0_451_1386)">
							<mask id="mask0_451_1386" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="4" width="25" height="19">
							<rect y="4.64587" width="25" height="17.7083" fill="url(#pattern0)"/>
							</mask>
							<g mask="url(#mask0_451_1386)">
							<rect width="25" height="27" fill="url(#paint0_linear_451_1386)"/>
							</g>
							</g>
							<defs>
							<pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
							<use xlink:href="#image0_451_1386" transform="translate(0 -0.205882) scale(0.00195312 0.00275735)"/>
							</pattern>
							<linearGradient id="paint0_linear_451_1386" x1="0" y1="3.91935" x2="25" y2="3.91935" gradientUnits="userSpaceOnUse">
							<stop stop-color="#37A4F8"/>
							<stop offset="1" stop-color="#DB1EBF"/>
							</linearGradient>
							<clipPath id="clip0_451_1386">
							<rect width="26" height="27" fill="white"/>
							</clipPath>
							<image id="image0_451_1386" width="512" height="512" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAAIACAMAAADDpiTIAAAAA3NCSVQICAjb4U/gAAAACXBIWXMAADkrAAA5KwHnPyCmAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAtxQTFRF////AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAjTcXJwAAAPN0Uk5TAAECAwQFBgcICQoLDA0ODxAREhMUFRYXGBkaGxwdHh8gISIjJCUnKCkqKywtLi8wMTIzNDU2Nzg5Oz0+P0BBQkNERUZHSElKS0xNTk9QUVJTVFZXWFlaW1xdXl9gYWJjZGVmZ2lqa2xtbm9xcnN0dXZ3eHl7fH1+f4CBgoOEhYaHiIqLjI2Oj5CRkpOUlZaXmJmam5ydnp+goaKjpKWmqKqrrK2ur7CxsrO0tba3uLm6u7y9vsDBwsPExcbHyMnKy8zNzs/Q0dLT1dbX2Nna29zd3t/g4eLj5OXm5+jp6uvs7e7v8PHy8/T19vf4+fr7/P3+dmMvuQAACfNJREFUeNrt3Yt/1XUdx/HfNi5jhFyGF8CRiGgMmU6FSLmUoEBYZqCZAQkzLO0iiCGCoqWJGtPAHKXgPYXEC6VJJLoCgpAJbHIJGJdxcTfO2T7/QI9Hjx490nb5nXO28/v9vu/X8094f96M3+ds5/PzPAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAOC/cs4adMnw3uQgKONLv/jwaMz+7cj6RcMyiERI5qjH9tmn7XvwbHIRkfWjg9aEU09dQDYKCj6wZsQWdCIe12UvilnzNheSkNtGbrcWnZpFRi6bGLfWLM8mJmd9sdpaV5pHUI46/7D5UTmaqJzUp9z8id1OWA46bZP5tqILeTmnxBKw8RwCc0xhYyIFsCNXEplb3rHExO8gM5eMsoQ915XY3LEi8QLYloHk5oqetUkUwKquJjlHFFlSGubyhyJuWGlJeqkb4bmgItkC2D/OJ73o62vJOz6J/CJvdAoFsMb5PAhE3XWWklXdiTDaZqZWACsbTIaRdmeKBbCT1xJilM23lC3KJEbpAtianuQoXQDbeSFBShfAqieTpHQBzH6eRZbSBbC1uYQpXQCruJg0pQtgNTcSp3QBzBZ3IFDpAtjbZ5CodAFsz2VEKl0Aq5tGptIFMFvSkVSlC2DrziJW6QLYvhHkKl0Aq59JsNIFMFvGVTHtAth7/chWugB24HLClS6AnbqVdKULwF05+QJYaX8Cli4Ad+XUC8BdOfUCmK3IIWTpAtjGAaQsXQDuyqkXgLty6gXgrpx8AWzLeUQtXQDuyqkXgLty6gUwe5m7ctoF4K6cegG4K6deAGu8hwcB6QJwV06+ANyVUy+AnfwGqUsXgLty8gWw17krp10A2zmU5ENiYSAFsOopRB8OxRYQ7sqFw/NBFYC7cuHwdmAF4K5cKGwNrgDclQuBjGMWpEe4KxewAgsWd+UCdnvABeCuXMBWBV0Aq5vOFIKTddyCV8xducBcZWHAXbnA/CEUBeCuXFAKLSTqixhGEFZaaCzrzDjS7oJYeArAXbn06/C+hQl35dLtbgsX7sql16UxCxvuyqVR348sfLgrlzZ5YZw/d+XSZkC5hVOcu3LpMHy3hRZ35dpd9+IGC7FN3JVrVxlT9lu4cVeuHQ25v8JCLz6bQbW93IIJRfdttmho47tyBXNKXivdU2+IjLa7K9fxa8v2kmf0VI1vm/HPKCfLaGq4K/VzMh2KKggyulK+K9fnXUKMtG2p3ZW7Yj8RRlxKd+VuixFg5KVwV24G6Tkh2bty4/j374jk7soNPUFyrkjmrlz2LnJzSOJ35eYQmlMSvSt3xnEyc0uCd+UeJzHXJHRXrm+cwNzzoP+7crNIy0Vre/stwFrCcpLfu3K9+AzIUTXf9lWAm0jKWb7uyj1MTu7yc1fuWWJymI+7cu+QkstavytXRkhua+2u3CdE5Lh1fVosAAE5758jKIC2Fu/KEY+CFu7KEY6E5u/KkY2GZu/KEY2I5u7KkYyMpu/KkYuOJu/KEYuQQ6MpgLb4DymAuP+7K0ckYj57V45E1BwZSwG0NcymAOL+964caSjafDoF0Lb1TAqgbVsfCqBtez8KIN6AHAqg7QEKoO1UPgXQ9qcMCqBtKgXQtjuDAmgbQQG0PUQBtH1MAcQNowDa5lEAbUspgLZXKYC2DRRAfQ0gA2m1FEDbAQqgbT0F0PYMBdC2gAJom0oBtOVTAGml/DZQ260UQFp9Lwog7QX+KFRa4xUUQFoxXwyRtrMrBZD+D2Ak3w2Utpivh0t7sTMFULY0iwshyu7jSJT0899tnIlTduwaDkUq23wel0KVPZ3DrWBh9d/jXLyyPcN5YYSytafzxhDl7W9RFu8MUt7+JvHaOOntbyAvjlT2my68OVR5+7uFl0cr2z2M18cre6t3s/P3qojH+e3v3szm5+9tJSDHVX3Va8mbJOS2TQNbnL+3nIictrxLy/P3FpKRy9tfkdeaUaTkro8va3X+XlYlObnqzd6eD08SlKPb38JMP/P3xhOVm9vfRM+fTnsJy0Ebz/X8mk5a7inp4nv+XuYW8nJM3UwvERNITG/7+5Q1ZOaSN3ITnL/XawepubP9Lcj0EpZ/guAccXSCl4xrGonOCX8b4CXn5hjhOeCpbC9ZY44SX+S3vxleCgZtJ8Foq7jUS0mPJ+KEGGGv53qpGryaGCO7/d2T6bWBMW/xMBjN7W+810Zyp62qI0+Z7a9JnyucdMu9Jc9Ke+PvlRH6aOTX2R7aXIf+01dH4odh3c0Mq710m/xu6Odffglzak/frAj3/Nf0YkbtK3tufYi3v/mZTKjdjTke1vkfuZrppMNF+8M5/7+ew2zS49zyMM7/Sba/tCkM33NA7XcZSxr9IHTbXyFDSatXwjX/19j+0iw3TKtAw90ZTCTdHgjR9ncV40i/M2vDMv9Str9ALAnJ/Jd1ZhaBGBiO7W86kwhKGP5sdtfFzCEwxcHP//c9GUNwrg18+5vH9hekng3Bzv/wOGYQrF2Bzv+DzzOBgG0Icv5L2f4CF+AXZ2qnEX/wStj+tP0sqPmvZvsLhfkBbX8/ZftTLsDhsSSvXID3+xO8cgF+xfanXICaqaSuXICdFxG6cgFW9SBz4QI03MX2p1yAQ1cSuHIB2P60C/BEJ+IWLkDNdwhbuQA7CshauQCvsv0pF6BhLtufcgEOfYWclQuwIY+YlQvwONufcgFqbiJj5QLsGErEygV4pTsJCxcgfifbn3IBKr9MvMoFeO9s0lUuwBK2P+UCVN9ItMoF+IjtT7oAv2P7Uy5AfA7bn3IB2P60C/AXtj/pAvyS7U+5ANXfIlHlApRdSKDKBXj5NPIULkB8NtufcgEOjiFM5QKs70eWygV4rCNRCheg+gaCVC5A2RByVC7AS2x/ETYv5e3vDkKMslTfH3twNBlG2g1sf9rGpTT/R9n+oi4/hfF/cj35RV7msaTnvz2f+BywJtn5v8j254S5SW5/PyE6NwxqTGb+B0aRnCv+mMT8/9yX3JwxJfH5P8L255COZYluf1MIzSkTE5v/h2x/0pvgC90IzDV5e32PP/Zj4nLQkCq/299IwnLS5bW+5r+O7c9VX4/7mP9itj93FbU6/pOTScll11e2PP9tg8nIbbm/bWH8jUu6kpDzxu5qbv67ufgvIeehmiaXv9l8+CNTgetWnvjM+DfOyiYXJZ0nluz9zw+CxorV3+dlj5KyeuR9YVghD34AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACB+heJJhfe6MQwnAAAAABJRU5ErkJggg=="/>
							</defs>
							</svg>
						</a>
  				</div>
  			</div>
  		</div>
  	</div>

  	<div class="section newsletter bg-black">
  		<div class="news-line1">
  			<h2 class="capitalise">Subscribe to our Newsletter</h2>
  		</div>
  		<div class="news-line2">
  			<p>To keep up to date with us</p>
  		</div>
  		<div class="news-input">
  			<input type="text" placeholder="Email" id="emailaddress"> <img id="joinnewsletter" src="https://assets.jedstar.space/img/gradient_right_arrow.webp" />
  		</div>
  	</div>

  	<div class="section bloglist-container bg-black">
      <?php echo $details["pagecontent"]; ?>
    </div>
    <div id="recaptcha" class="g-recaptcha" data-sitekey="6Ld8G2oUAAAAANiSEV6cKf8Qt7U1Jn6pQ0qMQkXP" data-callback="recaptchaResult" data-size="invisible"></div>
    <?php
    //TODO blog pagination
    /*<div class="section bloglist-loadmore bg-black">
      <button class="load-more">OLDER</button>
    </div>*/ ?>

  <?php
}else{
  echo $details["pagecontent"];
}

?>

	<div class="footer">
		<div class="footer-links">
			<a href="https://www.jedstar.com/about-us" class="capitalise"><div>About us</div></a>
			<a href="javascript:void(0)" class="dropdown capitalise"><div>Platforms</div></a>
		</div>
		<div class="footer-copyright">
			<p class="capitalise">Jedstar inc. ©2022</p>
		</div>
		<div class="social-strip">
			<div class="social-icon twitter">
				<a href="http://go.jedstar.com/twitter" aria-label="twitter">
				<img src="https://assets.jedstar.space/img/Twitter_footer.webp" alt="twitter">
				</a>
			</div>
			<div class="social-icon telegram">
				<a href="http://go.jedstar.com/telegram" aria-label="telegram">
				<img src="https://assets.jedstar.space/img/Telegram_footer.webp" alt="telegram">
				</a>
			</div>
			<div class="social-icon discord">
				<a href="http://go.jedstar.com/discord" aria-label="discord">
				<img src="https://assets.jedstar.space/img/Discord_footer.webp" alt="discord">
				</a>
			</div>
			<div class="social-icon youtube">
				<a href="http://go.jedstar.com/youtube" aria-label="youtube">
				<img src="https://assets.jedstar.space/img/youtube_footer.webp" alt="youtube">
				</a>
			</div>
			<div class="social-icon instagram">
				<a href="http://go.jedstar.com/instagram" aria-label="instagram">
				<img src="https://assets.jedstar.space/img/Instagram_footer.webp" alt="instagram">
				</a>
			</div>
			<div class="social-icon facebook">
				<a href="http://go.jedstar.com/facebook" aria-label="facebook">
				<img src="https://assets.jedstar.space/img/facebook_footer.webp" alt="facebook">
				</a>
			</div>
			<div class="social-icon medium">
				<a href="http://go.jedstar.com/medium" aria-label="reddit">
				<img src="https://assets.jedstar.space/img/MEDIUM_footer.webp" alt="reddit">
				</a>
			</div>
			<div class="social-icon reddit">
				<a href="http://go.jedstar.com/reddit" aria-label="reddit">
				<img src="https://assets.jedstar.space/img/reddit_footer.webp" alt="reddit">
				</a>
			</div>
      <div class="social-icon tiktok">
				<a href="https://www.tiktok.com/@jedstargaming" aria-label="tiktok">
				<img src="https://assets.jedstar.space/img/tiktok_footer.svg" alt="tiktok">
				</a>
			</div>
		</div>
	</div>

  <div id="scrollpx">&nbsp;</div>
  <div class="modal">
    <div class="modal-x">x</div>
    <div class="modal-content platform-dropdown">
      <div id = "silvervolt" class="platform silvervolt">
        <div class="logo">
          <img src="https://assets.jedstar.space/img/SILVERVOLT_LOGO.webp">
        </div>
        <div class="content">
          <p>Charge-to-Earn (C2E) auction platform, where Generators would charge their phones to generate VOLTS through our app, which can then be used to bid for, and win exclusive gaming prizes.</p>
        </div>
        <div class="title">
          <h2 class="capitalise">Charge. Generate. Win.</h2>
        </div>
        <div class="button">
          <button class="capitalise" id = "launchButton"><div>Launch Now</div></button>
        </div>
      </div>
      <div id = "agora" class="platform agora">
        <div class="logo">
          <img src="https://assets.jedstar.space/img/agora_logo_no-shadow.svg">
        </div>
        <div class="content">
          <p>AGORA Marketplace will host the most exclusive art from AAA artists and gaming assets. The marketplace will allow asset owners to sell and trade their digital collectables, fueled by our $KRED Token.</p>
        </div>
        <div class="title">
          <h2 class="capitalise">Exclusive. Curated. Limited Edition Collectibles.</h2>
        </div>
        <div class="button">
          <button id = "agora-button" class="capitalise"><div>Now Live</div></button>
        </div>
      </div>
      <div id = "starstaking" class="platform starstaking">
				<div class="logo">
					<img src="https://dev01agora.jedstar.app/img/agora/starstaking_logo_transparent_horizontal_v01.png">
				</div>
				<div class="content">
					<p>STARSTAKING is our next-generation DeFi platform providing innovative and unique reward programs. With its deflationary mechanisms and reward programs, STARSTAKING is poised to become a key player in the DeFi field. Season 1 now open.</p>
				</div>
				<div class="title">
					<h2 class="capitalise">Stake. Earn. Win.</h2>
				</div>
				<div class="button">
					<button id = "starstaking-button" class="capitalise"><div>Launch Now</div></button>
				</div>
			</div>
      <div id = "stardome" class="platform stardome">
				<div class="logo">
					<img src="https://jedstar.com/wp-content/uploads/2022/10/stardome_3D_whiteLogo_transparent-1.png">
				</div>
				<div class="content">
					<p>Play-to-Earn by challenging yourself or dueling others in any of our games.</p>
				</div>
				<div class="title">
					<h2 class="capitalise">Play. Earn. Challenge.</h2>
				</div>
				<div class="button">
					<button id = "stardome-button" class="capitalise"><div>Launch Now</div></button>
				</div>
			</div>
    </div>

      <div class="modal-content respec">
        <div class="title">
          <h2 class="capitalise collection"><span class="color-blue">Respec</span> requirements & rules</h2>
        </div>
        <div class="rainbow-border"></div>
        <div class="respec-content">
          <p>The RESPEC process is simple: Every two weeks you can spend $30.00 USD to re-randomize your NFT's Attribute points. You will randomly receive new values from 0-100 for each available Attribute (1 for a common NFT, 5 for a Secret-Rare NFT etc.).</p>
          <p>This process is behind a time-wall in order to ensure that JEDSTAR players aren't spamming the minting process. If you're a JED HODLer with more than 1000 JED in your wallet (and connected to AGORA) then the price and timing is discounted. JED HODLers can RESPEC their NFT's every 1 week for only $20.00 USD.</p>
          <p>All payments for the RESPEC process are made through agora using KRED, USDC, MATIC or BUSD.</p>
          <p>1. Go to MY AGORA</p>
          <p>2. Choose the NFT you would like to RESPEC</p>
          <p>3. CLICK RESPEC</p>
          <p>4. Confirm Payment</p>
          <p>5. Your NFT will have it’s new ATTRIBUTES allocated</p>
          <!-- <p>Need a description and instructions</p>
          <p>1. Connect Your Wallet (it must hold at least 5000 JED).</p>
          <p>2. Purchase AGORA digital collectibles with deposited credit. We accept <b>$KRED</b>, <b>USDC</b>, <b>BNB</b> and <b>BUSD</b>.</p> -->
        </div>
        <div class="respec-button">
          <button class="capitalise"><div>Deposit Credit</div></button>
        </div>
      </div>

      <div class="modal-content buy">
        <div class="title">
          <h2 class="capitalise collection"><span class="color-blue">How</span> to buy</h2>
        </div>
        <div class="rainbow-border"></div>
        <div class="buy-content">
          <p>Once you have connected your wallet to AGORA you will be able to load your account with credit. Once you have the appropriate amount of funds or crypto, you will be able to click the 'BUY NOW' button below the NFT of your choice in order to process the purchase. Once purchased, your GENESIS NFT will Mint with random Attributes, which provide bonuses and utility in the JEDSTAR universe of Apps and Games.</p>
          <div class = "numberedList">
            <p>1. Connect your wallet</p>
            <p>2. Ensure you have the appropriate amount of KRED, USDC, USDT or BUSD</p>
            <p>3. Click 'BUY NOW' on the GENESIS NFT of your choice</p>
            <p>4. Your NFT will be MINTED</p>
            <p>5. You can then view your GENESIS NFT on 'MY AGORA' and OpenSea</p>

          </div>
          <br>
          <!-- <p>1. Connect Your Wallet (it must hold at least 5000 JED).</p>
          <img src="img/agora/arrow3.svg">
          <p>2. Purchase AGORA digital collectibles with deposited credit. We accept <b>$KRED</b>, <b>USDC</b>, <b>BNB</b> and <b>BUSD</b>.</p> -->
        </div>
        <div class="buy-button">
          <button class="deposit_button capitalise"><div>Deposit Credit</div></button>
        </div>
      </div>

      <div class="modal-content mycredit">
        <div class="mycredit-head">
          <div class="title">
            <h2 class="capitalise collection"><span class="color-blue">My</span> credit</h2>
          </div>
          <div class="mycredit-button">
            <button id="btn_refresh_balances" class="capitalise"><div>Refresh Balances</div></button>

            <button class="deposit_button capitalise"><div>Deposit Credit</div></button>
          </div>
        </div>
        <div class="rainbow-border"></div>
        <div id="walletcreditentries" class="mycredit-content"></div>
        <div id="walletcreditnone" class="mycredit-none hidden">
          <div class="mycredit-none-content">
            <p class="capitalise">You currently have no credit</p>
            <p>AGORA digital collectibles can be purchased with deposited credit.<br>We accept $KRED, USDC, USDT and BUSD.</p>
          </div>
          <div>
            <button class="deposit_button capitalise"><div>Deposit Credit</div></button>
          </div>
        </div>


        <!-- <div class="mytransaction-head">
          <div class="title">
            <h2 class="capitalise collection"><span class="color-blue">My</span> transactions</h2>
          </div>
        </div>
        <div class="rainbow-border"></div>
        <div id="walletcreditentries" class="mycredit-content"></div>
        <div id="walletcreditnone">
          <div class="mycredit-none-content">
            <p class="capitalise">No transactions</p>
          </div>
        </div> -->
      </div>

      <div class="modal-content depositcredit">
        <div class="depositcredit-head">
          <div class="title">
            <h2 class="capitalise collection"><span class="color-blue">Deposit</span> credit</h2>
          </div>
          <div class="depositcredit-button">
            <button class="capitalise"><div>Deposit Credit</div></button>
          </div>
        </div>
        <div class="rainbow-border"></div>
        <div id="wallettxlist" class="depositcredit-content hidden"></div>
        <div class="depositcredit-instructions">
          <div class="instruction-image"><img src='/img/qrcode.png' alt='qr code'></div>
          <div class="instruction-content">
            <p>To purchase digital collectables on AGORA you first need to deposit funds into the escrow wallet.</p>
            <p>Once transferred your credit balance will update in the 'My AGORA' section within 1-5 minutes. You can then use your balance to purchase any of the NFTs available on AGORA.</p>
            <p>Accepted currencies: <b>$KRED</b>, <b>USDT</b> & <b>USDC</b>.</p>
            <p>Supported networks: Ethereum, Polygon, Binance Smart Chain</p>
            <p>To deposit funds into your account send one of the accepted currencies to wallet address: 0x929Ba3345536503dBCE86187c3A050e635B62a1e</p>
            <p class="important capitalise">IMPORTANT: You must transfer the currency from the same wallet you log in with. DO NOT TRANSFER IN FROM AN EXCHANGE OR HOSTED WALLET.</p>
          </div>
        </div>
      </div>

      <div class="modal-content utilities">
        <div class="mycredit-head">
          <div class="title">
            <h2 class="capitalise collection"><span class="color-blue">Genesis</span> mint utilities</h2>
          </div>
        </div>
        <div class="rainbow-border"></div>

        <div class="nft-detail-silvervolt">
          <div class="nft-detail-logo"><img src="https://assets.jedstar.space/img/SILVERVOLT_LOGO.webp"></div>
          <p>This NFT offers various in app Boosts and Upgrades to our SILVERVOLT gaming product. Sign up and top off your favorite games for FREE at <a href="">www.silvervolt.app</a></p>
          <div class="nft-detail-boosts">
            <div class="boost">
              <div class="boost-check"><img src="img/agora/check.svg"></div>
              <div class="boost-title"><p class="capitalise"><span id="boost-bid"></span> bid boost</p></div>
              <div class="boost-subtitle"><p class="label">Gives you additional weekly auction bids. Up to <span id = "numBid">5</span> / week for a <span class = "attributes-rarities">Secret-Rare</span>.</p></div>
              <div class="boost-stat"><p class="capitalise">Serge</p></div>
            </div>

            <div class="boost">
              <div class="boost-check"><img src="img/agora/check.svg"></div>
              <div class="boost-title"><p class="capitalise"><span id="boost-mining"></span> mining boost</p></div>
              <div class="boost-subtitle"><p class="label">Boosts your VOLT mining power. Up to <span id = "numMining">10</span>% for a <span class = "attributes-rarities">Secret-Rare</span>.</p></div>
              <div class="boost-stat"><p class="capitalise">Might</p></div>
            </div>

            <div class="boost">
              <div class="boost-check"><img src="img/agora/check.svg"></div>
              <div class="boost-title"><p class="capitalise">Tier boost</p></div>
              <div class="boost-subtitle"><p class="label">Instant access to <span id = "tierType">DIAMOND</span>-Tier Auctions for a <span class = "attributes-rarities">Secret-Rare</span>.</p></div>
            </div>

            <div class="boost">
              <div class="boost-check"><img src="img/agora/check.svg"></div>
              <div class="boost-title"><p class="capitalise"><span id="boost-referral"></span> Referral boost</p></div>
              <div class="boost-subtitle"><p class="label">Boosts your referral VOLT bonus. Up to <span id = "numReferral">4</span>% extra for a <span class = "attributes-rarities">Secret-Rare</span>.</p></div>
              <div class="boost-stat"><p class="capitalise">Resilience</p></div>
            </div>

            <div class="boost">
              <div class="boost-check"><img src="img/agora/check.svg"></div>
              <div class="boost-title"><p class="capitalise"><span id="boost-wheel"></span> Wheel Spin Boost</p></div>
              <div class="boost-subtitle"><p class="label">Cheaper Wheel Spins! (Coming Soon) Up to <span id = "numWheel">80</span>% off wheel spins for a <span class = "attributes-rarities">Secret-Rare</span>.</p></div>
              <div class="boost-stat"><p class="capitalise">Wit</p></div>
            </div>

            <div class="boost">
              <div class="boost-check"><img src="img/agora/check.svg"></div>
              <div class="boost-title"><p class="capitalise"><span id="boost-lottery"></span> Lottery Ticket Boost</p></div>
              <div class="boost-subtitle"><p class="label">Cheaper Lottery Tickets! Up to <span id = "numLottery">80</span>% off Lottery tickets for a <span class = "attributes-rarities">Secret-Rare</span>.</p></div>
              <div class="boost-stat"><p class="capitalise">Luck</p></div>
            </div>

            <div class="boost">
              <div class="boost-check"><img src="img/agora/check.svg"></div>
              <div class="boost-title"><p class="capitalise">High roller access</p></div>
              <div class="boost-subtitle"><p class="label">Coming Soon.</p></div>
            </div>

            <div class="boost">
              <div class="boost-check"><img src="img/agora/check.svg"></div>
              <div class="boost-title"><p class="capitalise">Custom name</p></div>
              <div class="boost-subtitle"><p class="label">Update your handle to be a custom name! Ultra-Rare NFTs and above.</p></div>
            </div>

            <div class="boost">
              <div class="boost-check"><img src="img/agora/check.svg"></div>
              <div class="boost-title"><p class="capitalise">Custom avatar</p></div>
              <div class="boost-subtitle"><p class="label">Select your own custom Avatar! Rare NFTs and above.</p></div>
            </div>

          </div>
        </div>

        <div class="nft-detail-extras">
          <div class="nft-detail-extra">
            <div class="nft-detail-logo"><img src="img/agora/starstaking_logo_transparent_horizontal_v01.png"></div>
            <p>JEDSTAR Gaming’s official staking platform</p>
            <p class="capitalise label">Coming soon</p>
          </div>

          <div class="nft-detail-extra">
            <div class="nft-detail-logo"><img src="img/agora/nightclaws.png"></div>
            <p>Arcade RPG Game (Mobile)</p>
            <p class="capitalise label">Coming soon</p>
          </div>

          <div class="nft-detail-extra">
            <div class="nft-detail-logo"><img src="img/agora/ascension.png"></div>
            <p>Real-Time Strategy Game (Mobile)</p>
            <p class="capitalise label">Coming soon</p>
          </div>

          <div class="nft-detail-extra">
            <div class="nft-detail-logo"><img src="img/agora/aethernova.png"></div>
            <p>Collectible Card Game (Mobile/PC)</p>
            <p class="capitalise label">Coming soon</p>
          </div>
        </div>

      </div>

      <div id="walletmodal" class="modal-content yourwallet"></div>

	</div>
	<div id="blockingmodal" class="hidden">
		<img src="/img/agora/silvervolt_processing_anim_v07.gif" alt="Loading" />
		<h3 id="blockingmessage">Loading</h3>
	</div>
<script>
let fontcss = "@font-face {font-family: 'Josefin Sans'; font-style: normal; font-weight: 400; font-display: swap; src: url(https://fonts.gstatic.com/s/josefinsans/v24/Qw3PZQNVED7rKGKxtqIqX5E-AVSJrOCfjY46_DjQbMhhLzTs.woff2) format('woff2'); unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF,U+1F181E;}@font-face { font-family: 'Josefin Sans'; font-style: normal; font-weight: 400; font-display: swap; src: url(https://fonts.gstatic.com/s/josefinsans/v24/Qw3PZQNVED7rKGKxtqIqX5E-AVSJrOCfjY46_DjQbMZhLw.woff2) format('woff2'); unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD,U+1F181E;}  @font-face { font-family: 'Lato'; font-style: normal; font-weight: 400; font-display: swap; src: url(https://fonts.gstatic.com/s/lato/v23/S6uyw4BMUTPHjxAwXjeu.woff2) format('woff2'); unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF,U+1F181E;}@font-face { font-family: 'Lato'; font-style: normal; font-weight: 400; font-display: swap; src: url(https://fonts.gstatic.com/s/lato/v23/S6uyw4BMUTPHjx4wXg.woff2) format('woff2'); unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD,U+1F181E;}"; let fontscript = document.createElement("style"); fontscript.setAttribute('type', 'text/css'); if ('textContent' in fontscript){ fontscript.textContent = fontcss; }else { fontscript.stylesheet.cssText = fontcss; } document.getElementsByTagName('head')[0].appendChild(fontscript);

    var loaded = 0;
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    var ajax = null, breadbox = {};
    var shield = function ( s, h, i, e, l, d ){ var g = document.createElement(e); g.src = s; if (typeof h=="function") g.onload=h; g.async="async"; document.getElementsByTagName(i)[l].appendChild(g);};
    var loadFull = function(ev){
        if (typeof localStorage == "object" && typeof localStorage["setItem"] == "function"){
          localStorage["setItem"]("presence", new Date().getTime());
        }
        if (loaded == 1){
            return;
        }
        loaded = 1;
        document.removeEventListener("touchstart", loadFull);
        document.removeEventListener("keydown", loadFull);
        document.removeEventListener("scroll", loadFull);
        document.removeEventListener("mousemove", loadFull);

        var crumbs = JSON.parse(localStorage.getItem("breadcrumbs")) || [];
        breadbox = {
          add: function(pageid){
            if (crumbs.length >= 5){
              crumbs.shift();
            }
            crumbs.push(pageid);
            breadbox.write();
          },
          get: function(){
            return crumbs;
          },
          write: function(){
            localStorage.setItem("breadcrumbs", JSON.stringify(crumbs));
          }
        };

        //===Template specific header/footer functions====
        var chainRead, servercomms;
        var body = document.querySelector("body"),
            modalCon = document.getElementsByClassName("modal-content"),
            dropdown = document.getElementsByClassName("dropdown"),
            cardBtn = document.getElementsByClassName("buynow"),
            currURL = window.location.href;

        document.querySelector(".menu").addEventListener("click", function(event){
          // console.log("hamburger clicked");
          // event.classList.toggle("menuOpen");
        });

        //close modals
        document.querySelector(".modal-x").addEventListener("click", function(){
          closeModal();
        });
        //also bind modal closure to ESC key
        body.addEventListener("keyup", function(e){
          if (e.key === "Escape" && body.classList.contains("modalOpen")){
            closeModal();
          }
        });
        //platform dropdown
        for(let x=0;x<dropdown.length;x++){
          dropdown[x].addEventListener("click", function(){
            body.classList.remove("menuOpen");
            modalPopup("platform-dropdown");
          });
        }
        // launch button
        document.getElementById("launchButton").addEventListener("click", function(){
          window.open("https://www.silvervolt.app/", '_blank').focus();
        });
        document.getElementById("agora-button").addEventListener("click", function(){
          window.open("https://agora.jedstar.com/", '_blank').focus();
        });
        document.getElementById("starstaking-button").addEventListener("click", function(){
          window.open("https://starstaking.jedstar.com/", '_blank').focus();
        });
        document.getElementById("stardome-button").addEventListener("click", function(){
          window.open("https://stardome.jedstar.com/", '_blank').focus();
        });

        // //my credit
        // document.getElementById("mycredit").addEventListener("click", function(){
        //     body.classList.remove("menuOpen");
        //     modalPopup("mycredit");
        // });
        //your wallet
        document.getElementById("connect_btn").addEventListener("click", function(){
            body.classList.remove("menuOpen");
            if (!body.classList.contains("modal-yourwallet")) modalPopup("yourwallet");
        });
        // //deposit credit
        // let depositButtons = document.getElementsByClassName('deposit_button');
        // for(let button of depositButtons){
        //   button.addEventListener('click', function(){
        //     let buy = document.querySelector(".modal .modal-content.buy"),
        //     wallet = document.querySelector(".modal .modal-content.mycredit"),
        //     depo = document.querySelector(".modal .modal-content.depositcredit");

        //     buy.style.display = "none";
        //     wallet.style.display = "none";
        //     depo.style.display = "block";
        //   })
        // }
        // //my agora
        // document.getElementById('myagora').addEventListener("click", function(){
        //   window.location = "/mine";
        // });
        // platforms
        // currURL = "starstaking.com";
        let plats = document.getElementsByClassName("platform");
        for (let plat of plats){plat.classList.remove("four");}
        if (currURL.includes("agora")){
          let plat = document.getElementById("agora");
          if (plat){plat.classList.add("hidden");}
        }else if (currURL.includes("stardome")){
          let plat = document.getElementById("stardome");
          if (plat){plat.classList.add("hidden");}
        }else if (currURL.includes("starstaking")){
          let plat = document.getElementById("starstaking");
          if (plat){plat.classList.add("hidden");}
        }else{
          for (let plat of plats){
            plat.classList.remove("hidden");
            plat.classList.add("four");
          }
        }
        window.modalPopup = function(page){
          if (page == "yourwallet"){
            if (body.classList.contains("connected")){
              //let the wallet manager take care of this interaction
              return;
            }else{
              body.classList.add("modal-"+page);
            }
          }
          if (page == "mycredit" && !body.classList.contains("connected")){
            //The user needs to connect before they can check their balance
            document.getElementById("connect_btn").click();
            return;
          }

          let p = document.querySelector(".modal .modal-content."+page);

          if(page == "platform-dropdown"){
            p.style.display = "flex";
          }else{
            p.style.display = "block";
          }
          body.classList.toggle("modalOpen");

        }
        window.closeModal = function(){
          //NOTE: The agora.js>walletui.on(wallet_connected) fn will augment the DOM to handle expected UI behaviours on different wallet states as well
          if (body.classList.contains("modalOpen")){
            body.classList.remove("modalOpen");
            setTimeout(
              function(){
                for(let x=0;x<modalCon.length; x++){
                  modalCon[x].style.display = "none";
                }
              }
              , 500);

              if (body.classList.contains("modal-yourwallet")){
                //trigger wallet modal close as well
                document.getElementById("connect_btn").click();
                body.classList.remove("modal-yourwallet");
              }
            }
          }
          window.blockingModal = function(show, msg){
            if (show){
              document.getElementById("blockingmodal").classList.remove("hidden");
              document.getElementById("blockingmessage").innerHTML = msg;
            }else{
              document.getElementById("blockingmodal").classList.add("hidden");
            }
          }
        //======End template specific functions=======

        //define a cross browser ajax function
        ajax = function(e,r){var t="";for(var n in e)e.hasOwnProperty(n)&&(t+=(""==t?"":"&")+n+"="+encodeURIComponent(e[n]));var o={api_url:"/api/",request_header:"application/x-www-form-urlencoded",json_return:!0,method:"POST"};if("object"==typeof r)for(n in r)r.hasOwnProperty(n)&&(o[n]=r[n]);return new Promise(((e,r)=>{var n=new XMLHttpRequest;n.open(o.method,o.api_url),n.setRequestHeader("Content-Type",o.request_header),n.onload=function(){if(200===n.status){var t=o.json_return?JSON.parse(n.responseText):n.responseText;e(t)}else r({status:"fail",resp:t})},n.send(t)}))};
				//Menu animation
        document.querySelector(".menu").addEventListener("click", function(){
      		document.querySelector("body").classList.toggle("menuOpen");
      	});

      	var dropdown = document.getElementsByClassName("dropdown");

      	for(let x=0;x<dropdown.length;x++){
      		dropdown[x].addEventListener("click", function(){
      			document.querySelector("body").classList.toggle("platformOpen");
      		});
      	}
      	/*document.querySelector(".platform-x").addEventListener("click", function(){
      		document.querySelector("body").classList.remove("platformOpen");
      	});*/


        var stacks = document.getElementsByClassName("stackedtext");
        for (var i=0; i < stacks.length; i++){
            stacks[i].className += " animated";
        }

        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', 'IDGOESHERE');
            fbq('track', 'PageView');

        gtag('js', new Date());
        gtag('config', 'G-EMWSEMG4SY');

        var imgarr = <?php echo $details["imgarr"]; ?>;
        if (typeof addnimgs != "undefined" && Array.isArray(addnimgs)){
          imgarr = imgarr.concat(addnimgs);
        }
        var mobileView = window.outerWidth < 500;
        for (var i = 0; i < imgarr.length; i++) {
            if (document.getElementById(imgarr[i].target) && !(mobileView && imgarr[i].desktoponly)){
              console.log("Applying to "+imgarr[i].target);
                if (imgarr[i].src) document.getElementById(imgarr[i].target).src = imgarr[i].src;
                if (imgarr[i].style) document.getElementById(imgarr[i].target).style = imgarr[i].style;
                document.getElementById(imgarr[i].target).classList.remove("animated-placeholder");
            }
        }
        var placeholders = document.getElementsByClassName("animated-placeholder");
        while (placeholders.length > 0){
          if (typeof placeholders[0].dataset.gfxtype == "string"){
            if (placeholders[0].dataset.gfxtype == "src"){
              placeholders[0].src = placeholders[0].dataset.gfxdata;
              placeholders[0].classList.add("fade-in");
            }else if (placeholders[0].dataset.gfxtype == "style"){
              let t_img = new Image();
              t_img.onload = function(){
                this.target.style = this.gfxdata;
                this.target.classList.remove("preloading-placeholder")
                this.target.classList.add("fade-in");
              };
              t_img.target = placeholders[0];
              t_img.gfxdata = placeholders[0].dataset.gfxdata
              t_img.src = placeholders[0].dataset.gfxsrc;
              placeholders[0].classList.add("preloading-placeholder");
            }

          }
          placeholders[0].classList.remove("animated-placeholder");
        }
        var upgradecandidates = document.getElementsByClassName("upgradeimg");
        while (upgradecandidates.length > 0){
          if (typeof upgradecandidates[0].dataset.upgradeimg == "string"){
            upgradecandidates[0].src = upgradecandidates[0].dataset.upgradeimg;
          }
          upgradecandidates[0].classList.remove("upgradeimg");
        }
        //upgrade the background image
        /*if (window.innerWidth >= 768){
          let bodystyle = document.getElementsByTagName("body")[0].style = 'background: url(https://assets.jedstar.space/img/background.webp) no-repeat; background-position: left-top; background-attachment: fixed; background-size: cover;';
        }*/
        /**************** FUNCTIONALITY EXTENSION ****************/
        Date.prototype.addDays=function(t){var e=new Date(this.valueOf());return e.setDate(e.getDate()+t),e},Date.prototype.addHours=function(t){var e=new Date(this.valueOf());return e.setHours(e.getHours()+t),e},Date.prototype.getDayName=function(){return["Sun","Mon","Tue","Wed","Thu","Fri","Sat"][this.getDay()]},Date.prototype.getMonthName=function(){return["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"][this.getMonth()]},Date.prototype.toHumanFriendlyFormat=function(){return this.getDayName()+" "+this.getDate()+" "+this.getMonthName()},Date.prototype.getHours12=function(){return 0==this.getHours()?"12am":this.getHours()<12?this.getHours()+"am":12==this.getHours()?"12pm":this.getHours()-12+"pm"},Date.prototype.getTime12=function(){var t=0==this.getHours()||12==this.getHours()?"12":this.getHours()<12?this.getHours():this.getHours()-12,e=0==this.getHours()?"am":this.getHours()<12?"am":"pm";return t+(this.getMinutes()<10?":0"+this.getMinutes():":"+this.getMinutes())+e},Date.prototype.getYMD=function(){return this.toISOString().substring(0,10)};
        Date.prototype.time_until = function(t){let d=t-this; return {days:Math.floor((((d/1000)/60)/60)/24),hours:Math.floor(((d/1000)/60)/60) % 24,mins:Math.floor((d/1000)/60) % 60,secs:Math.floor(d/1000) % 60}};
        /****************** END FUNCTIONALITY EXTENSION *************/
        <?php
        if (is_array($details["js_files"])){
            $details["js_files"][] = "https://www.googletagmanager.com/gtag/js?id=IDGOESHERE";
        }else{
            $details["js_files"] = array("https://www.googletagmanager.com/gtag/js?id=IDGOESHERE");
        }
        for ($i=0; $i < sizeof($details["js_files"]); $i++){
            ?>shield("<?php echo $details["js_files"][$i]; ?>", null, "head", "script", 0); <?php
        }
        ?>

        /*if (
          "IntersectionObserver" in window &&
          "IntersectionObserverEntry" in window &&
          "intersectionRatio" in window.IntersectionObserverEntry.prototype
        ) {
          var hdr = document.getElementById("header");
          let observer = new IntersectionObserver(entries => {
            if (entries[0].boundingClientRect.y < 0) {
              hdr.classList.add("shrink");
              console.log(entries[0].boundingClientRect);
            } else {
              hdr.classList.remove("shrink");
            }
          });
          observer.observe(document.querySelector("#scrollpx"));
        }*/
        <?php
        if ($details["additionalLoadFullJS"]){
            echo $details["additionalLoadFullJS"];
        }
        ?>
    };
    if (typeof localStorage == "object" && typeof localStorage["getItem"] == "function" && localStorage["getItem"]("presence") > (new Date().getTime()-36E5)){
        loadFull();
    }else{
        document.addEventListener("touchstart", loadFull);
        document.addEventListener("keydown", loadFull);
        document.addEventListener("scroll", loadFull);
        document.addEventListener("mousemove", loadFull);
    }
</script><noscript><img height="1" width="1" alt="FB" style="display:none"
src="https://www.facebook.com/tr?id=IDGOESHERE&ev=PageView&noscript=1"/></noscript>
</body>
</html><?php
}

function get_blog_index_ml($entries, $sizeof_blog, $pos) {
    //require_once("../Settings.php");
    $ml = "";
    $blog_template = '';
    $pagination_template = "";
    $number_of_buttons = ceil($sizeof_blog / POSTS_PER_PAGE);
    //generate pagination
    //TODO blog pagination
    /*for($i = 0; $i < $number_of_buttons; $i++) {
        if ($i  == 0) {
            $pagination_template .=  "<div id='page_nav-". $i ."'><a class='pagination-link' href='". $BASE_BLOG_URL.$i ."'>"."HOME"."</a></div>";
        } else if ($pos == $i){
            $pagination_template .=  "<div id='page_nav-". $i ."'><a class='pagination-link current-page as-button m-02 d-inline-block' href='". $BASE_BLOG_URL.$i ."'>". $i ."</a></div>";
        } else {
            $pagination_template .=  "<div id='page_nav-". $i ."'><a class='pagination-link as-button m-02 d-inline-block' href='". $BASE_BLOG_URL.$i ."'>". $i ."</a></div>";
        }
    }*/
    $thumbnails = array();
    //generate blog item
    for ($i = 0; $i < sizeof($entries); $i++){
      ob_start();
      ?>
      <div class="bloglist">
        <div class="bloglist-image">
          <a href="<?php echo $entries[$i]['canonical_url']; ?>"><img loading="lazy" class="animated-placeholder" data-gfxtype="src" data-gfxdata="<?php echo $entries[$i]['header_background_url_lo']; ?>" alt="blog-image" src="https://assets.jedstar.space/img/placeholder.webp"></a>
        </div>
        <div class="bloglist-line1">
          <div class="calendar">
            <img loading="lazy" class="animated-placeholder" data-gfxtype="src" data-gfxdata="/img/calendar grey.webp" alt="calendar"><span><?php echo $entries[$i]['publish_info']; ?></span>
          </div>
          <div class="tag capitalise">
            <img loading="lazy" class="animated-placeholder" data-gfxtype="src" data-gfxdata="/img/tag grey.webp" alt="TAG"><span><?php echo $entries[$i]['tag']; ?></span>
          </div>
        </div>
        <div class="bloglist-title">
          <a href="<?php echo $entries[$i]['canonical_url']; ?>"><h2 class="capitalise"><?php echo $entries[$i]['title']; ?></h2></a>
        </div>
        <div class="bloglist-desc">
          <p><?php echo $entries[$i]['description']; ?></p>
        </div>
        <div class="read-more">
          <a href="<?php echo $entries[$i]['canonical_url']; ?>" class="capitalise readmore">Read More
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="26" height="27" viewBox="0 0 26 27" fill="none">
						<g clip-path="url(#clip0_451_1386)">
						<mask id="mask0_451_1386" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="4" width="25" height="19">
						<rect y="4.64587" width="25" height="17.7083" fill="url(#pattern0)"/>
						</mask>
						<g mask="url(#mask0_451_1386)">
						<rect width="25" height="27" fill="url(#paint0_linear_451_1386)"/>
						</g>
						</g>
						<defs>
						<pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
						<use xlink:href="#image0_451_1386" transform="translate(0 -0.205882) scale(0.00195312 0.00275735)"/>
						</pattern>
						<linearGradient id="paint0_linear_451_1386" x1="0" y1="3.91935" x2="25" y2="3.91935" gradientUnits="userSpaceOnUse">
						<stop stop-color="#37A4F8"/>
						<stop offset="1" stop-color="#DB1EBF"/>
						</linearGradient>
						<clipPath id="clip0_451_1386">
						<rect width="26" height="27" fill="white"/>
						</clipPath>
						<image id="image0_451_1386" width="512" height="512" xlink:href="data:image/png;base64,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"/>
						</defs>
						</svg>
					</a>
        </div>
      </div>
      <?php
      $blog_template .= ob_get_clean();
    }
    $ml .=  $blog_template; //."<div class='button-container container row-1 center'>". $pagination_template ."</div>"; //TODO blog pagination
    return array("ml"=>$ml, "img"=>$thumbnails);
}
