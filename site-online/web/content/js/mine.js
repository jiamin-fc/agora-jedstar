
// var data = {
//   "status":"ok",
//   "benefits":{
//     "SVBB":{
//       "value":3,
//       "provider":[
//         "57896044618658097711785492504343953926634992332820282019728792003956564819969"],
//         "best":"h"
//       },
//     "SVCA":{
//       "value":[
//         "tier1","tier5","tier3","tier4","tier2"
//       ],
//       "provider":[
//         "57896044618658097711785492504343953926634992332820282019728792003956564819969",
//         "57896044618658097711785492504343953932419792570476235898606160330296624414722",
//         "57896044618658097711785492504343953927315557066662158946655541218820101242900",
//         "57896044618658097711785492504343953926975274699741220483192166611388333031445",
//         "57896044618658097711785492504343953930718380735871543581289287293137783357733"],
//         "best":"a"
//       },
//     "SVCN":{
//       "value":true,
//       "provider":[
//         "57896044618658097711785492504343953926634992332820282019728792003956564819969",
//         "57896044618658097711785492504343953932419792570476235898606160330296624414722",
//         "57896044618658097711785492504343953927315557066662158946655541218820101242900",
//         "57896044618658097711785492504343953926975274699741220483192166611388333031445",
//         "57896044618658097711785492504343953930718380735871543581289287293137783357733",
//         "57896044618658097711785492504343953932760074937397174362069534937728392626480",
//         "57896044618658097711785492504343953934121204405080928215923033367455465472308"],
//         "best":"h"
//       },
//     "SVHR":{
//       "value":true,
//       "provider":[
//         "57896044618658097711785492504343953926634992332820282019728792003956564819969",
//         "57896044618658097711785492504343953929697533635108728190899163470842478723364"],
//         "best":"h"
//       },
//     "SVMB":{
//       "value":9,
//       "provider":[
//         "57896044618658097711785492504343953926634992332820282019728792003956564819969",
//         "57896044618658097711785492504343953933440639671239051288996284152591929049394",
//         "57896044618658097711785492504343953927996121800504035873582290433683637666083"],
//         "best":"h"
//       },
//     "SVRB":{
//       "value":5,
//       "provider":[
//         "57896044618658097711785492504343953933100357304318112825532909545160160837937"],
//         "best":"h"
//       },
//     "SVLT1":{
//       "value":1,
//       "provider":[
//         "57896044618658097711785492504343953926634992332820282019728792003956564819969",
//         "57896044618658097711785492504343953930718380735871543581289287293137783357733",
//         "57896044618658097711785492504343953934121204405080928215923033367455465472308"],
//         "best":"l"
//       },
//     "SVLT2":{
//       "value":5,
//       "provider":[
//         "57896044618658097711785492504343953926634992332820282019728792003956564819969",
//         "57896044618658097711785492504343953926975274699741220483192166611388333031445",
//         "57896044618658097711785492504343953933440639671239051288996284152591929049394"],
//         "best":"l"
//       },
//     "SVLT3":{
//       "value":11,
//       "provider":[
//         "57896044618658097711785492504343953926634992332820282019728792003956564819969",
//         "57896044618658097711785492504343953929697533635108728190899163470842478723170"],
//         "best":"l"
//       },
//     "SVWS1":{
//       "value":1,
//       "provider":[
//         "57896044618658097711785492504343953926634992332820282019728792003956564819969",
//         "57896044618658097711785492504343953931398945469713420508216036508001319780449",
//         "57896044618658097711785492504343953934801769138922805142849782582319001895223",
//         "57896044618658097711785492504343953929697533635108728190899163470842478723170",
//         "57896044618658097711785492504343953930718380735871543581289287293137783357733"],
//         "best":"l"
//       },
//     "SVWS2":{
//       "value":4,
//       "provider":[
//         "57896044618658097711785492504343953926634992332820282019728792003956564819969"],
//         "best":"l"
//       },
//     "SVWS3":{
//       "value":7,
//       "provider":[
//         "57896044618658097711785492504343953926634992332820282019728792003956564819969"],
//         "best":"l"
//       }},
//       "providers":[]};

var data;
var info = {};

