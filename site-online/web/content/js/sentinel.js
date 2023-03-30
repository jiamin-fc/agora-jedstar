(function(){
var contract_address = '0x96a0640A9D7850c5150015DDFb558db34a898bAf';
var JED, KRED, isJEDApproved = false, isKREDApproved = false;
let not_logged = document.getElementById("userNotLogged");
not_logged.style.display = 'block';
document.getElementById("connect_btn2").addEventListener("click", function(){
    document.getElementById("connect_btn").click();
});
    
let wallet_status = function(){
    console.log("getting wallet status...");
    let JEDunlockfee = document.getElementsByClassName("JEDunlockfee");
    let JEDauth = document.getElementById("JEDauth");
    let KREDunlockfee = document.getElementsByClassName("KREDunlockfee");
    let KREDauth = document.getElementById("KREDauth");
    let JEDlogo = document.getElementById("JED");
    let KREDlogo = document.getElementById("KRED");
    document.getElementById("activate-button").disabled = true;
    document.getElementById("activate-button").classList.add("none");
    JEDauth.disabled = true;
    JEDauth.classList.add("none");
    KREDauth.disabled = true;
    KREDauth.classList.add("none");
    chain.read.WalletStatus.get_wallet_status(user_wallet).then(function(res){
        console.log(res);
        let JED_status = res[0];
        let KRED_status = res[1];
        if (!(JED_status || KRED_status)){
            console.log("all good!");
            JEDlogo.classList.add("activated");
            KREDlogo.classList.add("activated");
            document.getElementById("JED-no-fee").classList.remove("none");
            document.getElementById("KRED-no-fee").classList.remove("none");
        }else{
            if (!JED_status){JEDlogo.classList.add("activated");isJEDApproved = true;document.getElementById("JED-no-fee").classList.remove("none");}
            if (!KRED_status){KREDlogo.classList.add("activated");isKREDApproved = true;document.getElementById("KRED-no-fee").classList.remove("none");}
            document.getElementById("activate-message").classList.remove("none");
            console.log("getting wallet unfreeze...");
            chain.read.WalletStatus.get_wallet_unfreeze(user_wallet).then(function(res){
                console.log(res);
                JED = res[0];
                KRED = res[1];
                chain.read.JEDToken.allowance(user_wallet, contract_address).then(function(res){
                    let allowance = parseInt(remove18(res));
                    if (res == 0){allowance = 0;}
                    console.log("JED allowance: " + allowance);
                    if (JED != 0 && allowance < parseInt(remove18(JED))){
                        // display JED to user, enable authorise button
                        for (let fee of JEDunlockfee){fee.innerHTML = formatNum(remove18(JED));}
                        // JEDunlockfee.innerHTML = formatNum(remove18(JED));
                        JEDauth.disabled = false;
                        JEDauth.classList.remove("none");
                        document.querySelector("#JED .auth-fee").classList.remove("none"); 
                        JEDauth.addEventListener("click", function(){
                            blockingModal(true, "Authorising JED...");
                            chain.write.JEDToken.approve(contract_address, JED, console.log).then(function(){
                                blockingModal(false);
                                document.querySelector("#JED .auth-fee").classList.add("none"); 
                                document.getElementById("JED-authorised-text").classList.remove("none")
                                JEDauth.disabled = true;
                                JEDauth.classList.add("none");
                                isJEDApproved = true;
                                canActivate();
                            });
                        });
                    }else{
                        for (let fee of JEDunlockfee){fee.innerHTML = formatNum(remove18(JED));}
                        document.querySelector("#JED .auth-fee").classList.add("none"); 
                        document.getElementById("JED-authorised-text").classList.remove("none")
                        JEDauth.disabled = true;
                        JEDauth.classList.add("none");
                        isJEDApproved = true;
                        canActivate();
                    }
                });
                chain.read.KREDToken.allowance(user_wallet, contract_address).then(function(res){
                    let allowance = parseInt(remove18(res));
                    if (res == 0){allowance = 0;}
                    console.log("KRED allowance: " + res);
                    if (KRED != 0 && allowance < parseInt(remove18(KRED))){
                        // display KRED to user, enable authorise button
                        for (let fee of KREDunlockfee){fee.innerHTML = formatNum(remove18(KRED));}
                        // KREDunlockfee.innerHTML = formatNum(remove18(KRED));
                        KREDauth.disabled = false;
                        KREDauth.classList.remove("none");
                        document.querySelector("#KRED .auth-fee").classList.remove("none");
                        KREDauth.addEventListener("click", function(){
                            blockingModal(true, "Authorising KRED...");
                            chain.write.KREDToken.approve(contract_address, KRED, console.log).then(function(){
                                blockingModal(false);
                                document.querySelector("#KRED .auth-fee").classList.add("none");
                                document.getElementById("KRED-authorised-text").classList.remove("none");
                                KREDauth.disabled = true;
                                KREDauth.classList.add("none");
                                isKREDApproved = true;
                                canActivate();
                            });
                        });
                    }else{
                        for (let fee of KREDunlockfee){fee.innerHTML = formatNum(remove18(KRED));}
                        document.querySelector("#KRED .auth-fee").classList.add("none");
                        document.getElementById("KRED-authorised-text").classList.remove("none");
                        KREDauth.disabled = true;
                        KREDauth.classList.add("none");
                        isKREDApproved = true;
                        canActivate();
                    }
                });

            });
        }
    });
}

/* Activate the wallet */

let activate = function(){
    blockingModal(true, "Activating your wallet...");
    chain.write.WalletStatus.activate_wallet(console.log).then(function(){
        wallet_status();
        blockingModal(false);
    });
    // reset texts
    let t1 = document.getElementsByClassName("auth-fee");
    let t2 = document.getElementsByClassName("auth-text");
    for (let t of t1){if (!t.classList.contains("none")){t.classList.add("none");}}
    for (let t of t2){if (!t.classList.contains("none")){t.classList.add("none");}}
}

let canActivate = function(){
    console.log("in canActivate");
    let activate_button = document.getElementById("activate-button")
    if (isJEDApproved && isKREDApproved){
        document.getElementById("activate-message").classList.add("none");
        activate_button.disabled = false;
        activate_button.classList.remove("none");
        activate_button.addEventListener("click", function(){activate();});
    }
}

KV.ContractFns.when_ready('WalletStatus').then(function(w3ct){});
let connected_once = false;
walletui.on("wallet_connected", function(wa){
    if (connected_once){
        
    }else{
        let not_logged = document.getElementById("myagora-cards");
        let walletaddr = document.getElementById("walletaddress");
        let curr = document.getElementById("currencies");
        not_logged.style.display = 'none';
        walletaddr.innerHTML = formatAddr(user_wallet);
        curr.style.display = "block";

        wallet_status();
        connected_once = true;
    }
});
})()

/* HELPERS */

function formatAddr(addr) {
    let first4  = addr.slice(0,5);
    let last2   = addr.slice(-4);
    return first4 + "....." + last2;
}

function remove18(x){
    let end = x.length - 18;
    return x.substring(0,end);
}

function add18(x){
    for (let i = 0; i < 18; i++){
        x += "0";
    }
    return x;
}

function formatNum(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}