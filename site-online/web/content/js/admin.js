window.process_chain_result = function(tgt, res, err){
  let btn = document.getElementById(tgt+"_btn");
  let outdiv = document.getElementById(tgt+"_out");
  if (err){
    outdiv.classList.add("error");
  }
  outdiv.innerHTML = "<textarea readonly>"+JSON.stringify(res, null, 2)+"</textarea>";
};
window.prepare_field_data = function(txt){
  if (txt.substr(0,1)=="[" && txt.substr(-1) == "]"){
    //this is an array, get the innards and turn it into a string array, do not attempt to parse the numbers
    return txt.substr(1,txt.length-2).split(",");
  }else{
    return txt;
  }
};
let _loaded = {};
let enable_fns = function(){
  //get all the known chain data
  let target_cts = ["JedstarBaseNFT", "JedstarGamingRW", "JedstarNFTMinter"];
  let abis = {};
  let populateTables = function(tgt_ct){
    let new_table = "<h1>"+tgt_ct+"</h1><div class='functionContainer'>";

    for (fn in abis[tgt_ct]){
      if (abis[tgt_ct].hasOwnProperty(fn)){
        new_table += "<div class='fnBox'><span class='fnName'>"+fn+"</span><div class='fnFields'>";
        fn_exec = 'this.disable = true; this.innerHTML = "Working"; let thi = this; KV.ContractFns.cts["'+tgt_ct+'"].w3contract.methods["'+fn+'"](';
        for (let i=0; i < abis[tgt_ct][fn].inputs.length; i++){
          let field_name = tgt_ct+"_"+fn+"_input"+i;
          new_table += "<input id='"+field_name+"' placeholder='"+abis[tgt_ct][fn].inputs[i]["name"]+"' />";
          fn_exec += (i > 0 ? "," : "") + 'prepare_field_data(document.getElementById("'+field_name+'").value)';
        }
        if (abis[tgt_ct][fn].stateMutability == "view"){
          //read only function, no gas required
          fn_exec += ').call().then(function(res){thi.innerHTML = "Read"; thi.disabled=false;process_chain_result("'+tgt_ct+"_"+fn+'", res, false);}).catch(function(err){thi.innerHTML = "Read"; thi.disabled=false; process_chain_result("'+tgt_ct+"_"+fn+'", err, true);})';
        }else if (abis[tgt_ct][fn].stateMutability == "nonpayable"){
          fn_exec += ').send({from:Agora._credentials.wallet, to:KV.ContractFns.cts["'+tgt_ct+'"].contractAddress, value:0, gasPrice:"9000000"},function(err, tx){thi.innerHTML = tx; console.log("Processing with tx ref "+tx);}).then(function(res){thi.innerHTML = "Execute"; thi.disabled=false;process_chain_result("'+tgt_ct+"_"+fn+'", res, false);}).catch(function(err){thi.innerHTML = "Execute"; thi.disabled=false; process_chain_result("'+tgt_ct+"_"+fn+'", err, true);});'
        }else{
          console.error("No implementation available for payable functions yet - ref: "+tgt_ct+"_"+fn);
          fn_exec = 'alert("Implementation for payable functions not yet ready");'; //TODO - finish payable implementation
        }
        new_table += "</div><div class='fnButton'><button id='"+tgt_ct+"_"+fn+"_btn' class='executeOnChain' onClick='try{"+fn_exec+"}catch(e){ this.disabled = false; this.innerHTML=\"Try Again\"; console.error(e);process_chain_result(\""+tgt_ct+"_"+fn+"\", e, true);}'>"+(abis[tgt_ct][fn].stateMutability == "view" ? "Read" : "Execute")+"</button></div><div class='fnResults' id='"+tgt_ct+"_"+fn+"_out'></div></div>";
      }
    }

    new_table += "</div>"; //close functionContainer
    let fn_div = document.getElementById("functionsdiv");
    if (fn_div.classList.contains("empty")){
      fn_div.classList.remove("empty");
      fn_div.innerHTML = new_table;
    }else{
      fn_div.innerHTML += new_table;
    }
  };
  for (let j = 0; j < target_cts.length; j++){
    (function(tgt_ct){
      if (!_loaded[target_cts[j]]){
        _loaded[target_cts[j]] = true;
        setTimeout(function(){
          KV.ContractFns.when_ready(target_cts[j]).then(function(w3ct){
            let ct_abi = {};
            for (let i = 0; i < w3ct.w3contract._jsonInterface.length; i++){
              if (w3ct.w3contract._jsonInterface[i]["type"] == "function"){
                ct_abi[ w3ct.w3contract._jsonInterface[i]["name"] ] = w3ct.w3contract._jsonInterface[i];
              }
            }
            abis[target_cts[j]] = ct_abi;
            populateTables(tgt_ct);
          });
        }, 1000);
      }
    })(target_cts[j])
  }
  KV.ContractFns.prepare_contract({
    short_name: "JedstarGamingRW",
    contract_address: Settings.JGct,
    contract_abi: "/abi/JedstarGaming.json"
  }).then(function(res){
    Settings._loaded("JedstarGamingRW");
  });

  //activate all the buttons
  let all_btns = document.getElementsByClassName("fnbtn");
  for (let i = 0; i < all_btns.length; i++){
    all_btns[i].disabled = false;
  }
};

let disable_fns = function(){
  let all_btns = document.getElementsByClassName("fnbtn");
  for (let i = 0; i < all_btns.length; i++){
    all_btns[i].disabled = true;
  }
};

//simple UI management based on wallet connectivity
KV.wallet.on_connect(enable_fns);
KV.wallet.on_disconnect(disable_fns);

//KV.ContractFns.cts.JedstarNFTMinter.w3contract.methods.mint("0x221156ddfa256f83b21bec968688e385dac8cc81", "57896044618658097711785492504343953929016968901266851263972414255978942300160", 1, '0x0000', 250, "0x9680d16c7d20435960665ae141bcf777b50145784c54743e6093b6490fc867636cf1bc1dc2d2265f46735bfe2a89ad34cb4ee4fa1b0b0c48134424ecdc6ed88f1b").send({from:"0x7eA759BD08Aa1F4764688badaC7e6E87059c7243", to:Settings.NFTContract, value:0, gasPrice:'9000000'}, function(err, tx){console.log(tx);}).then(console.log).catch(console.error);