Agora.on("ready").then(function(){
  let body = document.querySelector("body");
  let page = document.querySelector(".myagora-page");
  let wallet_modal = document.getElementById("walletmodal");
  let card = document.querySelector(".card");
  let boosts = document.getElementById("myagora-boost");
  let collectibles = document.getElementById("myagora-collectibles");
  let userNotLogged = document.getElementById("userNotLogged");
  let info_button = document.querySelector(".info-button");
  let close_button = document.getElementsByClassName("close-button");
  let close_button_popup = document.getElementById("close-button-popup");
  let popup = document.getElementById("info-popup");
  let complete_popup = document.getElementById("complete-popup");
  let nft_popup = document.getElementById("nft-popup");
  let resync = document.getElementById("resync-button");
  let resync_popup = document.getElementById("resync-button-popup");
  let view_more_button = document.getElementById("viewMore");
  resync.addEventListener("click", function(){
    blockingModal(true, "Please wait for your NFT/SFT digital collectibles have been RE-SYNCed");
    retrieveData(Agora._credentials.wallet);
    complete_popup.classList.toggle('active');
    page.classList.toggle('active');

  });
  resync_popup.addEventListener("click", function(){
    blockingModal(true, "Please wait for your NFT/SFT digital collectibles have been RE-SYNCed");
    retrieveData(Agora._credentials.wallet);
    popup.classList.toggle('active');
    complete_popup.classList.toggle('active');
  });
  info_button.addEventListener("click", function(){
    popup.classList.toggle('active');
    page.classList.toggle('active');
  });

  view_more_button.addEventListener("click", function(){
    let more = document.getElementById("viewMoreBoost");
    if (more != undefined) {more.style.display = "contents";}
    if (view_more_button.children[0].innerHTML == "View All"){
      view_more_button.children[0].innerHTML = "View Less";
    }else{
      if (more != undefined) {more.style.display = "none";}
      view_more_button.children[0].innerHTML = "View All";
    }
  });

  for (let button of close_button){
    button.addEventListener("click", function(){
      if (popup.classList.contains('active')){
        popup.classList.toggle('active');
      }
      if (complete_popup.classList.contains('active')){
        complete_popup.classList.toggle('active');
      }
      if (nft_popup.classList.contains('active')){
        nft_popup.classList.toggle('active');
      }
      page.classList.toggle('active');
    });
  }
  close_button_popup.addEventListener("click",function(){
    close_button[0].click();
  });
  boosts.classList.add("hide");
  collectibles.classList.add("hide");
  userNotLogged.style.display = "block";
  document.getElementById("connect_btn2").addEventListener("click", function(){
    document.getElementById("connect_btn").click();
  });
});
Agora.on("login").then(function(){
  let cards = document.querySelectorAll(".card");
  let numCollectibles = document.getElementById("numCollectibles");
  let userNotLogged = document.getElementById("userNotLogged");
  let userNotLoggedHolder = document.getElementById("myagora-cards");
  let boosts = document.getElementById("myagora-boost");
  let collectibles = document.getElementById("myagora-collectibles");
  let nft_name;
  
  boosts.classList.remove("hide");
  collectibles.classList.remove("hide");
  userNotLoggedHolder.style.display = "none";
  userNotLogged.style.display = "none";
  for(let card of cards){
    card.style.display = "block";
  }

  /* Agora._credentials.wallet*/
  // retrieveData("0x7eA759BD08Aa1F4764688badaC7e6E87059c7243");
  retrieveData(Agora._credentials.wallet);
  // chainRead.nfts_by_wallet("0x7eA759BD08Aa1F4764688badaC7e6E87059c7243").then(function(res){
  chainRead.nfts_by_wallet(Agora._credentials.wallet).then(function(res){
    if(res.length === 0){
      for (let card of cards){
        card.style.display = "none";
      }
      document.getElementById("noCards").style.display = "block";
      numCollectibles.innerHTML = 0
      return;
    }
    numCollectibles.innerHTML = res.length;
    let loadingPlaceholderMl = '<div id="imageXXX" class="card-image loading">&nbsp;</div><div class="card-name"><p class="capitalise label">name</p><p class="capitalise name loading" id="nameXXX">&nbsp;</p></div><div class="card-rarity"><p id="rarityTxtXXX" class="rarityTxt capitalise loading">&nbsp;</p></div>';
    let cardsHolder = document.getElementById("cards");
    let addToCards = function(i){
      if (i >= res.length){ return; }

      /*57896044618658097711785492504343953926634992332820282019728792003956564819969*/
      let cardElem = document.createElement("DIV");
      cardElem.id = "card"+res[i];
      cardElem.dataset["cardid"] = res[i];
      cardElem.classList.add("card");
      cardElem.innerHTML = loadingPlaceholderMl.replace(/XXX/g, res[i]);
      cardElem.addEventListener("click", function(){
        window.location = "/view#"+res[i];
      });
      cardsHolder.appendChild(cardElem);

      chainRead.get_productId_from_token(res[i]).then(function(numbers){
        let imgDiv = document.getElementById("image"+res[i]);
        let imgAsset = new Image();
        imgAsset.onload = function(){
          imgDiv.innerHTML = "<img src='https://assets.jedstar.space/nft/"+numbers[0]+".jpg' />";
        };
        imgAsset.src = "https://assets.jedstar.space/nft/"+numbers[0]+".jpg";
        imgDiv.classList.remove("loading");
        imgDiv.classList.add("fade-in");

        chainRead.get_product_name(numbers[0]).then(function(prodName){
          let nameLbl = document.getElementById("name"+res[i]);
          nameLbl.innerHTML = prodName;
          nameLbl.classList.remove("loading");
          nameLbl.classList.add("fade-in");
          nft_name = prodName;
        }).catch(console.error);

        chainRead.get_product_tier(numbers[0]).then(function(tierId){
          let rarityLbl = document.getElementById("rarityTxt"+res[i]);
          let tierName, tierClass;
          tierId = tierId * 1;
          switch(tierId){
            case 0:
              tierName = "Secret rare";
              tierClass = "secretrare";
              break;
            case 1:
              tierName = "Ultra rare";
              tierClass = "ultrarare";
              break;
            case 2:
              tierName = "Rare";
              tierClass = "rare";
              break;
            case 3:
              tierName = "Uncommon";
              tierClass = "uncommon";
              break;
            case 4:
              tierName = "Common";
              tierClass = "common";
              break;
            default: //case 5:
              tierName = "Standard";
              tierClass = "common";
              break;
          }
          rarityLbl.innerHTML = tierName;
          rarityLbl.classList.add(tierClass);
          rarityLbl.classList.remove("loading");
          rarityLbl.classList.add("fade-in");
          info[res[i]] = {
            "id"        : numbers[0],
            "img"       : "https://assets.jedstar.space/nft/"+numbers[0]+".jpg",
            "name"      : nft_name,
            "rarity"    : tierName,
            "tierClass" : tierClass
          }
        }).catch(console.error);
      }).catch(console.error);

      setTimeout(function(){
        addToCards(i+1);
      }, 100);
    }
    cardsHolder.innerHTML = "";
    addToCards(0);

  }).catch(function(err){
    console.error(err);
  });
});

