//To enable wallet connect, an infuraID is needed, so this should be set first
//KV.set_infuraID(YOUR_INFURA_ID);
//The wallet connection system can be then be initialised, and other dependency scripts can be added
var wallet;

//global variables required for ops
var Settings = {
/* === PRODUCTION == * /
  chain_code: KV.rpc_codes.MATIC_MAINNET,
  NFTJsonAPI: "https://nft.jedstar.com/token/",
  NFTContract: "0x0b60eab12b8d2b4bf3be0eaa11c5ede360d4527a", //"0xc830a96A3358A717540562B577bCEb0361951975",
  JGct: "0x1C50E72b9b7a44bf7e63FE7735d67d82c3E4bF74", //"0x31fD6c3AcA42f0563F9dF47188C7Cd4a5585E3B4",
  JDct: "0x46ADEB6D42CbA55bDF8cEa91991D8dde63144402", //"0xf682B9A63025937bcD1e5FE663aba23127E761c2",
  JNct: "0x6F093CBcB46CE6840e18f62Ed263D05853Fb6E83",
  JEDct: "0x058a7Af19BdB63411d0a84e79E3312610D7fa90c",
  JEDNFTMinterCt: "0xAE8dfAa1b6BeeF57e1B87ADb7f762c3F89193C43", //"0xae955AC42708Ba7647C15dbde83eFC5eAE22Ff16",
// */

/* === DEVELOPMENT == * /
  chain_code: KV.rpc_codes.MATIC_MUMBAI,
  NFTJsonAPI: "http://localhost:8888/nft-info.php?tokenId=", //"https://dev01nft.jedstar.app/token/",
  NFTContract: "0x5a0A8a2469bD31eB0dee45412A37f6bC0622EdE7", //"0xc830a96A3358A717540562B577bCEb0361951975",
  JGct: "0xA5ee0dCa36b4Aa50cE09c143915DEe3D24d2473F", //"0x31fD6c3AcA42f0563F9dF47188C7Cd4a5585E3B4",
  JDct: "0x2b7FD7ebaD3dC9252e1C7d0F150eDDf0f3cb5fb5", //"0xf682B9A63025937bcD1e5FE663aba23127E761c2",
  JNct: "0x6f1113B4e32b9107199e8Eae5167B2374207928C",
  JEDct: "0x058a7Af19BdB63411d0a84e79E3312610D7fa90c",
  JEDNFTMinterCt: "0x7c254e529baaaC6f3601b3391520868b27ad33d0", //"0xae955AC42708Ba7647C15dbde83eFC5eAE22Ff16",
// */

/* === NEW DEVELOPMENT == */
  chain_code: KV.rpc_codes.MATIC_MUMBAI,
  NFTJsonAPI: "https://dev01nft.jedstar.app/token/",
  NFTContract: "0xC3642db3350c7A434D4A144c0C043ad05f10b686", //"0xc830a96A3358A717540562B577bCEb0361951975",
  JGct: "0x4eecc6dB31e0Ef58c1F26850712314c2A74b24c8", //"0x31fD6c3AcA42f0563F9dF47188C7Cd4a5585E3B4",
  JDct: "0x4012Bdbc4A152108acA039cFE8621eb3ebBe944E", //"0xf682B9A63025937bcD1e5FE663aba23127E761c2",
  JNct: "0x6f1113B4e32b9107199e8Eae5167B2374207928C",
  JEDct: "0x058a7Af19BdB63411d0a84e79E3312610D7fa90c",
  JEDNFTMinterCt: "0x3aD4fcaEC2e60Cf306e663D5A2380B3Cd9712DB9", //"0xae955AC42708Ba7647C15dbde83eFC5eAE22Ff16",
// */

  currencies: {
    "USDT": {contract: "0x55d398326f99059fF775485246999027B3197955"},
    "DAI": {contract: "0x1AF3F329e8BE154074D8769D1FFa4eE058B1DBc3"},
    "BUSD": {contract: "0xe9e7CEA3DedcA5984780Bafc599bD69ADd087D56"},
    "USDC": {contract: "0x8AC76a51cc950d9822D68b83fE1Ad97B32Cd580d"},
    "KREDT": {contract: "0x071549f11ade1044d338A66ABA6fA1903684Bec9"},
    //["KRED", "0xeA79d3a3a123C311939141106b0A9B1a5623696f"]
  },
  _loaded: function(ct){
    Settings._loadedcts[ct] = true;
  },
  _loadedcts: {}
};
//define page UI functions
var auth_token;
var UI = {
  update_payment_options: function(currencies){

  },

};
var reenableButtonIn = function (secs, target, text, wrapperDiv){
  if (secs > 0){
    target.disabled = true;
    target.innerHTML = (typeof wrapperDiv == "string" ? "<div class='"+wrapperDiv+"'>" : "") + text + " ("+secs+")" + (typeof wrapperDiv == "string" ? "</div>" : "");
    setTimeout(function(){
      reenableButtonIn(secs-1, target, text, wrapperDiv);
    }, 1000);
  }else{
    target.disabled = false;
    target.innerHTML = (typeof wrapperDiv == "string" ? "<div class='"+wrapperDiv+"'>" : "") + text + (typeof wrapperDiv == "string" ? "</div>" : "");
  }
};
var Agora = {
  _event: {},
  _queue: {},
  _hook: {},
  balances: {},
  hook: function(event, fn){
    if (!Array.isArray(Agora._hook[event])){
      Agora._hook[event] = [];
    }
    Agora._hook[event].push(fn);
  },
  on: function(event){
    return new Promise((resolve, reject) =>{
      if (Agora._event[event]){
        resolve();
      }else{
        if (!Array.isArray(Agora._queue[event])){
          Agora._queue[event] = [];
        }
        Agora._queue[event].push(resolve);
      }
    });
  },
  _process_queue: function(event, params){
    if (Array.isArray(Agora._queue[event])){
      while (Agora._queue[event].length > 0){
        Agora._queue[event].pop()(params);
      }
    }
  },
  _process_hooks: function(event, params){
    if (Array.isArray(Agora._hook[event])){
      for (let i = 0; i < Agora._hook[event].length; i++){
        (function(fn){ fn(params); })(Agora._hook[event][i])
      }
    }
  },
  confirm_login: function(wallet, token){
    Agora._credentials.wallet = wallet;
    Agora._credentials.auth_token = token;
    Agora._credentials.active = true;
    localStorage.setItem("agora_credentials", JSON.stringify(Agora._credentials));
    Agora._event["login"] = true;
    Agora._process_queue("login");
    Agora._process_hooks("login",wallet);
  },
  _credentials: {},
  declare_ready: function(){
    Agora._event["ready"] = true;
    //*** DOM specific functionality enablement ***
    document.getElementById("btn_refresh_balances").addEventListener("click", function(){
      this.disabled = true;
      this.innerHTML = "<div>Refreshing</div>";
      let thi = this;
      Agora.refresh_balances().then(function(res){
        reenableButtonIn(60, thi, "Refresh Balances", "balinfo");
      }).catch(function(err){
        console.error(err);
        thi.disabled = false;
        thi.innerHTML = "<div>Refresh Balances</div>";
      });
    });
    //**************
    Agora._process_queue("ready");
  },
  refresh_balances: function(){
    return new Promise((resolve, reject)=>{
      servercomms.get_balances(Agora._credentials.wallet, Agora._credentials.auth_token).then(function(walletbal_res){
        if (walletbal_res.status == "ok"){
          let walletui = document.getElementById("walletcreditentries");
          let walletnobalmsg = document.getElementById("walletcreditnone");
          //clear balances due incase there is any data left over from a previous account
          walletui.innerHTML = "";
          walletnobalmsg.classList.remove("hidden");
          let balances = false;
          for (let balelem in walletbal_res.balances){
            if (walletbal_res.balances.hasOwnProperty(balelem)){
              walletui.innerHTML += "<div class='mycredit-points'><div class='mycredit-point capitalise'>"+balelem+" "+(Math.floor(walletbal_res.balances[balelem]*100000)/100000)+"</div><div class='mycredit-withdraw'><button class='capitalise mycredit-withdraw-btn' data-curr='"+balelem+"'><div>Withdraw</div></button></div></div>";
              balances = true;
            }
          }
          if (balances){
            walletnobalmsg.classList.add("hidden");
          }

          Agora.balances = walletbal_res.balances;
          Agora._event["balances"] = true;
          Agora._process_queue("balances", Agora.balances);
          Agora._process_hooks("balances", Agora.balances);
          resolve(walletbal_res);
        }else{
          showToast("There was an error retrieving your balance. Your authentication token may have expired. Please log in again.", "bad");
          reject(res.status);
        }
      }).catch(function(err){
        // showToast("There was a problem retrieving your account balances. This may affect your ability to use Agora. Please reload and try to login again.", "bad");
        reject(err);
      });
    });
  }
};
KV.set_infuraID("a869ff8a02834a1dbc4614a626dba74f");
KV.init(["/js-lib/KV.WalletUIHandler.js", "/js-lib/KV.ContractFns.js", "/js-lib/walletconnect_1.7.1.min.js", "/js-lib/web3_3.0.0-rc.5.min.js"],
true).then(function(res){
  var preferredChain = Settings.chain_code;
  if (res["wallet"] == "ok"){
    preferredChain = localStorage.getItem("chainId") * 1;
  }
  let walletui = new KV.WalletUIHandler({
    parent_container: document.getElementById("walletmodal"),
    btn_connect: document.getElementById("connect_btn"),
    modal_connect_headline: "<span class='color-blue'>Connect</span> your wallet",
    btn_disconnect_label: "<div><img src='/img/agora/wallet.svg'> <span id='walletaddr'>Disconnect</span>",
    web3network: preferredChain,
    wallet_ready: res.wallet
  });
  walletui.on("btnconnect_clicked", function(activity_when){
    console.log("btnconnect", activity_when);
  });
  walletui.on("modal_open", function(msg){
    //add ML to achieve styling goals
    walletBtns = document.getElementsByClassName("kvwalletbtn");
    for (let i = 0; i < walletBtns.length; i++){
      if (walletBtns[i].innerHTML == "Binance"){
        walletBtns[i].innerHTML = "<div class='inner'><div class='wallet-name'>"+walletBtns[i].innerHTML+"</div><div class='wallet-icon'><img src='/img/agora/"+ walletBtns[i].innerHTML +".svg'></div></div>";
      }
      walletBtns[i].innerHTML = "<div class='inner'><div class='wallet-name'>"+walletBtns[i].innerHTML+"</div><div class='wallet-icon'><img src='/img/agora/"+ walletBtns[i].innerHTML +".png'></div></div>";
    }
    document.querySelector(".kvwalletmodal h2").insertAdjacentHTML("afterend", "<div class='rainbow-border'></div>");
  });
  walletui.on("modal_closed", function(msg){
    console.log("modal closed", msg);
  });
  walletui.on("wallet_connecting", function(msg){
    console.log("connecting", msg);
  });
  walletui.on("wallet_connected", function(msg){
    let dombody = document.getElementsByTagName("body")[0];
    dombody.classList.add("connected");
    if (dombody.classList.contains("modal-yourwallet")){
      dombody.classList.remove("modalOpen");
      dombody.classList.remove("modal-yourwallet");
    }
    document.getElementById('walletaddr').innerHTML = msg[0].substr(0,5)+"..."+msg[0].substr(-4);
    //Start connecting to contracts that require authorisation
    KV.ContractFns.prepare_contract({
      short_name: "JedstarBaseNFT",
      contract_address: Settings.NFTContract,
      contract_abi: "/abi/JedstarBaseNFT.json"
    }).then(function(res){
      Settings._loaded("JedstarBaseNFT");
    });
    KV.ContractFns.prepare_contract({
      short_name: "JedstarDelegation",
      contract_address: Settings.JDct,
      contract_abi: "/abi/JedstarDelegation.json"
    }).then(function(res){
      Settings._loaded("JedstarDelegation");
    });
    KV.ContractFns.prepare_contract({
      short_name: "JedstarNFTMinter",
      contract_address: Settings.JEDNFTMinterCt,
      contract_abi: "/abi/JedstarNFTMinter.json"
    }).then(function(res){
      Settings._loaded("JedstarNFTMinter");
    });
    let login_fn = function(wallet){
      servercomms.login(wallet).then(function(res){
        if (res.status == "ok"){
          Agora.confirm_login(msg[0], res.token);
          showToast("You are now logged in", "good");
          Agora.refresh_balances();
        }else{
          showToast("Your login failed.", "bad");
          document.getElementById("connect_btn").click();
        }
      }).catch(function(err){
        showToast("It was not possible to log you in. Please try again.", "bad");
        document.getElementById("connect_btn").click();
      });
    };
    let stored_creds = JSON.parse(localStorage.getItem("agora_credentials"));
    let bypass_login = false;
    if (stored_creds && stored_creds.active && stored_creds.wallet == msg[0]){
      Agora.confirm_login(stored_creds.wallet, stored_creds.auth_token);
      Agora.refresh_balances().then(function(res){
        if (res.status != "ok"){
          login_fn(msg[0]);
        }
      }).catch(function(err){
        login_fn(msg[0]);
      });
    }else{
      login_fn(msg[0]);
    }
  });
  walletui.on("wallet_error", function(msg){
    console.log("wallet err", msg);
  });
  walletui.on("wallet_disconnected", function(msg){
    console.log("wallet disconnected", msg);
    Agora._process_hooks("logout", null);
    document.getElementsByTagName("body")[0].classList.remove("connected");
    showToast("Your wallet "+Agora._credentials.wallet.substr(0,5)+"..."+Agora._credentials.wallet.substr(0,-4)+" has been disconnected", "bad");
    servercomms.logout(Agora._credentials.wallet, Agora._credentials.auth_token);
    Agora._credentials = {};
  });
  Agora.hook("balances", function(){
    document.querySelectorAll("button.mycredit-withdraw-btn").forEach( button => {
      button.addEventListener("click", function(){
        this.disabled = true;
        let thi = this;
        let ticker = this.dataset["curr"];
        let tstamp = new Date().getTime();
        let nonce = Math.round(Math.random()*100000);
        //modal screen prompting user to sign message
        blockingModal(true, "Please sign the login prompt in your wallet");

        KV.wallet.web3().eth.personal.sign("You are requesting to withdraw the full balance of funds for the currency "+ticker+" from Agora. You understand that these funds will be transferred back to the address they came from, on the most appropriate network, and that network gas fees will be borne by you.\n\nWithdrawals can take up to 72 hours, however your balance will still be usable in Agora until it is sent to you on the blockchain.\n\nUnique reference: "+tstamp+"-"+nonce, Agora._credentials.wallet).then(function(usersig){
          blockingModal(true, "Requesting withdrawal");
          thi.innerHTML = "<div>Requesting</div>";
          ajax({
            action: "withdraw",
            wallet: Agora._credentials.wallet,
            token: Agora._credentials.auth_token,
            signature: usersig,
            tstamp: tstamp,
            nonce: nonce,
            ticker: ticker
          }).then(function(cnf){
            blockingModal(false);
            if (cnf.status == "ok"){
              showToast("Your withdrawal request has been received! Please note that it can take up to 72 hours to process. Your balance is usable in Agora until it is sent to you on the blockchain", "good");
              thi.innerHTML = "<div>Requested</div>";
            }else{
              showToast("There was an error requesting for your withdrawal. The server said: ".cnf.debug,"\n\nIf you are unable to resolve this, please contact support immediately.", "bad");
              thi.disabled = false;
            }
          }).catch(function(err){
            blockingModal(false);
            showToast("The request for withdrawal failed. Please check your connection and login status and try again.", "bad");
            thi.disabled = false;
          });
        }).catch(function(err){
          blockingModal(false);
          showToast("It was not possible to capture your signature for the request. Please try again if you wish to withdraw.", "bad");
          thi.disabled = false;
        });
      });
    });
    servercomms.get_deposit_history(Agora._credentials.wallet, Agora._credentials.auth_token).then(function(deposit_tx){
      servercomms.get_spend_history(Agora._credentials.wallet, Agora._credentials.auth_token).then(function(spend_tx){
        let tx_list = [];
        for (let d=0; d < deposit_tx.tx.length; d++){
          tx_list.push({
            ticker: deposit_tx.tx[d]["ticker"],
            value: deposit_tx.tx[d]["human_value"],
            time: new Date(deposit_tx.tx[d]["block_time"].replace(" ","T")+".000Z"),
            description: KV.network_humannames[deposit_tx.tx[d]["network_id"]],
            type: "deposit"
          });
        }
        for (let s=0; s < spend_tx.tx.length; s++){
          tx_list.push({
            ticker: spend_tx.tx[s]["ticker"],
            value: spend_tx.tx[s]["human_value"],
            time: new Date(spend_tx.tx[s]["createdwhen"].replace(" ","T")+".000Z"),
            description: spend_tx.tx[s]["description"],
            type: "spend"
          });
        }
        tx_list.sort(function (a,b){
          return a.time - b.time;
        });
        let wallet_tx_ui = document.getElementById("wallettxlist");
        let ml = "";
        for (let a=0; a < tx_list.length; a++){
          let d = tx_list[a].time;
          ml += "<div class='depositcredit-points "+tx_list[a].type+"'><div class='depositcredit-point capitalise'>"+tx_list[a].ticker+" "+(Math.floor(tx_list[a].value*10000)/10000)+"<br/>"+tx_list[a].description+"</div><div class='depositcredit-date capitalise'>"+d.getTime12()+" "+d.toDateString()+"</div></div>";
        }
        wallet_tx_ui.innerHTML = ml;
        wallet_tx_ui.classList.remove("hidden");

      }).catch(function(err){
        console.error("Unable to retrieve spend transactions. User session may have expired?", console.error(err));
        document.getElementById("wallettxlist").classList.add("hidden");
      })
    }).catch(function(err){
      console.error("Unable to retrieve deposit transactions. User session may have expired?", console.error(err));
      document.getElementById("wallettxlist").classList.add("hidden");
    })
  });
  //The scope of the session change event is fired within the metamask extension
  /*var connect_btn = document.getElementById("connect_btn");
  KV.wallet.on_session_change(function(res){
    console.log("Disconnecting user due to session change");
    showToast("Your wallet session has changed. For your security, you have been logged out.", "bad");
    connect_btn.click();
  });*/

  //TODO put this in a real var
  servercomms = {};
  servercomms.login = function(walletId){
    return new Promise((resolve, reject)=>{
      let mynonce = Math.round(Math.random()*10000);
      let tstamp = new Date().getTime();

      //clear balances due incase there is any data left over from a previous account
      document.getElementById("walletcreditentries").innerHTML = "";
      document.getElementById("walletcreditnone").classList.remove("hidden");
      //modal screen prompting user to sign message
      blockingModal(true, "Please sign the login prompt in your wallet");

      KV.wallet.web3().eth.personal.sign("You are requesting to login to your Jedstar Agora account.\n\nPlease tap the Sign button within 60 seconds to authorise this login.\n\nUnique login: "+tstamp+"-"+mynonce, walletId).then(function(usersig){
        blockingModal(true, "Logging you in");
        ajax({
          "action": "login",
          "tstamp": tstamp,
          "nonce": mynonce,
          "signature": usersig,
          "wallet": walletId
        }).then(function(res){
          blockingModal(false);
          resolve(res);
        }).catch(function(err){
          blockingModal(false);
          console.error(err);
          reject(err);
        });
      }).catch(function(sigerr){
        blockingModal(false);
        showToast("It was not possible to capture your signature for login", "bad");
        reject(sigerr);
      });
    });
  };
  servercomms.logout = function(walletId, token){
    return new Promise((resolve, reject) => {
      ajax({
        "action": "logout",
        "token": token,
        "walletID": walletId
      }).then(function(res){
        if (res.status == "ok"){
          console.log("User has been logged out, and token expired.");
          resolve();
        }else{
          console.error("Logout was not executed properly");
          reject();
        }
      }).catch(function(err){
        console.error("Issues communicating with server to logout. Likely network trouble.");
        reject();
      });
    })
  };
  servercomms.get_deposit_history = function(walletId, token){
    return new Promise((resolve, reject) => {
      ajax({
        "action": "getdeposithistory",
        "token": token,
        "walletID": walletId
      }).then(function(res){
        resolve(res);
      }).catch(function(err){
        console.log(err);
        showToast("There was an error retrieving your transactions. Please check your connection.", "bad");
        reject(err);
      });
    });
  };
  servercomms.get_spend_history = function(walletId, token){
    return new Promise((resolve, reject) => {
      ajax({
        "action": "getspendhistory",
        "token": token,
        "walletID": walletId
      }).then(function(res){
        resolve(res);
      }).catch(function(err){
        console.log(err);
        showToast("There was an error retrieving your transactions. Please check your connection.", "bad");
        reject(err);
      });
    });
  };
  servercomms.get_balances = function(walletId, token){
    return new Promise((resolve, reject) => {
      ajax({
        "action": "getaccountbalances",
        "token": token,
        "walletID": walletId
      }).then(function(res){
        resolve(res);
      }).catch(function(err){
        // showToast("There was an error retrieving your balance. Please check your connection.", "bad");
        reject(err);
      });
    });
  };
  servercomms.get_product_pricing = function(productId){
    return ajax({
      "action": "getprice",
      "productId": productId
    });
  };
  servercomms.get_nft_benefits = function(walletId){
    return ajax({
      "action": "refreshnftbenefits",
      "wallet": walletId
    });
  };
  servercomms.sign_po = function(walletId, token, item_name, product_id, ticker, value){
    return new Promise((resolve, reject)=>{
      let mynonce = Math.round(Math.random()*10000);
      let tstamp = new Date().getTime();
      KV.wallet.web3().eth.personal.sign("You are requesting to purchase an item from Jedstar Agora. This will debit your Agora balance accordingly.\n\nItem: "+item_name+"\nCost: "+ticker+" "+value+"\n\nPlease tap the Sign button within 60 seconds to authorise this purchase.\n\nUnique purchase reference: "+tstamp+"-"+mynonce, walletId).then(function(usersig){
        blockingModal(true, "Minting in progress. Do not navigate away from this page!");
        ajax({
          "action": "signpurchaseorder",
          "item_name": item_name,
          "product_id": product_id,
          "ticker": ticker,
          "value": value,
          "tstamp": tstamp,
          "nonce": mynonce,
          "wallet": walletId,
          "token": token,
          "signature": usersig
        }).then(function(res){
          resolve(res);
        }).catch(function(err){
          showToast("There was an error communicating with Agora. Please try again.", "bad");
          reject(err);
        });
      }).catch(function(err){
        showToast("It was not possible to retrieve your authorisation for the purchase. Please try again.", "bad");
        reject(err);
      });
    });
  };
  servercomms.regen_request = function (walletId, token, item_name, product_id, ticker, value, tokenId){
    return new Promise((resolve, reject)=>{
      let mynonce = Math.round(Math.random()*10000);
      let tstamp = new Date().getTime();
      KV.wallet.web3().eth.personal.sign("You are requesting to regen your NFT attributes. This will change the values of your attributes, modifying your utility boosts, and is a chargeable activity. All values are randomly generated and you can't regen them for at least another 15 minutes. This will debit your Agora balance as follows.\n\nItem: "+item_name+"\nCost: "+ticker+" "+value+"\nToken ID: "+tokenId+"\n\nPlease tap the Sign button within 60 seconds to authorise this regen.\n\nUnique purchase reference: "+tstamp+"-"+mynonce, walletId).then(function(usersig){
        blockingModal(true, "Regeneration in progress. Do not navigate away from this page!");
        ajax({
          "action": "regenrequest",
          "item_name": item_name,
          "product_id": product_id,
          "ticker": ticker,
          "value": value,
          "tstamp": tstamp,
          "nonce": mynonce,
          "wallet": walletId,
          "token": token,
          "signature": usersig,
          "tokenId": tokenId
        }).then(function(res){
          resolve(res);
        }).catch(function(err){
          showToast("There was an error communicating with Agora. Please try again.", "bad");
          reject(err);
        });
      }).catch(function(err){
        showToast("It was not possible to retrieve your authorisation for the transaction. Please try again.", "bad");
        reject(err);
      });
    });
  };
  //var
  chainRead = {
    minted_count: function(productId){
      return new Promise((resolve, reject) => {
        KV.ContractFns.when_ready("JedstarBaseNFTRO").then(function(w3ct){
          w3ct.w3contract.methods.getProductTotalMints(productId).call().then(resolve).catch(reject);
        });
      });
    },
    last_gen_time: function(tokenId){
      return new Promise((resolve, reject) => {
        KV.ContractFns.when_ready("JedstarBaseNFTRO").then(function(w3ct){
          w3ct.w3contract.methods.getItemLastGenTime(tokenId).call().then(resolve).catch(reject);
        });
      });
    },
    nfts_by_wallet: function(wallet_addr){
      return new Promise((resolve, reject) => {
        KV.ContractFns.when_ready("JedstarBaseNFTRO").then(function(w3ct){
          w3ct.w3contract.methods.getTokenIdsByWallet(wallet_addr).call().then(resolve).catch(reject);
        });
      });
    },
    nft_owner: function(tokenId){
      return new Promise((resolve, reject) => {
        KV.ContractFns.when_ready("JedstarBaseNFTRO").then(function(w3ct){
          w3ct.w3contract.methods.ownerOf(tokenId).call().then(resolve).catch(reject);
        });
      });
    },
    jed_balance: function(wallet_addr){
      return new Promise((resolve, reject) => {
        KV.ContractFns.when_ready("JED").then(function(w3ct){
          w3ct.w3contract.methods.balanceOf(wallet_addr).call().then(function(res){ resolve( res/Math.pow(10,9) );}).catch(reject);
        });
      });
    },
    get_productId_from_token: function(tokenId){
      return new Promise((resolve, reject) => {
        //These values are static, so they can be cached indefinitely
        let tokenIdMap = localStorage.getItem("tokenIdMap") != null ? JSON.parse(localStorage.getItem("tokenIdMap")) : {};
        if (typeof tokenIdMap[tokenId] != "undefined"){
          resolve(tokenIdMap[tokenId]);
        }else{
          KV.ContractFns.when_ready("JedstarNumbers").then(function(w3ct){
            w3ct.w3contract.methods.check_number(tokenId).call().then(function(res){
              //read the data in again as it may have changed
              //NOTE: JavaScript is single-threaded, so while this data may change multiple times, it will not be modified concurrently, so race conditions are not an issue with storage updates
              tokenIdMap = localStorage.getItem("tokenIdMap") != null ? JSON.parse(localStorage.getItem("tokenIdMap")) : {};
              tokenIdMap[tokenId] = res;
              localStorage.setItem("tokenIdMap", JSON.stringify(tokenIdMap));
              resolve(res);
            }).catch(reject);
          });
        }
      });
    },
    get_product_name: function(productId){
      //These values are static, so they can be cached indefinitely
      return new Promise((resolve, reject) => {
        let productIdNames = localStorage.getItem("productIdNames") != null ? JSON.parse(localStorage.getItem("productIdNames")) : {};
        if (typeof productIdNames[productId] != "undefined"){
          resolve(productIdNames[productId]);
        }else{
          KV.ContractFns.when_ready("JedstarGaming").then(function(w3ct){
            w3ct.w3contract.methods.getProductName(productId).call().then(function(res){
              productIdNames = localStorage.getItem("productIdNames") != null ? JSON.parse(localStorage.getItem("productIdNames")) : {};
              productIdNames[productId] = res;
              localStorage.setItem("productIdNames", JSON.stringify(productIdNames));
              resolve(res);
            }).catch(reject);
          });
        }
      });
    },
    get_product_tier: function(productId){
      //These values are static, so they can be cached indefinitely
      return new Promise((resolve, reject) => {
        let productIdTiers = localStorage.getItem("productIdTiers") != null ? JSON.parse(localStorage.getItem("productIdTiers")) : {};
        if (typeof productIdTiers[productId] != "undefined"){
          resolve(productIdTiers[productId]);
        }else{
          KV.ContractFns.when_ready("JedstarGaming").then(function(w3ct){
            w3ct.w3contract.methods.getProductTier(productId).call().then(function(res){
              productIdTiers = localStorage.getItem("productIdTiers") != null ? JSON.parse(localStorage.getItem("productIdTiers")) : {};
              productIdTiers[productId] = res;
              localStorage.setItem("productIdTiers", JSON.stringify(productIdTiers));
              resolve(res);
            }).catch(reject);
          });
        }
      });
    },
    get_product_groupId: function(productId){
      //These values are static, so they can be cached indefinitely
      return new Promise((resolve, reject) => {
        let productIdGroups = localStorage.getItem("productIdGroups") != null ? JSON.parse(localStorage.getItem("productIdGroups")) : {};
        if (typeof productIdGroups[productId] != "undefined"){
          resolve(productIdGroups[productId]);
        }else{
          KV.ContractFns.when_ready("JedstarGaming").then(function(w3ct){
            w3ct.w3contract.methods.getProductGroupId(productId).call().then(function(res){
              productIdGroups = localStorage.getItem("productIdGroups") != null ? JSON.parse(localStorage.getItem("productIdGroups")) : {};
              productIdGroups[productId] = res;
              localStorage.setItem("productIdGroups", JSON.stringify(productIdGroups));
              resolve(res);
            }).catch(reject);
          });
        }
      });
    },
    get_mint_limits: function(productId){
      //These values are static, so they can be cached indefinitely
      return new Promise((resolve, reject) => {
        let productIdLimits = localStorage.getItem("productIdLimits") != null ? JSON.parse(localStorage.getItem("productIdLimits")) : {};
        if (typeof productIdLimits[productId] != "undefined"){
          resolve(productIdLimits[productId]);
        }else{
          KV.ContractFns.when_ready("JedstarGaming").then(function(w3ct){
            w3ct.w3contract.methods.getProductMintLimits(productId).call().then(function(res){
              productIdLimits = localStorage.getItem("productIdLimits") != null ? JSON.parse(localStorage.getItem("productIdLimits")) : {};
              productIdLimits[productId] = res;
              localStorage.setItem("productIdLimits", JSON.stringify(productIdLimits));
              resolve(res);
            }).catch(reject);
          });
        }
      });
    },
    get_group_name: function(groupId){
      //These values are static, so they can be cached indefinitely
      return new Promise((resolve, reject) => {
        let groupIdName = localStorage.getItem("groupIdName") != null ? JSON.parse(localStorage.getItem("groupIdName")) : {};
        if (typeof groupIdName[groupId] != "undefined"){
          resolve(groupIdName[groupId]);
        }else{
          KV.ContractFns.when_ready("JedstarGaming").then(function(w3ct){
            w3ct.w3contract.methods.getGroupName(groupId).call().then(function(res){
              groupIdName = localStorage.getItem("groupIdName") != null ? JSON.parse(localStorage.getItem("groupIdName")) : {};
              groupIdName[groupId] = res;
              localStorage.setItem("groupIdName", JSON.stringify(groupIdName));
              resolve(res);
            }).catch(reject);
          });
        }
      });
    },

    _loaded: 0,
    _isLoaded: false
  };
  KV.ContractFns.prepare_contract({
    short_name: "JedstarBaseNFTRO",
    contract_address: Settings.NFTContract,
    contract_abi: "/abi/JedstarBaseNFT.json",
    readonly_from_chain_id: Settings.chain_code
  }).then(function(res){
    Settings._loaded("JedstarBaseNFTRO");
  });
  KV.ContractFns.prepare_contract({
    short_name: "JedstarNumbers",
    contract_address: Settings.JNct,
    contract_abi: "/abi/JNumbers.json",
    readonly_from_chain_id: Settings.chain_code
  }).then(function(res){
    Settings._loaded("JedstarNumbers");
  });
  KV.ContractFns.prepare_contract({
    short_name: "JED",
    contract_address: Settings.JEDct,
    contract_abi: "/abi/JED.json",
    readonly_from_chain_id: KV.rpc_codes["BSC_MAINNET"]
  }).then(function(res){
    Settings._loaded("JED");
  });
  KV.ContractFns.prepare_contract({
    short_name: "JedstarGaming",
    contract_address: Settings.JGct,
    contract_abi: "/abi/JedstarGaming.json",
    readonly_from_chain_id: Settings.chain_code
  }).then(function(res){
    Settings._loaded("JedstarGaming");
  });
  Agora.declare_ready();

}).catch(function(err){
  console.error(err);
});
