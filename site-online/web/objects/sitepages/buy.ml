<div class="index-container individual">
  <div class="header agora">
    <div class="bg-image"><img src="/img/agora/space_dark_blue_planet2.png"></div>
    <div class="logo"><img src="/img/agora/agoralogo.png"></div>
  </div>

  <div class="nft-content">
    <div class="back-button">
      <button id = "backButton"><div class="capitalise">Back</div></button>
    </div>

    <div class="nft-details">
      <div class="nft-intro">
        <!-- div class="bg-sparkle invisible"><img src="/img/agora/glow_v01.png"></div -->
        <div id="nftimage" class="nft-card-image loading"></div>
        <div class="nft-basic">
          <p class="capitalise label">Art by</p>
          <p id="artistName" class="name loading">&nbsp;</p>
          <p id="artistTitle" class="basic-title loading">&nbsp;</p>
          <p id="artistSource" class="source loading">&nbsp;</p>
        </div>
      </div>

      <div class="nft-detail">
        <div class="title collection-title">
          <h2 id="collectionName" class="capitalise collection loading">&nbsp;</h2>
					<div class="social-list">
						<div class="social-icon twitter">
							<a href="http://go.jedstar.com/twitter" aria-label="twitter">
							<img src="https://assets.jedstar.space/img/Twitter_footer.webp" alt="twitter">
							</a>
						</div>
						<div class="social-icon facebook">
							<a href="http://go.jedstar.com/facebook" aria-label="facebook">
							<img src="https://assets.jedstar.space/img/facebook_footer.webp" alt="facebook">
							</a>
						</div>
						<div class="social-icon reddit">
							<a href="http://go.jedstar.com/reddit" aria-label="reddit">
							<img src="https://assets.jedstar.space/img/reddit_footer.webp" alt="reddit">
							</a>
						</div>
					</div>
        </div>

        <div class="rainbow-border"></div>

        <div class="nft-detail-content">
          <div class="nft-detail-name">
            <p class="capitalise label">Name</p>
            <div class="title">
              <h2 id="nftname" class="capitalise loading paragraph">&nbsp;</h2>
            </div>
            <div class="card-rarity">
              <p id="rarity-label" class="capitalise loading paragraph">&nbsp;</p>
            </div>
          </div>

					<div class="nft-respec">
						<p class="capitalise label">Current Price</p>
						<div class="agora-buy-price">
              <div class="pricecurrency-wrapper"><select id="pricecurrency"><option value="USDC">USDC</option></select></div>
							<h3 id="price" class="loading">&nbsp;</h3>
						</div>
						<p id="mintedcount" class="remaining capitalise loading">&nbsp;</p>
						<button id="buynowsolo" class="buynow"><div class="capitalise">Buy now</div></button>
					</div>

          <div class="nft-detail-description">
            <p class="capitalise label">Description</p>
            <p id="nft-description-text" class="loading">&nbsp;</p>
          </div>

          <div class="nft-detail-abilities">
            <p class="capitalise label">Attributes & utilities</p>
            <p id="nft-utilities-text" class="loading"></p><br>
          </div>

          <div class="nft-detail-attributes">
            <div class="title">
              <h2 class="capitalise">Attributes</h2>
            </div>
            <div class="abilities-respec">
              <p class="capitalise">Upon minting <span id ="attributes-rarity">...</span> NFTS are given <span id ="attributes-given"><span id = "attributes-given-num">0</span> of 5</span> random attributes that have a random power scale from 1-100.</p>
            </div>
            <div class="attributes-table">
              <table>
                <tr id="Surge"><td class="attr-bar"><p class="capitalise">Serge</p></td><td class="attr-val">?</td></tr>
                <tr id="Might"><td class="attr-bar"><p class="capitalise">Might</p></td><td class="attr-val">?</td></tr>
                <tr id="Resilience"><td class="attr-bar"><p class="capitalise">Resilience</p></td><td class="attr-val">?</td></tr>
                <tr id="Wit"><td class="attr-bar"><p class="capitalise">Wit</p></td><td class="attr-val">?</td></tr>
                <tr id="Luck"><td class="attr-bar"><p class="capitalise">Luck</p></td><td class="attr-val">?</td></tr>
              </table>
            </div>
            <div class="chainlink">
              <p>Powered by</p>
              <img src="/img/agora/chainlink.png">
              <p class="capitalise"><a href="https://chain.link/vrf" target="_blank" rel="nofollow">about</a></p>
            </div>
          </div>
          <div><p>This NFT offers utility across the JEDSTAR universe of apps and games. </p></div>
          <div class="nft-detail-silvervolt">
            <div class="nft-detail-logo"><img src="https://assets.jedstar.space/img/SILVERVOLT_LOGO.webp"></div>
            <p>This NFT offers various in app Boosts and Upgrades to our SILVERVOLT gaming product. Sign up and top off your favorite games for FREE at <a href="">www.silvervolt.app</a></p>
            <div class="nft-detail-boosts">
              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise"><span id="boost-bid"></span> bid boost</p></div>
                <div class="boost-subtitle"><p class="label">Gives you additional weekly auction bids. Up to <span id = "numBid">...</span> / week for a <span class = "attributes-rarities">...</span>.</p></div>
                <div class="boost-stat"><p class="capitalise">Serge</p></div>
              </div>

              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise"><span id="boost-mining"></span> mining boost</p></div>
                <div class="boost-subtitle"><p class="label">Boosts your VOLT mining power. Up to <span id = "numMining">...</span>% for a <span class = "attributes-rarities">...</span>.</p></div>
                <div class="boost-stat"><p class="capitalise">Might</p></div>
              </div>

              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise">Tier boost</p></div>
                <div class="boost-subtitle"><p class="label">Instant access to <span id = "tierType">...</span>-Tier Auctions for a <span class = "attributes-rarities">...</span>.</p></div>
                </div>

              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise"><span id="boost-referral"></span> Referral boost</p></div>
                <div class="boost-subtitle"><p class="label">Boosts your referral VOLT bonus. Up to <span id = "numReferral">...</span>% extra for a <span class = "attributes-rarities">...</span>.</p></div>
                <div class="boost-stat"><p class="capitalise">Resilience</p></div>
              </div>

              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise"><span id="boost-wheel"></span> Wheel Spin Boost</p></div>
                <div class="boost-subtitle"><p class="label">Cheaper Wheel Spins! (Coming Soon) Up to <span id = "numWheel">...</span>% off wheel spins for a <span class = "attributes-rarities">...</span>.</p></div>
                <div class="boost-stat"><p class="capitalise">Wit</p></div>
              </div>

              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise"><span id="boost-lottery"></span> Lottery Ticket Boost</p></div>
                <div class="boost-subtitle"><p class="label">Cheaper Lottery Tickets! Up to <span id = "numLottery">...</span>% off Lottery tickets for a <span class = "attributes-rarities">...</span>.</p></div>
                <div class="boost-stat"><p class="capitalise">Luck</p></div>
              </div>

              <div class="boost off" id = "highRoller">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise">High roller access</p></div>
                <div class="boost-subtitle"><p class="label">Coming Soon.</p></div>
              </div>

              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise">Custom name</p></div>
                <div class="boost-subtitle"><p class="label">Update your handle to be a custom name! Ultra-Rare NFTs and above.</p></div>
              </div>

              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise">Custom avatar</p></div>
                <div class="boost-subtitle"><p class="label">Select your own custom Avatar! Rare NFTs and above.</p></div>
              </div>

            </div>
          </div>
          <div class="nft-detail-extras">
            <div class="nft-detail-extra">
              <div class="nft-detail-logo"><img src="/img/agora/starstaking_logo_transparent_horizontal_v01.png"></div>
              <p>JEDSTAR Gaming’s official staking platform</p>
              <p class="capitalise label">Coming soon</p>
            </div>

            <div class="nft-detail-extra">
              <div class="nft-detail-logo"><img src="/img/agora/nightclaws.png"></div>
              <p>Arcade RPG Game (Mobile)</p>
              <p class="capitalise label">Coming soon</p>
            </div>

            <div class="nft-detail-extra">
              <div class="nft-detail-logo"><img src="/img/agora/ascension.png"></div>
              <p>Real-Time Strategy Game (Mobile)</p>
              <p class="capitalise label">Coming soon</p>
            </div>

            <div class="nft-detail-extra">
              <div class="nft-detail-logo"><img src="/img/agora/aethernova.png"></div>
              <p>Collectible Card Game (Mobile/PC)</p>
              <p class="capitalise label">Coming soon</p>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
