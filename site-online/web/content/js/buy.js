let x = window.location.toString().split('/');
if (x[x.length-2] == "buy" || x[x.length-2] == "?c=buy"){
  let productId = x[x.length-1] * 1;
  let product_purchase_details = {};
  Agora.on("ready").then(function(){
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
    })
    const back_button = document.getElementById("backButton");
    back_button.addEventListener('click', function(){
      history.back();
    });
    chainRead.minted_count(productId).then(function(minted_qty){
      chainRead.get_mint_limits(productId).then(function(mint_limits){
        let mintedLbl = document.getElementById("mintedcount");
        if (minted_qty/mint_limits >= 0.75){
          mintedLbl.innerHTML = (mint_limits - minted_qty)+" of "+mint_limits+" remain";
        }else{
          mintedLbl.innerHTML = minted_qty + " minted";
        }
        mintedLbl.classList.remove("loading");
        mintedLbl.classList.add("fade-in");
      });
    }).catch(function(err){
      console.error(err);
    });

    chainRead.get_product_groupId(productId).then(function(groupId){
      if (groupId < 4){
        chainRead.get_group_name(groupId).then(function(group_name){
          let collectionLbl = document.getElementById("collectionName");
          collectionLbl.innerHTML = "<span class='color-blue'>"+group_name+"</span> collection";
          collectionLbl.classList.remove("loading");
          collectionLbl.classList.add("fade-in");
        }).catch(function(err){
          console.error("Group name retrieval failure", err);
        })
      }else{
        window.location = "/";
      }
    }).catch(function(err){
      console.error("Group ID retrieval failure", err);
    });

    chainRead.get_product_tier(productId).then(function(tierId){
      let rarityLbl = document.getElementById("rarity-label");
      let tierName, tierClass;
      let attributes_rarity = document.getElementById("attributes-rarity");
      let attributes_given_num = document.getElementById("attributes-given-num");
      let bid = document.getElementById("numBid");
      let mining = document.getElementById("numMining");
      let referral = document.getElementById("numReferral");
      let wheel = document.getElementById("numWheel");
      let lottery = document.getElementById("numLottery");
      let tier = document.getElementById("tierType");
      document.getElementById("attributes-given").style.color = "#1C84FE";

      let boosts = document.querySelectorAll(".boost");
      for (let boost of boosts){
        if (boost.id !== "highRoller"){
          boost.classList.remove("off");
        }
      }

      tierId = tierId * 1;
      switch(tierId){
        case 0:
          tierName = "Secret rare";
          tierClass = "secretrare";
          attributes_rarity.style.color = "#F23535";
          attributes_given_num.innerHTML = 5;
          bid.innerHTML = 5, mining.innerHTML = 10, referral.innerHTML = 4, wheel.innerHTML = 80, lottery.innerHTML = 80, tier.innerHTML = "Diamond";
          document.getElementById("highRoller").classList.remove("off");
          break;
        case 1:
          tierName = "Ultra rare";
          tierClass = "ultrarare";
          attributes_rarity.style.color = "#31FF5D";
          attributes_given_num.innerHTML = 4;
          bid.innerHTML = 4, mining.innerHTML = 8, referral.innerHTML = 4, wheel.innerHTML = 70, lottery.innerHTML = 70, tier.innerHTML = "Platinum";
          break;
        case 2:
          tierName = "Rare";
          tierClass = "rare";
          attributes_rarity.style.color = "#FFD131";
          attributes_given_num.innerHTML = 3;
          bid.innerHTML = 3, mining.innerHTML = 6, referral.innerHTML = 2, wheel.innerHTML = 60, lottery.innerHTML = 60, tier.innerHTML = "Gold";
          break;
        case 3:
          tierName = "Uncommon";
          tierClass = "uncommon";
          attributes_rarity.style.color = "#318DFF";
          attributes_given_num.innerHTML = 2;
          bid.innerHTML = 2, mining.innerHTML = 5, referral.innerHTML = 1.5, wheel.innerHTML = 50, lottery.innerHTML = 50, tier.innerHTML = "Silver";
          break;
        case 4:
          tierName = "Common";
          tierClass = "common";
          attributes_rarity.style.color = "#D9D9D9";
          attributes_given_num.innerHTML = 1;
          bid.innerHTML = 1, mining.innerHTML = 4, referral.innerHTML = 1, wheel.innerHTML = 40, lottery.innerHTML = 40, tier.innerHTML = "Not available for Common NFTs";
          break;
        default: //case 5:
          tierName = "Standard";
          tierClass = "common";
          attributes_rarity.style.color = "#D9D9D9";
          attributes_given_num.innerHTML = 1;
          bid.innerHTML = "N/A", mining.innerHTML = "N/A", referral.innerHTML = "N/A", wheel.innerHTML = "N/A", lottery.innerHTML = "N/A", tier.innerHTML = "Not available for Standard NFTs";
          break;
      }
      let attribute_rarities = document.getElementsByClassName("attributes-rarities");
      for (let attribute of attribute_rarities){
        attribute.innerHTML = tierName;
      }
      rarityLbl.innerHTML = tierName;
      attributes_rarity.innerHTML = tierName;
      rarityLbl.classList.add(tierClass);
      rarityLbl.classList.remove("loading");
      rarityLbl.classList.add("fade-in");
    }).catch(function(err){
      console.error("Tier ID retrieval failed", err);
    })

    servercomms.get_product_pricing(productId).then(function(res){
      if (res.status == "ok"){
        let priceLbl = document.getElementById("price");
        let nameLbl = document.getElementById("nftname");
        let imgNft = document.getElementById("nftimage");
        let imgAsset = new Image();
        let btn_buynow = document.getElementById("buynowsolo");
        let descriptionTxt = document.getElementById("nft-description-text");
        let utilTxt = document.getElementById("nft-utilities-text");
        let artistName = document.getElementById("artistName");
        let artistTitle = document.getElementById("artistTitle");
        let artistSource = document.getElementById("artistSource");

        let animFadeIn = function (domElemArr){
          for (let e=0; e < domElemArr.length; e++){
            domElemArr[e].classList.remove("loading");
            domElemArr[e].classList.add("fade-in");
          }
        }

        price.innerHTML = res.price["USDC"];

        nftname.innerHTML = res.name;

        imgAsset.onload = function(){
          imgNft.innerHTML = "<img src='https://assets.jedstar.space/nft/"+productId+".jpg' />";
        };
        imgAsset.src = "https://assets.jedstar.space/nft/"+productId+".jpg";

        let blurbs = JSON.parse(res.description);
        descriptionTxt.innerHTML = blurbs.description;
        // utilTxt.innerHTML = blurbs.utilities;
        utilTxt.innerHTML = "<span><span style = \"font-weight: bold;\">What is an ATTRIBUTE?:</span> When your GENESIS NFT is minted, your Character will be assigned random attribute bonuses, across 5 possible attributes; SERGE, MIGHT, RESILIENCE, WIT and LUCK. Each Attribute will have a bonus value between 1 to 100.</span><br><br>\
              <span><span style = \"font-weight: bold;\">How many ATTRIBUTE Bonuses?</span> A Rarer GENESIS NFT will get more randomly generated Attribute bonuses than a common GENESIS NFT (1 for a Common, 2 for an Uncommon, 3 for a Rare, 4 for an Ultra-Rare and all 5 for a Secret-Rare!). This means that a Common NFT will pick one Attribute at random to allocate your score to, whereas a Secret-Rare will have scores in all 5 Attributes.</span><br><br>\
              <span><span style = \"font-weight: bold;\">Utility:</span> The Attribute scores will directly correlate with increased power, buffs and features in JEDSTAR Games! Scroll down to see what these effects might be.</span>"

        let artBlurb = JSON.parse(res.artist.blurb);
        artistName.innerHTML = res.artist.name;
        artistTitle.innerHTML = artBlurb.title;
        artistSource.innerHTML = artBlurb.source;

        btn_buynow.dataset["productId"] = productId;
        product_purchase_details.pricing = res.price;
        product_purchase_details.name = res.name;

        //Fade all the text blocks in
        animFadeIn([price, nftname, imgNft, descriptionTxt, utilTxt, artistName, artistTitle, artistSource]);

        let currDropdown = document.getElementById("pricecurrency");
        let load_all_prices = function(){
          currDropdown.innerHTML = "";
          for (curr in product_purchase_details.pricing){
            if (product_purchase_details.pricing.hasOwnProperty(curr)){
              if ((curr == "AGORA" && Agora.balances["AGORA"] > 0) || (curr != "AGORA")){
                let opta = document.createElement("OPTION");
                opta.value = curr;
                opta.innerHTML = curr;
                currDropdown.appendChild(opta);
              }
            }
          }
        };
        load_all_prices();
        currDropdown.addEventListener("change", function(){
          priceLbl.innerHTML = product_purchase_details.pricing[currDropdown.value];
          if (typeof product_purchase_details.pricing[currDropdown.value] != "undefined" && Agora.balances[currDropdown.value] >= product_purchase_details.pricing[currDropdown.value]){
            btn_buynow.classList.add('enabled');
          }else{
            btn_buynow.classList.remove('enabled');
          }
        });
        Agora.hook("balances", function(balances){
          //this fires everytime the user's balance is updated
          if (Agora.balances["AGORA"] > 0 ){
            load_all_prices();
          }
          for (curr in Agora.balances){
            if (Agora.balances.hasOwnProperty(curr)){
              if (typeof product_purchase_details.pricing[curr] != "undefined" && Agora.balances[curr] >= product_purchase_details.pricing[curr]){
                for(var i=0; i<currDropdown.options.length; i++) {
                  if ( currDropdown.options[i].value == curr ) {
                    currDropdown.selectedIndex = i;
                    break;
                  }
                }
                btn_buynow.classList.add('enabled');
                break;
              }
            }
          }
          priceLbl.innerHTML = product_purchase_details.pricing[currDropdown.value];
        });
        //bind the button to the purchase process
        btn_buynow.addEventListener("click", function(){
          if (Agora._credentials.active
          && typeof product_purchase_details.pricing[currDropdown.value] != "undefined"
          && typeof Agora.balances[currDropdown.value] != "undefined"
          && Agora.balances[currDropdown.value] >= product_purchase_details.pricing[currDropdown.value]){
            //everything looks good to commence purchase
            blockingModal(true, "Please sign the purchase request to start minting");
            servercomms.sign_po(
              Agora._credentials.wallet, Agora._credentials.auth_token,
              product_purchase_details.name,
              productId,
              currDropdown.value,
              product_purchase_details.pricing[currDropdown.value]
            ).then(function(res){
              Agora.refresh_balances();
              if (res.status == "ok"){
                blockingModal(true, "Your collectable has been minted! Taking you to your Agora...");
                setTimeout(function(){
                  window.location = "/mine";
                }, 2000);
              }else{
                blockingModal(false);
                showToast("There was a problem minting your NFT. Your account has not been debited, please reload the page and try again.", "bad");
              }
            }).catch(function(err){
              blockingModal(false);
              showToast("NFT failed to mint.", "bad");
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
        console.error("Failed to retrieve pricing info");
      }
    }).catch(function(err){
      console.log(err);
    });
  });
}else{
  //This URL is incorrectly formatted
  console.error("Not proper URL");
}