function retrieveData(wallet_id){
  servercomms.get_nft_benefits(wallet_id).then(function(res){
    console.log(res);
    if (res.status == "ok"){
      blockingModal(false);
      data = res;
      populateData(data.benefits);
    }else{
      blockingModal(false);
      showToast("There was an error trying to RE-SYNC your NFT/SFT. Please try again.", "bad");
    }
  }).catch(console.error);
}

function populateData(data){
   let totalBoost = 0;
   let size = Object.keys(data).length;
   let nft_detail_boosts = document.querySelector(".nft-detail-boosts");
   let view_more_button = document.getElementById("viewMore");
   let numBoost = document.getElementById("numBoost");
   
   // clear loading anim
   while(nft_detail_boosts.firstChild){
    nft_detail_boosts.removeChild(nft_detail_boosts.lastChild);
   }
   if (size === 0){
    let noBoosts = document.getElementById("noBoosts");
    let nft_detail_logo = document.querySelector(".nft-detail-logo");
    let nft_detail_logo_desc = document.querySelector(".nft-detail-logo-description");
    noBoosts.style.display = "block";
    nft_detail_logo.style.display = "none";
    nft_detail_logo_desc.style.display = "none";
   }else{
    var more = document.createElement("span");
    more.setAttribute('id', 'viewMoreBoost');
    more.style.display = "none";
    for (const [key, value] of Object.entries(data)){
      if (value.provider != "default"){
        if (totalBoost > 9){
          more.appendChild(createBoost(key, value));
          totalBoost++;
        }else{
          nft_detail_boosts.appendChild(createBoost(key, value));
          totalBoost++;
        }
      }
    }
  }
  numBoost.innerHTML = totalBoost;
  if (totalBoost <= 10){
    view_more_button.style.display = "none";
  }else{
    if (more.style.display == "none" && view_more_button.children[0].innerHTML == "View Less"){
      view_more_button.children[0].innerHTML = "View All";
    }
    nft_detail_boosts.appendChild(more);
  }
}

