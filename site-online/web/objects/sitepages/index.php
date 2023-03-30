<?php

function render_dynamic_content($page_params){
	global $blogroll;
  require_once(WEB_ROOT."../lib/PageCache.php");
  $pc = new PageCache(array(
    "page"=>"index",
    "validity_period" => TIMESECS_ONE_WEEK //12 hours before regeneration is required
  ));
  $ml = $pc->get_ml();
  if (!$ml){
    $addnimgs = array();
		ob_start();
		?>
		<div class="index-container">
			<div class="header">
				<div class="carousel">
				
					<div class="carousel-tab tabpage1">
						<div class="carousel-content">
							<div class="subtitle">
								<h3 class="capitalise">Agora nfts</h3>
							</div>
							<div class="title">
								<h2 class="capitalise">The genesis mint</h2>
							</div>
							<div class="text">
								<p>PLAY to earn by challenging yourself or duelling others In any of our games.</p>
							</div>
							<div class="carousel-button">
								<button class="capitalise"><div>buy now</div></button>
							</div>
						</div>
						<div class="extra-space">
							<br>
						</div>
					</div>
				
					<div class="carousel-tab tabpage2">
						<div class="carousel-content">
							<div class="subtitle">
								<h3 class="capitalise">Agora nfts</h3>
							</div>
							<div class="title">
								<h2 class="capitalise">The genesis mint</h2>
							</div>
							<div class="text">
								<p>PLAY to earn by challenging yourself or duelling others In any of our games.</p>
							</div>
							<div class="carousel-button">
								<button class="capitalise"><div>buy now</div></button>
							</div>
						</div>
						<div class="extra-space">
							<br>
						</div>
					</div>
				
					<div class="carousel-tab tabpage3">
						<div class="carousel-content">
							<div class="subtitle">
								<h3 class="capitalise">Agora nfts</h3>
							</div>
							<div class="title">
								<h2 class="capitalise">The genesis mint</h2>
							</div>
							<div class="text">
								<p>PLAY to earn by challenging yourself or duelling others In any of our games.</p>
							</div>
							<div class="carousel-button">
								<button class="capitalise"><div>buy now</div></button>
							</div>
						</div>
						<div class="extra-space">
							<br>
						</div>
					</div>
				</div>
						
				<div class="carousel-pagination">
					<div class="page1"></div>
					<div class="page2"></div>
					<div class="page3"></div>
				</div>
				
				<div class="header-background">
					<img src="/img/agora/genesis_name_promo_noLogo_v02.png">
				</div>
				<div class="header-marketplace">
					<img src="/img/agora/agoramarketplace.png">
				</div>
				<div class="header-is-live">
					<p class="capitalise">“Genesis Mint” NFT Sale is live.</p>
				</div>
				<div class="header-button">
					<button id="mywallet2" class="capitalise"><div><img src="/img/agora/wallet.svg"> Connect Wallet</div></button>
				</div>
			</div>

			<div class="info">
				<div class="about close">
					<div id="goBack" class="title">
						<h2 class="capitalise">About the genesis mint</h2>
					</div>
					<!-- <div class="content">
						<p>We are proud to introduce The GENESIS Mint, JEDSTAR Gaming’s first NFT minting. This lore driven mint includes 18 unique designs in four distinctive collections. Three of the collections, fifteen pieces in total, were hand painted in-house by JEDSTAR Gaming’s very own Creative Team! The fourth collection is comprised of three original hand painted pieces brought to you by Triple A Artist and exclusive JEDSTAR Flagship Illustrator, Tony Moy. This is Tony’s first NFT Collection and we are excited to offer you a part of this artistic history in the making.</p>
						<p>The GENESIS Mint is not just stellar artwork - our NFTs offer true utility! Each piece will have a set of attributes that offer special privileges, buffs, upgrades and prioritization to the NFT’s owner. These attributes apply to not just one, but all of JEDSTAR Gaming’s products, games and services, present and future! Benefits and utilities will evolve with our products as they are released and updated.</p>
						<p>We invite you to unlock the power of the Galaxy through JEDSTAR NFT ownership.</p>
					</div>
					<div class="subtitle">
						<h3 class="capitalise"><span class="color-blue">NFTs Featuring</span> the first of many utilities</h3>
					</div>
					<div class="about-logos">
						<img src="https://assets.jedstar.space/img/SILVERVOLT_LOGO.webp">
					</div>
					<div class="about-button">
						<button id="viewUtilities" class="capitalise"><div>View <span id="utilitiesNum">15</span> utilities</div></button>
					</div>
					<div class="about-more capitalise">
						<span>More</span>
					</div> -->
				</div>
				<div class="view-buy-button">
					<button id="viewBuy" class="capitalise"><div>How to buy</div></button>
					<button id="learnMore" class="capitalise"><a><div>Learn More</div></a></button>
				</div>
				<div id="backToTop" class="collection-links">
					<h2 class="capitalise"><a href="#0"><span class="color-blue">Battle Ready</span> Collection</a></h2>
					<h2 class="capitalise"><a href="#1"><span class="color-blue">After Dark</span> Collection</a></h2>
					<h2 class="capitalise"><a href="#2"><span class="color-blue">Jed's Journey</span> Collection</a></h2>
					<h2 class="capitalise"><a href="#3"><span class="color-blue">Tony Moy</span> Collection</a></h2>
				</div>
				<a class="topButton" href="#goBack"><img src="img/agora/top.png"></a>

				<div class="rainbow-border"></div>
			</div>


			<div class="collections">
				<?php
				//NOTE: This activity is expensive, and so heavy caching is deployed to prevent this having to be run more than is realistically necessary
				require_once(WEB_ROOT."../lib/chain_functions.php");
				require_once(WEB_ROOT."../lib/Product.php");
				$totalProducts = getTotalProducts();
				error_log("Total Products: ".$totalProducts);
				$collections = array();
				for ($i = 0; $i < $totalProducts; $i++){
					$groupId = getProductGroupId($i);
					$typeId = getGroupTypeId($groupId);
					if (!isset($collections[$typeId][$groupId])){
						$collections[$typeId][$groupId] = array(
							"name" => getGroupName($groupId),
							"products" => array()
						);
					}
					$collections[$typeId][$groupId]["products"][] = array(
						"tier" => getProductTier($i),
						"mintLimit" => getProductMintLimits($i),
						"name" => getProductName($i),
						"productid" => $i,
						"productObj" => new Product($i)
					);
				}
				for ($i = 0; $i < count($collections); $i++){
					switch ($i){
						case 0:
						  $mintType = "Genesis mint";
							$collectionClass = "collection";
							break;
						case 1:
						  $mintType = "Featured artist";
							$collectionClass = "featured-collection";
							break;
						case 2:  //Unless explicitly allocated, these items are not for sale, and so should not be put in the index browser
						default:
						  $mintType = "hidden";
							$collectionClass = "hidden";
							break;
						}
						if ($mintType != "hidden"){
							foreach ($collections[$i] as $j => $jval){ //($j = 0; $j < count($collections[$i]); $j++){
								?>
								<div class="rainbow-border"></div>
								<div class="<?php echo $collectionClass; ?>" id = "<?php echo $j; ?>">
									<div class="title">
										<h2 class="capitalise"><span class="color-blue"><?php echo $mintType; ?></span> <?php echo $collections[$i][$j]["name"]; ?></h2>
									</div>
									<div class="cards">
										<?php
										if ($i == 1){
											//This is a featured artist collection, and needs an "Artist Card"
											$dbsql = "SELECT imgurl, name, blurb FROM artists WHERE blockchainGroupId = ?";
											$dbres = db_pquery($dbsql, array(array("i", $j)));
											if ($dbres["status"]){
												error_log("===============> DB ===> ".json_encode($dbres));
												$artist["url"] = $dbres["data"][0]["imgurl"];
												$artist["name"] = $dbres["data"][0]["name"];
												$artistblurb = json_decode($dbres["data"][0]["blurb"], true);
												$artist["blurb"] = $artistblurb["description"];
											}else{
												//This is bad.
												error_log("==== !!!!! ==== Unable to fetch artist information for blockchainGroupId ".$j);
											}
											?>
											<div class="feature">
												<div class="feature-image">
													<img src="<?php echo $artist["url"]; ?>">
												</div>
												<div class="title">
													<h2 class="capitalise"><?php echo $artist["name"]; ?></h2>
												</div>
												<div class="feature-text">
													<p><?php echo $artist["blurb"]; ?></p>
												</div>
											</div>

											<?php
										}
										for ($k = 0; $k < count($collections[$i][$j]["products"]); $k++){
											$item = $collections[$i][$j]["products"][$k];
											switch ($item["tier"]){
												case 0:
												$tierName = "Secret rare";
												$tierClass = "secretrare";
												break;
												case 1:
												$tierName = "Ultra rare";
												$tierClass = "ultrarare";
												break;
												case 2:
												$tierName = "Rare";
												$tierClass = "rare";
												break;
												case 3:
												$tierName = "Uncommon";
												$tierClass = "uncommon";
												break;
												case 4:
												$tierName = "Common";
												$tierClass = "common";
												break;
												default: //case 5:
												$tierName = "Standard";
												$tierClass = "common";
												break;
											}
											$prices = $item["productObj"]->get_all_prices();
											?>
											<div class="card <?php echo $tierClass; ?>" data-productid="<?php echo $item["productid"]; ?>" data-maxmint="<?php echo $item["mintLimit"]; ?>">
												<div class="card-image">
													<img src="https://assets.jedstar.space/nft/<?php echo $item["productid"]; ?>.jpg" alt="<?php echo $item["name"]; ?>">
												</div>
												<div class="card-name">
													<p class="capitalise label">name</p>
													<p class="capitalise name"><?php echo $item["name"]; ?></p>
												</div>
												<div class="card-rarity">
													<p class="capitalise"><?php echo $tierName; ?></p>
												</div>
												<div class="card-price">
													<p class="capitalise label">price</p>
													<p class="capitalise price">USDC <?php echo $prices["USDC"]; ?></p>
												</div>
												<div class="card-remaining" id="label-remain-prod<?php echo $item["productid"]; ?>">
													<p class="capitalise"><span id="remain_prod<?php echo $item["productid"]; ?>">..</span> of <?php echo $item["mintLimit"]; ?> remaining</p>
												</div>
												<div class="card-button">
													<button class="capitalise buynow" data-productid="<?php echo $item["productid"]; ?>"><div>Buy Now</div></button>
												</div>
											</div>

											<?php
										}
										?>
									</div>
								</div>
								<?php
							}
						}
						?>

					<?php
				}
				?>
				</div>
			</div>
<?php

		$ml = ob_get_contents();
		ob_end_clean();
		$pc->cache_ml($ml); //save this generated ML for the future
	}
	return $ml;
}
