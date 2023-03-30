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
<div class="intro-container">
		<div class="background-image"><img src="img/AdobeStock_408698751 1.png"></div>
		
		<div class="section">
			<div class="header">
				<div class="logos">
					<img src="img/starstaking_logo_transparent_horizontal_v01 1.png">
				</div>
				<div class="header-text">
					<p>Stake your JED, $KRED or SFTs to earn tokens, rewards, and bonuses throughout the entire JEDSTAR Gaming universe.</p>
				</div>
			</div>
		</div>
			
		<div class="stake-picks">
			<div class="pick">
				<img class="pick-space" src="img/pick1.png">
				<div id = "stakeJED" class="button">
					<button class="capitalise"><div>Stake jed</div></button>
				</div>
			</div>
			
			<div class="pick hide">
				<img src="img/pick2.png">
				<div class="button">
					<button class="capitalise"><div>Stake kred</div></button>
				</div>
			</div>
			
			<div class="pick hide">
				<img src="img/SFT_logo.png">
				<div class="button disabled">
					<button class="capitalise"><div>Stake sfts</div></button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="index-container">
		<div class="background-image"><img src="img/AdobeStock_408698751 1.png"></div>
		
		<div class="section">
			<div class="header">
				<div class="logos">
					<img src="img/starstaking_logo_transparent_horizontal_v01 1.png">
					<h1>:</h1>
					<img src="img/image 62.png">
				</div>
			</div>
			
			<div class="total">
				<div class="total-box rewards">
					<p class="capitalise">Total rewards remaining</p>
					<h2 id = "trr" class="colored capitalise">... kred</h2>
				</div>
				<div class="total-box value">
					<p class="capitalise">Total value locked</p>
					<h2 id = "tvl" class="colored capitalise">... kred</h2>
				</div>
			</div>
			
			<div class="stakes">
				<div id = "stake1" class="stake">
					<div class = "dimmer" >
						<p class = "loading">Loading...</p>
					</div>
					<div class="stake-day">
						<img src="img/30_days.png">
						<img class="logo" src="img/jedstar_logo4_GradientCOLOR_black_transparent 1.png">
					</div>
					
					<div class="stake-title">
						<p class="capitalise">staking amount remaining</p>
						<h3 id = "stakeable1" class="colored">... JED</h3>
					</div>
					
					<div class="stake-form">
						<div class="line-two-elem">
							<div class="form-elem">
								<p class="label">Locking Duration</p>
								<input id = "min-period1" type="text" placeholder="enter amount" value="30 days" disabled>
							</div>
							
							<div class="form-elem">
								<p class="label">APY</p>
								<input id = "apy1" type="text" placeholder="enter amount" value="8%" disabled>
							</div>
						</div>
						
						<div class="form-one-elem">
							<p class="label">Staking Amount</p>
							<div class="input-join">
								<input class="capitalise" type="text" placeholder="enter amount" value="jed" disabled>
								<input id = "stakingAmount1" type="text" placeholder="enter amount">
								<span id = "max1" class="max">max</span>
							</div>
							<p id = "mykred1" class="mykred balance">My available $JED: Connect your wallet</p>
							<p id = "warning1" class="warning">You have exceeded the maximum</p>
						</div>
						
						<div class="form-one-elem">
							<p id = "rewardsLabel1" class="label">Total Potential Reward based on 'Staking Amount'</p>
							<div class="input-join">
								<input class="capitalise" type="text" placeholder="enter amount" value="$kred" disabled>
								<input id = "value-after-apy1" type="text" placeholder="---" disabled>
							</div>
							<div class="loading-bar"></div>
							<!--<p id = "total-rewards-p1" class="mykred">Total Calculated based on “Staking Amount”</p>-->
						</div>
						
						<div class="form-area">
							<p class="label">Benefits (3)</p>
							<div class="kdarena">
								<!-- <p class="kdarenatitle">KDArena</p> -->
								<ul>
									<li> 1 PFP NFT mint (1)</li>
									<li>Priority for JEDMembership</li>
									<li>Priority for STARSTAKING Season 2</li>
								</ul>
								<!-- <p class="kdarena-more"><span>more</span></p> -->
							</div>
						</div>
							
						<div class="maturity form-one-elem">
							<p class="label">Time to Maturity</p>
							<div class="input-join">
								<input id = "maturity1" type="text" placeholder="" value="Calculating..." disabled>
							</div>
						</div>
						
						<div class="button">
							<button id = "stakeBtn1" class="capitalise stakeBtn"><div>Stake Now</div></button>
							<button id = "claim1" class="capitalise claimBtn" disabled><div>Claim reward</div></button>
							<p id = "locking1" class="locking"></p>
						</div>
						
					</div>
				</div>
			
				<div id = "stake2" class="stake">
					<div class = "dimmer" >
						<p class = "loading">Loading...</p>
					</div>
					<div class="stake-day">
						<img src="img/60_days.png">
						<img class="logo" src="img/jedstar_logo4_GradientCOLOR_black_transparent 1.png">
					</div>
					
					<div class="stake-title">
						<p class="capitalise">staking amount remaining</p>
						<h3 id = "stakeable2" class="colored">... JED</h3>
					</div>
					
					<div class="stake-form">
						<div class="line-two-elem">
							<div class="form-elem">
								<p class="label">Locking Duration</p>
								<input id = "min-period2" type="text" placeholder="enter amount" value="60 days" disabled>
							</div>
							
							<div class="form-elem">
								<p class="label">APY</p>
								<input id = "apy2" type="text" placeholder="enter amount" value="14%" disabled>
							</div>
						</div>
						
						<div class="form-one-elem">
							<p class="label">Staking Amount</p>
							<div class="input-join">
								<input class="capitalise" type="text" placeholder="enter amount" value="jed" disabled>
								<input id = "stakingAmount2" type="text" placeholder="enter amount">
								<span id = "max2" class="max">max</span>
							</div>
							<p id = "mykred2" class="mykred balance">My available $JED: Connect your wallet</p>
							<p id = "warning2" class="warning">You have exceeded the maximum</p>
						</div>
						
						<div class="form-one-elem">
							<p id = "rewardsLabel2" class="label">Total Potential Reward based on 'Staking Amount'</p>
							<div class="input-join">
								<input class="capitalise" type="text" placeholder="enter amount" value="$kred" disabled>
								<input id = "value-after-apy2" type="text" placeholder="---" disabled>
							</div>
							<div class="loading-bar"></div>
							<!--<p id = "total-rewards-p2" class="mykred">Total Calculated based on “Staking Amount”</p>-->
						</div>
						
						<div class="form-area">
							<p class="label">Benefits (5)</p>
							<div class="kdarena">
								<p class="kdarenatitle">Everything  in 30 days; plus</p>
								<ul>
									<li>2 additional PFP NFT mint (3)</li>
									<li>Confirmed JEDMembership slot</li>
									<li>Whitelisted for lower tier NC Comic Book NFT</li>
									<li>Access to JEDSTAR ICO Platform lower tiers</li>
								</ul>
								<!-- <p class="kdarena-more"><span>more</span></p> -->
							</div>
						</div>
							
						<div class="maturity form-one-elem">
							<p class="label">Time to Maturity</p>
							<div class="input-join">
								<input id = "maturity2" type="text" placeholder="" value="Calculating..." disabled>
							</div>
						</div>
						
						<div class="button">
							<button id = "stakeBtn2" class="capitalise stakeBtn"><div>Stake Now</div></button>
							<button id = "claim2" class="capitalise claimBtn" disabled><div>Claim reward</div></button>
							<p id = "locking2" class="locking"></p>
						</div>
						
					</div>
				</div>
			
				<div id = "stake3" class="stake">
					<div class = "dimmer" >
						<p class = "loading">Loading...</p>
					</div>
					<div class="stake-day">
						<img src="img/120_days.png">
						<img class="logo" src="img/jedstar_logo4_GradientCOLOR_black_transparent 1.png">
					</div>
					
					<div class="stake-title">
						<p class="capitalise">staking amount remaining</p>
						<h3 id = "stakeable3" class="colored">... JED</h3>
					</div>
					
					<div class="stake-form">
						<div class="line-two-elem">
							<div class="form-elem">
								<p class="label">Locking Duration</p>
								<input id = "min-period3" type="text" placeholder="enter amount" value="60 days" disabled>
							</div>
							
							<div class="form-elem">
								<p class="label">APY</p>
								<input id = "apy3" type="text" placeholder="enter amount" value="1.5%" disabled>
							</div>
						</div>
						
						<div class="form-one-elem">
							<p class="label">Staking Amount</p>
							<div class="input-join">
								<input class="capitalise" type="text" placeholder="enter amount" value="jed" disabled>
								<input id = "stakingAmount3" type="text" placeholder="enter amount">
								<span id = "max3" class="max">max</span>
							</div>
							<p id = "mykred3" class="mykred balance">My available $JED: Connect your wallet</p>
							<p id = "warning3" class="warning">You have exceeded the maximum</p>
						</div>
						
						<div class="form-one-elem">
							<p id = "rewardsLabel3" class="label">Total Potential Reward based on 'Staking Amount'</p>
							<div class="input-join">
								<input class="capitalise" type="text" placeholder="enter amount" value="$kred" disabled>
								<input id = "value-after-apy3" type="text" placeholder="---" disabled>
							</div>
							<div class="loading-bar"></div>
							<!--<p id = "total-rewards-p3" class="mykred">Total Calculated based on “Staking Amount”</p>-->
						</div>
						
						<div class="form-area">
							<p class="label">Benefits (4)</p>
							<div class="kdarena">
								<p class="kdarenatitle">Everything in 60 days; plus</p>
								<ul>
									<li>2 additional PFP NFT mint (5)</li>
									<li>Whitelisted for all tier NC Comic Book NFT mint</li>
									<li>Access to JEDSTAR ICO platform Top tier</li>
								</ul>
								<!-- <p class="kdarena-more"><span>more</span></p> -->
							</div>
						</div>
							
						<div class="maturity form-one-elem">
							<p class="label">Time to Maturity</p>
							<div class="input-join">
								<input id = "maturity3" type="text" placeholder="" value="Calculating..." disabled>
							</div>
						</div>
						
						<div class="button">
							<button id = "stakeBtn3" class="capitalise stakeBtn"><div>Stake Now</div></button>
							<button id = "claim3" class="capitalise claimBtn" disabled><div>Claim reward</div></button>
							<p id = "locking3" class="locking"></p>
						</div>
						
					</div>
				</div>
			</div>
			
		</div>
		<div id = "info-popup" class = "popup">
			<div class = "title">
				<h2 id = "popup-title" class = "capitalise">...</h2>
				<div class = "close-button">
					<img src = "/img/agora/close.png">
				</div>
			</div>
			<p id = "popup-text" >...</p>
			<div id = "authorize-button-div" class = "authorize-button-popup">
				<button id = "authorize-button-popup"><div id = "popup-button-text" class = "capitalise">...</div></button>
			</div>
		</div>
	</div>
<?php

		$ml = ob_get_contents();
		ob_end_clean();
		$pc->cache_ml($ml); //save this generated ML for the future
	}
	return $ml;
}