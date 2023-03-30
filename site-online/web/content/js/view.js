let data;
let currDropdown = document.getElementById("currencyDropdown");
let btn_respec = document.getElementById("btn_regen");
let spec_anim, token;
let pricing_matrix = {
  "KRED": 30000,
  "USDC": 3,
  "USDT": 3,
  "BUSD": 3,
  "AGORA": 3
};
let load_all_prices = function(){
  currDropdown.innerHTML = "";
  for (curr in pricing_matrix){
    if (pricing_matrix.hasOwnProperty(curr)){
      if ((curr == "AGORA" && Agora.balances["AGORA"] > 0) || (curr != "AGORA")){
        let opta = document.createElement("OPTION");
        opta.value = curr;
        opta.innerHTML = curr;
        currDropdown.appendChild(opta);
      }
    }
  }
};
Agora.hook("balances", function(balances){
  //this fires everytime the user's balance is updated
  load_all_prices();
  for (curr in Agora.balances){
    if (Agora.balances.hasOwnProperty(curr)){
      if (typeof pricing_matrix[curr] != "undefined" && Agora.balances[curr] >= pricing_matrix[curr]){
        for (let i=0; i < currDropdown.options.length; i++){
          if (currDropdown.options[i].value == curr){
            currDropdown.selectedIndex = i;
            break;
          }
        }
        break;
      }
    }
  }

  respec_price.innerHTML = pricing_matrix[currencyDropdown.value];
});

Agora.on("ready").then(function(){
  load_all_prices();

  let curr_url = window.location.href;
  token = curr_url.split('#')[1];
  if (token && /^[0-9]{77}$/.test(token)) {
    retrieveData(token);
  }else{
    blockingModal(true, "Your URL does not seem to be correctly formatted. Please check it and try again. Redirecting you to the homepage.");
    setTimeout(function(){
      window.location = "/";
    }, 4000);
  }
  const share_buttons = document.querySelectorAll('.share-icon');
    share_buttons.forEach(button => {
      button.addEventListener('click', function(event){
        let icons = event.target.parentNode.id;
        let URL;
        switch(icons){
          case 'twitter':
            URL = "https://twitter.com/intent/tweet?url=" + window.location.href;
            break;
          case 'facebook':
            URL = "https://www.facebook.com/sharer/sharer.php?u=" + window.location.href;
            break;
          case 'reddit':
            URL = "https://reddit.com/submit?url=" + window.location.href;
            break;
          case 0:
            break;
        }
        if (URL) window.open(URL, '_blank').focus();
      });
    });
    const back_button = document.getElementById("backButton");
    back_button.addEventListener('click', function(){
      history.back();
    });
});

// This function retrieves the data by calling an API
function retrieveData(token){
  let req = new XMLHttpRequest();
  req.open("GET", Settings.NFTJsonAPI + token);
  req.setRequestHeader("Accept", "application/json");
  req.send();
  req.onload=function(){
    data = JSON.parse(req.responseText);
    populateData(data);
  }
}