function populateProviders(nft){
  let cardsHolder = document.getElementById("cards-popup");
  let loadingPlaceholderMl = '<div id="popup-imageXXX" class="card-image loading">&nbsp;</div><div class="card-name"><p class="capitalise label">name</p><p class="capitalise name loading" id="popup-nameXXX">&nbsp;</p></div><div class="card-rarity"><p id="popup-rarityTxtXXX" class="rarityTxt capitalise loading">&nbsp;</p></div>';
  let providers = nft.provider;
  cardsHolder.innerHTML = "";


  for (let provider of providers){
    let nft_detes = getProvider(provider);
    if (nft_detes != undefined){
      let cardElem = document.createElement("DIV");
      cardElem.id = "popup-card"+provider;
      cardElem.dataset["cardid"] = provider;
      cardElem.classList.add("card");
      cardElem.innerHTML = loadingPlaceholderMl.replace(/XXX/g, provider);
      cardElem.addEventListener("click", function(){
        window.location = "/view#"+provider;
      });
      cardsHolder.appendChild(cardElem);
  
      let imgDiv = document.getElementById("popup-image"+provider);
      let imgAsset = new Image();
      imgAsset.onload = function(){
        imgDiv.innerHTML = "<img src='"+nft_detes.img+"' />";
      };
      imgAsset.src =nft_detes.img;
      imgDiv.classList.remove("loading");
      imgDiv.classList.add("fade-in");
      
      let nameLbl = document.getElementById("popup-name"+provider);
      nameLbl.innerHTML = nft_detes.name;
      nameLbl.classList.remove("loading");
      nameLbl.classList.add("fade-in");
  
      let rarityLbl = document.getElementById("popup-rarityTxt"+provider);
      rarityLbl.innerHTML = nft_detes.rarity;
      rarityLbl.classList.add(nft_detes.tierClass);
      rarityLbl.classList.remove("loading");
      rarityLbl.classList.add("fade-in");

    }
  }
}

