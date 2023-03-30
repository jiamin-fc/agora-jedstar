<div class="index-container">
  <div class="header agora">
    <div class="bg-image"><img src="img/agora/space_dark_blue_planet2.png"></div>
    <div class="logo"><img src="img/agora/agoralogo.png"></div>
  </div>

  <div class="myagora-page">
    <div class="back-button" id="backButton">
      <button><div class="capitalise">Back</div></button>
    </div>
    <div class="rainbow-border"></div>

    <div id = "myagora-title" class="title">
      <h1 class="capitalise"><span class="color-blue">My</span> agora</h1>
    </div>


    <div id="myagora-cards" class="myagora-cards">

      <div id = "userNotLogged" class="title" style = "display:none">
        <h2 class="capitalise">please log in</h2>
        <p class="capitalise">Log in with your wallet to view your AGORA digital collectibles.</p>
        <button id="connect_btn2" class="capitalise"><div><img src="/img/agora/wallet.svg"> <span>Log in</span></div></button>
      </div>

     
      <!--
      <div class="card">
        <div class="card-image loading">&nbsp;</div>
        <div class="card-name">
          <p class="capitalise label">name</p>
          <p class="capitalise name loading"></p>
        </div>
        <div class="card-rarity">
          <p id="rarityTxt" class="capitalise loading">&nbsp;</p>
        </div>
      </div>
      -->

    </div>
    <div id = "myagora-boost">
      <div class="rainbow-border"></div>
      <div class = "title">
        <h2 class = "capitalise"><span class = "color-blue">Boosts</span> & Upgrades (<span id = "numBoost">?</span>)</h2>
        <div class = "resync-button">
          <button id = "resync-button"><div class = "capitalise"><img src = "img/resync_logo.png"> Re-Sync</div></button>
          <div class = "info-button">
            <img src = "img/agora/info.png">
          </div>
        </div> 
      </div><br><br>
      <div class="nft-detail-logo"><img src="https://assets.jedstar.space/img/SILVERVOLT_LOGO.webp"></div>
      <p class = "nft-detail-logo-description">This NFT offers various in app Boosts and Upgrades to our SILVERVOLT gaming product.<br>Sign up and top off your favorite games for FREE at <a href="">www.silvervolt.app</a></p>

      <div id ="noBoosts" class = "title" style = "display:none">
        <h2 class = "capitalise">you have no boosts</h2>
      </div>
      
      <div class="nft-detail-boosts">
        <div class="boost off">
          <div class="boost-check"><img src="/img/agora/check.svg"></div>
          <div class="boost-title loading"><p class="capitalise">&nbsp;</p></div>
        </div>
        <div class="boost off">
          <div class="boost-check"><img src="/img/agora/check.svg"></div>
          <div class="boost-title loading"><p class="capitalise">&nbsp;</p></div>
        </div><div class="boost off">
          <div class="boost-check"><img src="/img/agora/check.svg"></div>
          <div class="boost-title loading"><p class="capitalise">&nbsp;</p></div>
        </div><div class="boost off">
          <div class="boost-check"><img src="/img/agora/check.svg"></div>
          <div class="boost-title loading"><p class="capitalise">&nbsp;</p></div>
        </div><div class="boost off">
          <div class="boost-check"><img src="/img/agora/check.svg"></div>
          <div class="boost-title loading"><p class="capitalise">&nbsp;</p></div>
        </div>
      </div>
      <div class = "view-more-button">
        <button id="viewMore"><div class="capitalise">View All</div></button>
      </div>
    </div>
    <div id = "myagora-collectibles">
      <div class="rainbow-border"></div>
      <div class = "title">
        <h2 class = "capitalise"><span class = "color-blue">Digital</span> Collectibles (<span id = "numCollectibles">?</span>)</h2>
      </div><br><br>

      <div id ="noCards" class = "title" style = "display:none">
        <h2 class = "capitalise">you have no cards</h2>
      </div>

      <div id = "cards" class = "cards">
        <div class="card">
          <div class="card-image loading">&nbsp;</div>
          <div class="card-name">
            <p class="capitalise label">name</p>
            <p class="capitalise name loading"></p>
          </div>
          <div class="card-rarity">
            <p id="rarityTxt" class="capitalise loading">&nbsp;</p>
          </div>
        </div>
        <div class="card">
          <div class="card-image loading">&nbsp;</div>
          <div class="card-name">
            <p class="capitalise label">name</p>
            <p class="capitalise name loading"></p>
          </div>
          <div class="card-rarity">
            <p id="rarityTxt" class="capitalise loading">&nbsp;</p>
          </div>
        </div>
        <div class="card">
          <div class="card-image loading">&nbsp;</div>
          <div class="card-name">
            <p class="capitalise label">name</p>
            <p class="capitalise name loading"></p>
          </div>
          <div class="card-rarity">
            <p id="rarityTxt" class="capitalise loading">&nbsp;</p>
          </div>
        </div>
        <div class="card">
          <div class="card-image loading">&nbsp;</div>
          <div class="card-name">
            <p class="capitalise label">name</p>
            <p class="capitalise name loading"></p>
          </div>
          <div class="card-rarity">
            <p id="rarityTxt" class="capitalise loading">&nbsp;</p>
          </div>
        </div>
        <div class="card">
          <div class="card-image loading">&nbsp;</div>
          <div class="card-name">
            <p class="capitalise label">name</p>
            <p class="capitalise name loading"></p>
          </div>
          <div class="card-rarity">
            <p id="rarityTxt" class="capitalise loading">&nbsp;</p>
          </div>
        </div>
    </div>
  </div>
  
