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
        <div id="nftimage" class="nft-card-image loading"></div>
        <div class="nft-basic">
          <p class="capitalise label">Art by</p>
          <p id="artistname" class="name loading">&nbsp;</p>
          <p id="artisttitle" class="basic-title loading">&nbsp;</p>
          <p id="artistsource" class="source loading">&nbsp;</p>
        </div>
      </div>

      <div class="nft-detail">
        <div class="title collection-title">
          <h2 id="collectionname" class="capitalise collection loading">&nbsp;</h2>
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
              <h2 id="name" class="capitalise loading">&nbsp;</h2>
            </div>
            <div class="card-rarity">
              <p id="rarity" class="capitalise loading">&nbsp;</p>
            </div>
          </div>

					<div class="nft-respec">
						<p class="capitalise label">Respec your attributes</p>
            <p id = "respec_desc"></p>
						<p class="source"><a href="">Requirements & Rules</a></p>
            <div class="agora-regen-price">
              <div class="pricecurrency-wrapper"><select id="currencyDropdown"><option value="KRED">KRED</option><option value="USDC">USDC</option><option value="USDT">USDT</option><option value="BUSD">BUSD</option></select></div>
              <h3 id="respec_price" class="loading">&nbsp;</h3>
            </div>
						<p id="respec_available_in" class="availablein capitalise loading">&nbsp;</p>
						<button id="btn_regen"><div class="capitalise">Regen attributes</div></button>
					</div>

          <div class="nft-detail-description">
            <p class="capitalise label">Description</p>
            <p id="nft-description-text" class="loading paragraph">&nbsp;</p>
          </div>

          <div class="nft-detail-abilities">
            <p class="capitalise label">Attributes & utilities</p>
            <p id="nft-utilities-text" class="loading paragraph"></p><br>
          </div>

          <div class="nft-detail-attributes">
            <div class="title">
              <h2 class="capitalise">Attributes</h2>
            </div>
            <div class="abilities-respec">
              <p class="capitalise">Attributes can be respeced which will randomly assign a new set of attributes and assign new values to each of them.</p>
            </div>
            <div class="attributes-table">
              <table>
                <tr id="Serge"><td class="attr-bar"><p class="capitalise">Serge</p></td><td class="attr-val">0</td></tr>
                <tr id="Might"><td class="attr-bar"><p class="capitalise">Might</p></td><td class="attr-val">0</td></tr>
                <tr id="Resilience"><td class="attr-bar"><p class="capitalise">Resilience</p></td><td class="attr-val">0</td></tr>
                <tr id="Wit"><td class="attr-bar"><p class="capitalise">Wit</p></td><td class="attr-val">0</td></tr>
                <tr id="Luck"><td class="attr-bar"><p class="capitalise">Luck</p></td><td class="attr-val">0</td></tr>
              </table>
            </div>
            <div class="chainlink">
              <p>Powered by</p>
              <img src="/img/agora/chainlink.png">
              <p class="capitalise"><a href="https://chain.link/vrf" target="_blank" rel="nofollow">about</a></p>
            </div>
          </div>

          <div class="nft-detail-silvervolt">
            <div class="nft-detail-logo"><img src="https://assets.jedstar.space/img/SILVERVOLT_LOGO.webp"></div>
            <p>This NFT offers various in app Boosts and Upgrades to our SILVERVOLT gaming product. Sign up and top off your favorite games for FREE at <a href="">www.silvervolt.app</a></p>
            <div class="nft-detail-boosts">
              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise"><span id="boost-bid"></span> bid boost</p></div>
                <div class="boost-subtitle"><p class="label"><span id = "numBid">...</span> Additional Bids per week.</p></div>
                <div class="boost-stat"><p class="capitalise">Serge</p></div>
              </div>

              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise"><span id="boost-mining"></span> mining boost</p></div>
                <div class="boost-subtitle"><p class="label"><span id = "numMining">...</span>% Boost to VOLTS Mining Power.</p></div>
                <div class="boost-stat"><p class="capitalise">Might</p></div>
              </div>

              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise">Tier boost</p></div>
                <div class="boost-subtitle"><p class="label">Access to <span id = "tierType">...</span> tier auctions.</p></div>
              </div>

              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise"><span id="boost-referral"></span> Referral boost</p></div>
                <div class="boost-subtitle"><p class="label"><span id = "numReferral">...</span>% extra VOLTS from Referrals.</p></div>
                <div class="boost-stat"><p class="capitalise">Resilience</p></div>
              </div>

              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise"><span id="boost-wheel">Wheel Spin Boost</span></p></div>
                <div class="boost-subtitle"><p class="label" id="boost-wheel-pricing">💎<span class="wheelBoost">...</span>, 💎<span class = "wheelBoost">...</span>, and 💎<span class = "wheelBoost">...</span> as your special pricing</p></div>
                <div class="boost-stat"><p class="capitalise">Wit</p></div>
              </div>

              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise"><span id="boost-lottery">Lottery Ticket Boost</span></p></div>
                <div class="boost-subtitle"><p class="label" id="boost-lottery-pricing">💎<span class = "lotteryBoost">...</span>, 💎<span class = "lotteryBoost">...</span>, and 💎<span class="lotteryBoost">...</span> as your special pricing</p></div>
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
                <div class="boost-subtitle"><p class="label">Access to Custom Name: <span id = "customNameAccess">...</span></p></div>
              </div>

              <div class="boost off">
                <div class="boost-check"><img src="/img/agora/check.svg"></div>
                <div class="boost-title"><p class="capitalise">Custom avatar</p></div>
                <div class="boost-subtitle"><p class="label">Access to Custom Avatar: <span id = "customAvatarAccess">...</span></p></div>
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