// This function populates and manipulates the DOM with data
function populateData(data){
  let animFadeIn = function (domElemArr){
    for (let e=0; e < domElemArr.length; e++){
      domElemArr[e].classList.remove("loading");
      domElemArr[e].classList.add("fade-in");
    }
  }
  let rarity_type;
  let traits = [];
  let boosts = [];
  let bonuses = [];
  let custom = [];
  let collectionname = document.getElementById("collectionname");
  let name = document.getElementById("name");
  let rarity = document.getElementById("rarity");
  let imageDiv = document.getElementById("nftimage");
  let description = document.getElementById("nft-description-text");
  let utilities = document.getElementById("nft-utilities-text");
  let attributes = document.getElementsByClassName("nft-detail-attributes")[0].children[2];
  let nft_boost = document.getElementsByClassName("nft-detail-boosts")[0].getElementsByClassName("boost");

  let respec_price = document.getElementById("respec_price");
  let respec_available_in = document.getElementById("respec_available_in");
  let respec_desc = document.getElementById("respec_desc");

  let artistname = document.getElementById("artistname");
  let artisttitle = document.getElementById("artisttitle");
  let artistsource = document.getElementById("artistsource");

  let bid = document.getElementById("numBid");
  let mining = document.getElementById("numMining");
  let referral = document.getElementById("numReferral");
  let wheels = document.getElementsByClassName("wheelBoost");
  let lotteries = document.getElementsByClassName("lotteryBoost");
  let tier = document.getElementById("tierType");
  let customName = document.getElementById("customNameAccess");
  let customAvatar = document.getElementById("customAvatarAccess");

  let hasCustomName = false;
  let hasCustomAvatar = false;

  // populate artist
  let artistBlurb = JSON.parse(data.artist_blurb);
  artistname.innerHTML = data.artist_name;
  if (artistBlurb != null){
    artisttitle.innerHTML = artistBlurb.title;
    artistsource.innerHTML = artistBlurb.source;
  }

  name.textContent = data.name;
  let imageAsset = new Image();
  imageAsset.onload = function(){
    imageDiv.innerHTML = "<img src='"+data.image+"' />";
  }
  imageAsset.src = data.image;
  description.textContent = data.description;
  // utilities.textContent = data.utilities;
  utilities.innerHTML = "<span><span style = \"font-weight: bold;\">What is an ATTRIBUTE?:</span> When your GENESIS NFT is minted, your Character will be assigned random attribute bonuses, across 5 possible attributes; SERGE, MIGHT, RESILIENCE, WIT and LUCK. Each Attribute will have a bonus value between 1 to 100.</span><br><br>\
              <span><span style = \"font-weight: bold;\">How many ATTRIBUTE Bonuses?</span> A Rarer GENESIS NFT will get more randomly generated Attribute bonuses than a common GENESIS NFT (1 for a Common, 2 for an Uncommon, 3 for a Rare, 4 for an Ultra-Rare and all 5 for a Secret-Rare!). This means that a Common NFT will pick one Attribute at random to allocate your score to, whereas a Secret-Rare will have scores in all 5 Attributes.</span><br><br>\
              <span><span style = \"font-weight: bold;\">Utility:</span> The Attribute scores will directly correlate with increased power, buffs and features in JEDSTAR Games! Scroll down to see what these effects might be.</span>"
  collectionname.innerHTML = "<span class='color-blue'>" + data.collection + "</span> collection";

  //for price display
  //provide a default
  respec_price.innerHTML = pricing_matrix[currDropdown.value];
  //hook into balance updates
  currencyDropdown.addEventListener("change", function(){
    respec_price.innerHTML = pricing_matrix[currDropdown.value];
  });

  //for respec countdown
  let gen_time = new Date(data.last_generation_time * 1000);
  let respec_time = new Date(((data.last_generation_time*1) + 900) * 1000); //gen_time.addDays(30);
  let countdown_respec = function(){
    let now = new Date();
    if (respec_time - now > 0){
      if(btn_respec.classList.contains("enabled")) btn_respec.classList.remove("enabled");
      btn_respec.classList.add("disabled");
      let when = now.time_until(respec_time);
      respec_available_in.innerHTML = "Available in ";
      if (when.days > 0) respec_available_in.innerHTML += when.days + " days, ";
      if (when.hours > 0) respec_available_in.innerHTML += when.hours + " hours, ";
      if (when.mins > 0) respec_available_in.innerHTML += when.mins + " mins, ";
      respec_available_in.innerHTML += when.secs + " secs ";
      setTimeout(countdown_respec, 1000);
    }else{
      respec_available_in.innerHTML = "Validating...";
      chainRead.nft_owner(data.tokenId).then(function(owner_addr){
        if (Agora._credentials.active
        && owner_addr.toLowerCase() == Agora._credentials.wallet.toLowerCase()){
          respec_available_in.innerHTML = "Available Now";
          if(btn_respec.classList.contains("disabled")) btn_respec.classList.remove("disabled");
          btn_respec.classList.add("enabled");
          btn_respec.addEventListener("click", function(){
            if (Agora._credentials.active
              && typeof pricing_matrix[currDropdown.value] != "undefined"
              && typeof Agora.balances[currDropdown.value] != "undefined"
              && Agora.balances[currDropdown.value] >= pricing_matrix[currDropdown.value]){
                blockingModal(true, "Please sign the regen request in your wallet");
                servercomms.regen_request(
                  Agora._credentials.wallet, Agora._credentials.auth_token,
                  data.name,
                  data.productId,
                  currDropdown.value,
                  pricing_matrix[currDropdown.value],
                  data.tokenId
                ).then(function(res){
                  Agora.refresh_balances();
                  blockingModal(true, "Refreshing your NFT...");
                  setTimeout(function(){
                    window.location.reload();
                  }, 3000);
                }).catch(function(err){
                  console.log(err);
                  blockingModal(false);
                  showToast("There was an error trying to regenerate your NFT. Please try again.", "bad");
                });
              }else if (!Agora._credentials.active){
                showToast("Please login to purchase", "bad");
                document.getElementById("connect_btn").click();
              }else{
                showToast("Insufficient balance. Please deposit.", "bad");
                document.getElementById("mycredit").click();
              }
            });
          }else{
            respec_available_in.innerHTML = "The owner can regen this NFT";
            if(btn_respec.classList.contains("disabled")) btn_respec.classList.remove("disabled");
            btn_respec.classList.add("enabled");
          }
      }).catch(function(err){
        showToast("Was not able to retrieve the details of the NFT owner! Regen will not be available.", "bad");
        respec_available_in.innerHTML = "Please reload the page";
      });
    }
  }
  countdown_respec();


  respec_desc.innerHTML = "Every NFT can be \"Respecced\" in order to re-randomize the Attribute bonuses you have on your GENESIS Card. Check the \"Requirements & Rules\" below to learn more about how this process works.";

  // Get Character Traits
  let attr_checksum = 0;
  for (let trait of Object.keys(data.character_traits)){
    attr_checksum += (data.character_traits[trait]);
    traits.push(trait);
    traits[trait] = (data.character_traits[trait]);
  }

  for (let boost of Object.keys(data.agora)){
    boosts.push(boost);
    boosts[boost] = data.agora[boost];
  }

  // Assigning each trait_type into its respective arrays
  // let attr_checksum = 0;
  for (let attribute of data.attributes){
    // if (typeof attribute.max_value != "undefined") {
    //   attr_checksum += (attribute.value*1);
    //   traits.push(attribute);
    // }else if (typeof attribute.display_type != "undefined") {
    //   boosts.push(attribute);
    // }else
    if (attribute.trait_type === "Rarity"){
      rarity_type = attribute.value;
      rarity.innerHTML = rarity_type;
    }else{
      bonuses.push(attribute);
    }
  }

  // enable the tier boost attribute
  tier.parentNode.parentNode.parentNode.classList.remove("off");


  switch(rarity_type){
    case "Secret rare":
      rarity.classList.add("secretrare");
      tier.innerHTML = "Diamond";
      document.getElementById("highRoller").classList.remove("off");
      hasCustomAvatar = true;
      hasCustomName = true;
      break;
    case "Ultra rare":
      rarity.classList.add("ultrarare");
      tier.innerHTML = "Platinum";
      hasCustomAvatar = true;
      hasCustomName = true;
      break;
    case "Rare":
      rarity.classList.add("rare");
      tier.innerHTML = "Gold";
      hasCustomAvatar = true;
      break;
    case "Uncommon":
      rarity.classList.add("uncommon");
      tier.innerHTML = "Silver";
      break;
    case "Common":
      rarity.classList.add("common");
      tier.parentNode.parentNode.parentNode.classList.add("off");
      tier.parentNode.innerHTML = "Not available for Common NFTs";
      break;
    case "Standard":
      rarity.classList.add("common");
      tier.innerHTML = "Not available for Standard NFTs";
      tier.parentNode.parentNode.parentNode.classList.add("on");
      break;
  }

  // Populate the attribute numbers
  //let attributes_values = attributes.getElementsByClassName("attr-val");
  if (attr_checksum  < 1 && rarity_type != "Standard"){
    showToast("Your NFT is still being created! This page will automatically check for updates!", "good");
    //the VRF has not been written yet
    let curr_traits = document.getElementsByClassName("attr-bar");
    if (!spec_anim){
      spec_anim = setInterval(function(){
        let r = Math.floor(Math.random()*curr_traits.length);
        let p = Math.round(Math.random()*90);
        curr_traits[r].childNodes[0].style.width = p+"%";
      }, 250);
    }
    setTimeout(function(){ retrieveData(token); },7500);
  }else{
    clearInterval(spec_anim);
    for (let i = 0; i < traits.length; i++){
      let t = document.querySelector("#"+traits[i]);

      if(traits[traits[i]] > 0){
				t.childNodes[0].childNodes[0].style.width = "calc("+traits[traits[i]]+"% - 2vw)";
        t.childNodes[1].innerHTML = traits[traits[i]];
      }else{
        t.classList.add("disabled");
      }
    }
  }

  // Populate the boost percentage numbers
  let SVLT = [], SVWS = [];
  for (let boost of boosts) {
    switch(boost) {
      case "SVBB":
        bid.innerHTML = boosts[boost];
        document.getElementById("boost-bid").textContent = boosts[boost] + "%";
        document.getElementById("boost-bid").parentNode.parentNode.parentNode.classList.remove("off");
        break;
      case "SVMB":
        mining.innerHTML = boosts[boost];
        document.getElementById("boost-mining").textContent = boosts[boost] + "%";
        document.getElementById("boost-mining").parentNode.parentNode.parentNode.classList.remove("off");
        break;
      case "SVRB":
        referral.innerHTML = boosts[boost];
        document.getElementById("boost-referral").textContent = boosts[boost] + "%";
        document.getElementById("boost-referral").parentNode.parentNode.parentNode.classList.remove("off");
        break;
    }
    if (boost.includes("SVLT")){
      SVLT.push(boosts[boost]);
    }else if (boost.includes("SVWS")){
      SVWS.push(boosts[boost]);
    }
  }

  // Populate the bonus percentage numbers
  if (SVWS[0] != undefined){
    document.getElementById("boost-wheel").parentNode.parentNode.parentNode.classList.remove("off");
    for(let i = 0; i < wheels.length; i++){
      if (SVWS[i]){
        wheels[i].innerHTML = SVWS[i];
      }
    }
  }else{
    document.getElementById("boost-wheel-pricing").innerHTML = "No special pricing";
  }

  if (SVLT[0] != undefined){
    document.getElementById("boost-lottery").parentNode.parentNode.parentNode.classList.remove("off");
    for(let i = 0; i < lotteries.length; i++){
      if (SVLT[i]){
        lotteries[i].innerHTML = SVLT[i];
      }
    }
  }else{
    document.getElementById("boost-lottery-pricing").innerHTML = "No special pricing";
  }

  // populate custom avatar and name
  if (hasCustomAvatar){
    customAvatar.innerHTML = "Yes";
    customAvatar.parentNode.parentNode.parentNode.classList.remove("off");
  }else{
    customAvatar.innerHTML = "No";
  }

  if (hasCustomName){
    customName.innerHTML = "Yes";
    customName.parentNode.parentNode.parentNode.classList.remove("off");
  }else{
    customName.innerHTML = "No";
  }
  animFadeIn([name, rarity, imageDiv, description, utilities, artistname, artisttitle, artistsource, collectionname, respec_price, respec_available_in]);
}