</div>
<div id = "info-popup" class = "popup">
  <div class = "title">
      <h2 class = "capitalise">Re-Sync</h2>
      <div class = "close-button">
        <img src = "img/agora/close.png">
      </div>
  </div>
  <p>Please RE-SYNC your NFT/SFT digital collectibles to ensure your latest Attributes and Bonuses are up to date in all our products and games.</p>
  <div class = "resync-button-popup">
    <button id = "resync-button-popup"><div class = "capitalise">Re-Sync</div></button>
  </div>
</div>

<div id = "complete-popup" class = "popup">
  <div class = "title">
      <h2 class = "capitalise">Re-Sync Complete</h2>
      <div class = "close-button">
        <img src = "img/agora/close.png">
      </div>
  </div>
  <p>Your NFT/SFT digital collectibles have been RE-SYNCed to ensure your latest Attributes and Bonuses are up to date in all our products and games.</p>
  <div class = "resync-button-popup">
    <button id = "close-button-popup"><div class = "capitalise">Close</div></button>
  </div>
</div>

<div id = "nft-popup" class = "popup">
  <div class = "title">
      <h2 class = "capitalise">Providers</h2>
      <div class = "close-button">
        <img src = "img/agora/close.png">
      </div>
  </div>
  <div class = "cards" id = "cards-popup">
    <div class="card">
      <div class="card-image loading">&nbsp;</div>
      <div class="card-name">
        <p class="capitalise label">name</p>
        <p class="capitalise name loading"></p>
      </div>
      <div class="card-rarity">
        <p id="rarityTxt" class="capitalise loading">&nbsp;</p>
      </div>
    </div>
    <div class="card">
      <div class="card-image loading">&nbsp;</div>
      <div class="card-name">
        <p class="capitalise label">name</p>
        <p class="capitalise name loading"></p>
      </div>
      <div class="card-rarity">
        <p id="rarityTxt" class="capitalise loading">&nbsp;</p>
      </div>
    </div>
    <div class="card">
      <div class="card-image loading">&nbsp;</div>
      <div class="card-name">
        <p class="capitalise label">name</p>
        <p class="capitalise name loading"></p>
      </div>
      <div class="card-rarity">
        <p id="rarityTxt" class="capitalise loading">&nbsp;</p>
      </div>
    </div>
    <div class="card">
      <div class="card-image loading">&nbsp;</div>
      <div class="card-name">
        <p class="capitalise label">name</p>
        <p class="capitalise name loading"></p>
      </div>
      <div class="card-rarity">
        <p id="rarityTxt" class="capitalise loading">&nbsp;</p>
      </div>
    </div>
    <div class="card">
      <div class="card-image loading">&nbsp;</div>
      <div class="card-name">
        <p class="capitalise label">name</p>
        <p class="capitalise name loading"></p>
      </div>
      <div class="card-rarity">
        <p id="rarityTxt" class="capitalise loading">&nbsp;</p>
      </div>
    </div>
  </div>
</div>