function createBoost(key, val){
  let provider = getProvider(val);
  let boost = document.createElement("div");
  boost.classList.add("boost");
  boost.innerHTML = "";
  boost.innerHTML += "<div class = \"view-nft\">View</div>\n";

  if (key.includes("SVWS")) {
    let numStr;
    let num = key.substr(key.length - 1);
    switch (num){
      case "1":
        numStr = "first";
        break;
      case "2":
        numStr = "second";
        break;
      case "3":
        numStr = "third";
        break;
    }
    boost.innerHTML += "<div class=\"boost-check\"><img src=\"/img/agora/check.svg\"></div>\n";
    boost.innerHTML += "<div class=\"boost-title\"><p class=\"capitalise\"><span id=\"boost-wheel\">Wheel Spin Boost ("+ num +")</span></p></div>\n";
    boost.innerHTML += "<div class=\"boost-subtitle\"><p class=\"label\" id=\"boost-wheel-pricing\">💎<span class=\"wheelBoost\">"+ val.value +"</span> as your "+ numStr +" pricing</p></div>\n";
    boost.innerHTML += "<div class=\"boost-stat\"><p class=\"capitalise\">Wit</p></div>";
  }else if (key.includes("SVLT")){
    let numStr;
    let num = key.substr(key.length - 1);
    switch (num){
      case "1":
        numStr = "first";
        break;
      case "2":
        numStr = "second";
        break;
      case "3":
        numStr = "third";
        break;
    }
    boost.innerHTML += "<div class=\"boost-check\"><img src=\"/img/agora/check.svg\"></div>\n";
    boost.innerHTML += "<div class=\"boost-title\"><p class=\"capitalise\"><span id=\"boost-lottery\">Lottery Ticket Boost ("+ num +")</span></p></div>\n";
    boost.innerHTML += "<div class=\"boost-subtitle\"><p class=\"label\" id=\"boost-lottery-pricing\">💎<span class = \"lotteryBoost\">"+ val.value +"</span> as your "+ numStr +" pricing</p></div>\n";
    boost.innerHTML += "<div class=\"boost-stat\"><p class=\"capitalise\">Luck</p></div>";
  }else{
    switch(key){
      case "SVBB":
        boost.innerHTML += "<div class=\"boost-check\"><img src=\"/img/agora/check.svg\"></div>\n";
        boost.innerHTML += "<div class=\"boost-title\"><p class=\"capitalise\"><span id=\"boost-bid\">"+ val.value +"</span> bid boost</p></div>\n";
        boost.innerHTML += "<div class=\"boost-subtitle\"><p class=\"label\"><span id = \"numBid\">"+ val.value +"</span> Additional Bids per week.</p></div>\n";
        boost.innerHTML += "<div class=\"boost-stat\"><p class=\"capitalise\">Serge</p></div>";
        break;
      case "SVMB":
        boost.innerHTML += "<div class=\"boost-check\"><img src=\"/img/agora/check.svg\"></div>\n";
        boost.innerHTML += "<div class=\"boost-title\"><p class=\"capitalise\"><span id=\"boost-mining\">"+ val.value +"</span>% mining boost</p></div>\n";
        boost.innerHTML += "<div class=\"boost-subtitle\"><p class=\"label\"><span id = \"numMining\">"+ val.value +"</span>% Boost to VOLTS Mining Power.</p></div>\n";
        boost.innerHTML += "<div class=\"boost-stat\"><p class=\"capitalise\">Might</p></div>";
        break;
      case "SVRB":
        boost.innerHTML += "<div class=\"boost-check\"><img src=\"/img/agora/check.svg\"></div>\n";
        boost.innerHTML += "<div class=\"boost-title\"><p class=\"capitalise\"><span id=\"boost-referral\">"+ val.value +"</span>% Referral boost</p></div>\n";
        boost.innerHTML += "<div class=\"boost-subtitle\"><p class=\"label\"><span id = \"numReferral\">"+ val.value +"</span>% extra VOLTS from Referrals.</p></div>\n";
        boost.innerHTML += "<div class=\"boost-stat\"><p class=\"capitalise\">Resilience</p></div>";
        break;
      case "SVCA":
        if (val.value.length != 0){
          boost.innerHTML += "<div class=\"boost-check\"><img src=\"/img/agora/check.svg\"></div>\n";
          boost.innerHTML += "<div class=\"boost-title\"><p class=\"capitalise\">Custom avatar</p></div>\n";
          boost.innerHTML += "<div class=\"boost-subtitle\"><p class=\"label\">Access to Custom Avatar: <span id = \"customNameAccess\">Yes</span></p></div>";
        }
        break;
      case "SVCN":
        if (val.value){
          boost.innerHTML += "<div class=\"boost-check\"><img src=\"/img/agora/check.svg\"></div>\n";
          boost.innerHTML += "<div class=\"boost-title\"><p class=\"capitalise\">Custom name</p></div>\n";
          boost.innerHTML += "<div class=\"boost-subtitle\"><p class=\"label\">Access to Custom Name: <span id = \"customNameAccess\">Yes</span></p></div>";
        }else{
          boost.remove();
        }
        break;
      case "SVHR":
        if (val.value){
          boost.setAttribute('id', "highRoller");
          boost.innerHTML += "<div class=\"boost-check\"><img src=\"/img/agora/check.svg\"></div>\n";
          boost.innerHTML += "<div class=\"boost-title\"><p class=\"capitalise\">High roller access</p></div>\n";
          boost.innerHTML += "<div class=\"boost-subtitle\"><p class=\"label\">Coming Soon.</p></div>";
        }else{
          boost.remove();
        }
        break;
      case "Rarity":
        boost.innerHTML += "<div class=\"boost-check\"><img src=\"/img/agora/check.svg\"></div>\n";
        boost.innerHTML += "<div class=\"boost-title\"><p class=\"capitalise\">Tier boost</p></div>\n";
        let tier;
        switch(val.value){
          case "Secret rare":
            tier = "Diamond";
            break;
          case "Ultra rare":
            tier = "Platinum";
            break;
          case "Rare":
            tier = "Gold";
            break;
          case "Uncommon":
            tier = "Silver";
            break;
        }
        boost.innerHTML += "<div class=\"boost-subtitle\"><p class=\"label\">Access to <span id = \"tierType\">"+ tier +"</span> tier auctions.</p></div>";
        break;
    }
  }
  let nft_popup = document.getElementById("nft-popup");
  let page = document.querySelector(".myagora-page");
  boost.addEventListener("click", function(){
    nft_popup.classList.toggle("active");
    page.classList.toggle("active");
    populateProviders(val);
  });
  return boost;
}

function getProvider(token){
  for (let key in info){
    if (key == token){
      return info[key];
    }
  }
}

const back_button = document.getElementById("backButton");
back_button.addEventListener('click', function(){
history.back();
});