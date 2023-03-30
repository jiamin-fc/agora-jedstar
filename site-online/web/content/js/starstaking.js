(function(){
/* Disable login button until wallets are loaded */
var populated = false;
var contract_address = "0x7d2A8685597D04bD5D42fcE2d25B31d86E420501";
var interval={};
var isTVL = false;
var mybalance;
var total_staked1, total_staked2, total_staked3;
var stakingPlans;
var fullStakeVal;
var apy = {};
var mystake = {};
var maxstake = {};
var minstake = {};
var stakeable = {};
var staked = {};
var isStaked = new Array();
let popup = document.getElementById("info-popup");
let page = document.querySelector(".index-container .section");
let bg_img = document.querySelector(".index-container .background-image");
let allowance = 0;
let calculateTVL;

let JED = document.getElementById("stakeJED");
JED.addEventListener("click", function(){
    if (!isTVL){
        blockingModal(true, "Please wait...");
    }
    document.body.classList.add("jed");
});
let fn_processAddressMap = function(num){
    return function(res){
        KV.debug("addressmap"+num);
        let mykred = document.getElementById("mykred"+num);
        let max = document.getElementById("max"+num);
        let stake = document.getElementById("stake"+num);
        let input = document.getElementById("stakingAmount"+num);
        let rewards = document.getElementById("rewardsLabel"+num);
        let warning = document.getElementById("warning"+num);
        mystake[num] = res;
        if (res.stakeAmt != 0){
            stake.classList.add("staked");
            mykred.style.display = "none";
            max.style.display = "none";
            rewards.innerHTML = "Total Accrued Reward Until Now";
            if (input){
                input.value = remove18(res.stakeAmt);
            }
            let maturity = timeToMaturity(parseInt(res.stakeTime)*1000, parseInt(stakingPlans[num-1]["minStakePeriod"])); // maturity1 = timeToMaturity("December 28, 2022 15:31:10", 30)

            // Starts Countdown
            let time = document.getElementById("maturity"+num);
            let claim = document.getElementById("claim"+num);
            if (!interval[num]){
                KV.debug(num);
                startCountdown(interval[num], maturity, time, claim, num);
            }
            input.disabled = true;
            input.classList.add("white");
            warning.innerHTML = "";
        }else{
            stake.classList.remove("staked");
            mykred.style.display = "block";
            max.style.display = "block";
            input.disabled = false;
            input.classList.remove("white");
            rewards.innerHTML = "Total Potential Reward based on 'Staking Amount'";
            warning.innerHTML = "";
        }
    }
}
function populateDataRO(){
    KV.debug("starting populateDataRO()...");

    /* Disable Stake Buttons */
    let stakeBtns = document.getElementsByClassName("stakeBtn");
    for (let stakeBtn of stakeBtns){
        stakeBtn.disabled = true;
    }

    /* Loading Overlay */
    var dots = window.setInterval( function() {
        let loadings = document.getElementsByClassName("loading");
        for (let loading of loadings){
            if ( loading.innerHTML.length > 9 ) 
                loading.innerHTML = "Loading";
            else 
                loading.innerHTML += ".";
        }
        // var wait = document.getElementById("wait");
    }, 500);

    /* Staking Amount Remaining (stakeable - total staked over all time)*/
    let updateStakeable = function(){
        return new Promise((resolve, reject) => {
            KV.debug("Updating stakeable...");
            Promise.all([
                chain.read.JedstarStakingRO.totalStakeable1(),
                chain.read.JedstarStakingRO.totalStaked1(),
                chain.read.JedstarStakingRO.totalStakeable2(),
                chain.read.JedstarStakingRO.totalStaked2(),
                chain.read.JedstarStakingRO.totalStakeable3(),
                chain.read.JedstarStakingRO.totalStaked3()
            ]).then((responses)=>{
                let res = [];
                for (let i = 0; i < responses.length; i+=2){
                    let total = responses[i] <= 0 ? 0 : parseInt(remove18(responses[i]));
                    let staked = responses[i+1] <= 0 ? 0 : parseInt(remove18(responses[i+1]));

                    res.push({"total": total, "staked": staked});
                }
                resolve(res);
            }).catch((error) => {
                reject(error);
            });
        })
    };

    /* Total Staked Amount */
    calculateTVL = function(){
        KV.debug("TVL function starts");
        let staked1 = document.querySelector("#period1 .total-staked-amount");
        let staked2 = document.querySelector("#period2 .total-staked-amount");
        let staked3 = document.querySelector("#period3 .total-staked-amount");

        //Retrieve the current amounts which are locked in the contract - this is different to the "totalStaked" series which counts the total over all time
        Promise.all([
            chain.read.JedstarStakingRO.totalStakedAmount1(),
            chain.read.JedstarStakingRO.totalStakedAmount2(),
            chain.read.JedstarStakingRO.totalStakedAmount3()
        ]).then(function(res){
            total_staked1 = res[0] <= 0 ? 0 : parseInt(remove18(res[0]).toString());
            total_staked2 = res[1] <= 0 ? 0 : parseInt(remove18(res[1]).toString());
            total_staked3 = res[2] <= 0 ? 0 : parseInt(remove18(res[2]).toString());
    
            if (staked1) staked1.innerHTML = total_staked1 + " JED";
            if (staked2) staked2.innerHTML = total_staked2 + " JED";
            if (staked3) staked3.innerHTML = total_staked3 + " JED";
    
            updateStakeable().then(function(res){
                for (k=0; k < res.length; k++){
                    stakeable[k+1] = res[k]["total"] - res[k]["staked"];
                    staked[k+1] = res[k]["staked"];
                    document.getElementById("stakeable"+(k+1)).innerHTML = stakeable[k+1] + " JED";
                }   
            }).catch(function(err){
                console.error("Unfortunately there was an error updating stakable values!");
                console.error(err);
            })
    
            let tvl = document.getElementById('tvl');
            if (tvl) {
                /* 4000 (-18 0s) */
                let result = (total_staked1 + total_staked2 + total_staked3).toString()
                tvl.innerHTML =  formatNum(result) + " JED";
    
                /* Clearing blocking modal */
                KV.debug("TVL done");
                isTVL = true;
                blockingModal(false);
            }
            /* Load in Cards */
            Promise.all([populateCard(1), populateCard(2), populateCard(3)]).then(function(carddata){
                //Use the data retrieved for populating the cards to load in the Total Rewards Remaining
                KV.debug("Updating TRR...");
                stakingPlans = carddata;
                let trr_val = 0;
                fullStakeVal = 0; //total of all max stakeable values
                for (let j=0; j < carddata.length; j++){
                    trr_val += (stakeable[j+1] - staked[j+1]) * convertSecToDay(carddata[j]["minStakePeriod"]) * carddata[j]["dailyInterestRate"];
                    fullStakeVal += parseInt(remove18(carddata[j]["maxStakeAmt"]));
                }
                document.getElementById("trr").innerHTML = formatNum(trr_val) + " KRED";
            }).catch(function(err){
                console.error("Unfortunately there was an error populating the staking cards");
                console.error(err);
            })
        }).catch(function(error){
            console.error("Unfortunately there was an error retrieving total staked amounts");
            console.error(error);
        });
    };

    let populateCard = async function(num){
        return new Promise((resolve, reject) => {
            if (num < 1 || num > 3){
                reject("Bad card number. Nothing done.");
            }
            KV.debug("Updating card "+num);
            Promise.all([
                chain.read.JedstarStakingRO["dailyInterestRate"+num](),
                chain.read.JedstarStakingRO["minStakePeriod"+num](),
                chain.read.JedstarStakingRO["minStakeAmt"+num](),
                chain.read.JedstarStakingRO["maxStakeAmt"+num]()
            ]).then((responses) => {
                apy[num] = responses[0] / 10000;
                document.getElementById("apy"+num).value = (apy[num] * 365) + "%";
                let period = document.getElementById("min-period"+num);
                if (period){ period.value = secToDHMS(responses[1]); }
                minstake[num] = responses[2];
                maxstake[num] = responses[3];
                let loading = document.querySelector("#stake"+num+" .dimmer");
                loading.classList.add("hidden");
                //Reset staking input values and reward values
                document.getElementById("value-after-apy"+num).value = 0;
                if (mystake[num] === undefined){
                    document.getElementById("stakingAmount"+num).value = "";
                }else{ if (mystake[num].stakeAmt == 0){document.getElementById("stakingAmount"+num).value = "";}}
                resolve({
                    "dailyInterestRate": responses[0],
                    "minStakePeriod": responses[1],
                    "minStakeAmt": responses[2],
                    "maxStakeAmt": responses[3]
                });
            }).catch((error) => {
                reject(error);
            });
        });
  
    }
     /* Value after APY */
     let fn_updateStakingVals = function(num){
        return function(){
            //NOTE: populateCard has to have been executed for this fn to work as the stakingPlans var needs to be populated.
            //In the current workflow this is assured as the staking plan card is not interactive until populateCard has completed
            let stakingAmt = document.getElementById("stakingAmount"+num);
            let warning = document.getElementById("warning"+num);
            let valueAfterAPY = document.getElementById("value-after-apy"+num);
            
            //Force whole numbers
            stakingAmt.value = parseInt(stakingAmt.value) ? parseInt(stakingAmt.value) : "";

            if (parseInt(stakingAmt.value) > parseInt(remove18(maxstake[num]))) {
                stakingAmt.classList.add("exceed"); 
                warning.classList.add("visible");
                warning.innerHTML = "Maximum stake amount is " + formatNum(remove18(maxstake[num])) + " JED";
            }else if (parseInt(stakingAmt.value) < parseInt(remove18(minstake[num]))) {
                stakingAmt.classList.add("exceed"); 
                warning.classList.add("visible");
                warning.innerHTML = "Minimum stake amount is "+ formatNum(remove18(minstake[num])) + " JED";
            }else if (stakingAmt.classList.contains("exceed") && parseInt(stakingAmt.value) >= parseInt(remove18(minstake[num])) && parseInt(stakingAmt.value) <= parseInt(remove18(maxstake[num]))) {
                stakingAmt.classList.remove("exceed");
                warning.classList.remove("visible");
            }
            if (isNaN(stakingAmt.value)){
                valueAfterAPY.value = "---";
            }else{
                let apy = document.getElementById("apy"+num).value;
                let locking = document.getElementById("locking"+num);
                let after = parseInt(calculateValueAfterAPY(stakingAmt.value, (stakingPlans[num-1]["dailyInterestRate"]/1000000), parseInt(stakingPlans[num-1]["minStakePeriod"])));
                let duration = secToDHMS(parseInt(stakingPlans[num-1]["minStakePeriod"]));
                if (stakingAmt.classList.contains("exceed")){
                    valueAfterAPY.value = "---";
                    locking.innerHTML = "";
                }else{
                    valueAfterAPY.value = formatNum(after);
                    locking.innerHTML = "Locking " +formatNum(stakingAmt.value)+ " JED for "+duration+" to receive "+formatNum(after)+" KRED";
                }
            } 
        }
     }
     let fn_showMaxVal = function(num){
        return function(){
            let planMax = parseInt(remove18(stakingPlans[num-1]["maxStakeAmt"]));
            let colMax = stakeable[num];
            let maxPoss = planMax > colMax ? colMax : planMax;
            if (typeof mybalance == "undefined"){
                document.getElementById("stakingAmount"+num).value = maxPoss;
            }else{
                let jedBal = parseInt(remove18(mybalance)) ? parseInt(remove18(mybalance)) : 0;
                document.getElementById("stakingAmount"+num).value = maxPoss > jedBal  ? jedBal : maxPoss;
            }
            fn_updateStakingVals(num)();
        }
    };
    for (let x = 1; x <= 3; x++){
        document.getElementById("stakingAmount"+x).addEventListener("input", fn_updateStakingVals(x));
        document.getElementById("max"+x).addEventListener("click", fn_showMaxVal(x));
    }
    
    calculateTVL();
}

function populateData(){
    chain.read.JedstarStaking.addressMap1(user_wallet).then(fn_processAddressMap(1));
    chain.read.JedstarStaking.addressMap2(user_wallet).then(fn_processAddressMap(2));
    chain.read.JedstarStaking.addressMap3(user_wallet).then(fn_processAddressMap(3));

    /* Claim reward button */
    let fn_claimHandler = function(num){
        chain.read.JedstarStaking["addressMap"+num](user_wallet).then(function(res){
            let amount = res.stakeAmt;
            chain.write.JedstarStaking["unstake"+num](amount, KV.debug).then(function(res){
                //TODO update post-unstake logic
                //Unstaking successful, update UI
                blockingModal(true, "Updating...");
                calculateTVL(); //update all general world vars
                //update the vars specific to this user
                chain.read.JedstarStaking["addressMap"+num](user_wallet).then(function(res){
                    fn_processAddressMap(num)(res);
                    blockingModal(false);
                    document.getElementById("claim"+num).disabled = false;
                    updateJEDBalanceLabels();
                    showToast("Your rewards have been claimed!", "good");
                });
                let locking = document.getElementById("locking"+num);
                locking.innerHTML = "";
            }).catch(function(err){
                KV.debug(err);
                showToast("Failed to claim your rewards", "bad");
                blockingModal(false);
            });
        });
    };
    let claims = document.getElementsByClassName("claimBtn");
    for (let claim of claims){
        claim.addEventListener("click", function(){
            blockingModal(true, "Claiming your rewards...");
            switch(claim.id){
                case "claim1":
                    fn_claimHandler(1);
                    break;
                case "claim2":
                    fn_claimHandler(2);
                    break;
                case "claim3":
                    fn_claimHandler(3);
                    break;
            }
        });
    }
}

var updateJEDBalanceLabels = function(){
    return new Promise((resolve, reject) => {
        chain.read.JEDToken.balanceOf(user_wallet).then(function(res){
            mybalance = res;
            let jedTxt;
            if (res == "0"){
                jedTxt = "You don't have any JED. <a target='_blank' href='https://app.uniswap.org/#/swap?outputCurrency=0xF6D0762C645E873E5884E69BBcB2F074E6067A70'>Buy some?</a>";
            }else{
                jedTxt = "My Available JED: " + formatNum(parseInt(remove18(res)));
            }
            let myjed = document.querySelectorAll(".mykred.balance");
            for (let jedlbl of myjed) {
                jedlbl.innerHTML = jedTxt;
            }
        }).catch(function(err){
            reject(err);
        });
        resolve(mybalance);
    });
};

function getBalance(){
    KV.debug("updating balance...");

    updateJEDBalanceLabels();
    /* Enable Stake Buttons */
    let stakeBtns = document.getElementsByClassName("stakeBtn");
    for (let stakeBtn of stakeBtns){
        stakeBtn.disabled = false;
    }

    /* Staking */
    let fn_doAuthorisation = function(stakingAmt, allowance){
        return new Promise((resolve, reject) => {
            if (allowance >= stakingAmt){
                resolve();
            }else{
                document.getElementById("popup-title").innerHTML = "Authorization";
                document.getElementById("popup-text").innerHTML = "You need to authorize the staking contract to move your JED tokens. Please click Authorize and then confirm the transaction in your wallet.";
                document.getElementById("authorize-button-popup").innerHTML = '<div id = "popup-button-text" class = "capitalise">Authorize</div>';
                popup.classList.add('active');
                bg_img.classList.add('active');
                page.classList.add('active');

                document.getElementById("popup-button-text").addEventListener("click", function(){
                    blockingModal(true, "Please approve the transaction in your wallet");
                    chain.write.JEDToken.approve(
                        contract_address,
                        add18(fullStakeVal),
                        function(txid){ 
                            showToast("Broadcasting your transaction to the blockchain with transaction reference "+txid, "good");
                            blockingModal(true, "Authorizing...");
                        }
                    ).then(function(res){
                        document.getElementById("popup-title").innerHTML = "Stake";
                        document.getElementById("popup-text").innerHTML = "You successfully approved the staking contract to move your JED tokens. You can now proceed to stake.";
                        document.getElementById("authorize-button-popup").innerHTML = '<div id = "popup2-button-text" class = "capitalise">Stake</div>';
                        document.getElementById("popup2-button-text").addEventListener("click", function(){
                            popup.classList.remove('active');
                            bg_img.classList.remove('active');
                            page.classList.remove('active');
                            resolve();
                        });
                        blockingModal(false);
                        showToast("You successfully approved the staking contract to move your JED" , "good");
                    }).catch(function(err){
                        console.error(err);
                        blockingModal(false);
                        showToast("The staking contract was not approved to move your JED", "bad");
                        popup.classList.remove('active');
                        bg_img.classList.remove('active');
                        page.classList.remove('active');
                        reject(err);
                    });
                })
                blockingModal(false);
            }
        });
    };
    let fn_doStake = function(num){
        return function(){
            if (document.body.classList.contains("connected")){
                blockingModal(true, "Checking your allowance...");
                chain.read.JEDToken.allowance(user_wallet, contract_address).then(function(res){
                    if (res == 0){
                        allowance = 0;
                    }else{
                        allowance = parseInt(remove18(res));
                    }
                    let stakingValInput = document.getElementById('stakingAmount'+num);
                    if (stakingValInput.classList.contains("exceed")){
                        showToast("Please check your Staking Amount input again", "bad");
                        return;
                    }
                    fn_doAuthorisation(stakingValInput.value, allowance).then(function(){
                        blockingModal(true, "Please approve the request in your wallet");
                        amount = add18(stakingValInput.value);
                        chain.write.JedstarStaking["stake"+num](amount, function(txid){ blockingModal(true, "Staking JED..."); }).then(function(res){
                            //Staking successful, update UI
                            blockingModal(true, "Updating...");
                            calculateTVL(); //update all general world vars
                            //update the vars specific to this user
                            chain.read.JedstarStaking["addressMap"+num](user_wallet).then(function(res){
                                fn_processAddressMap(num)(res);
                                document.getElementById("claim"+num).disabled = true;
                                updateJEDBalanceLabels();
                                //Update rewards field to zero
                                document.getElementById("value-after-apy"+num).value = 0;
                                blockingModal(false);
                                showToast("Your JED has been successfully staked", "good");
                            });
                            let locking = document.getElementById("locking"+num);
                            locking.innerHTML = "";
                        }).catch(function(err){
                            showToast("Failed to stake", "bad");
                            blockingModal(false);
                            KV.debug(err);
                        });
                    }).catch(function(err){

                    });
                });
            }else{
                showToast("Please log into your wallet before staking", "bad");
            }  
        }
    };
    document.getElementById("stakeBtn1").addEventListener("click", fn_doStake(1));
    document.getElementById("stakeBtn2").addEventListener("click", fn_doStake(2));
    document.getElementById("stakeBtn3").addEventListener("click", fn_doStake(3));
}

/* Helper Functions */

function convertSecToDay(s) {
    return s / 86400;
}

function timeToMaturity(t, x) {
    let stake_time = new Date(t);

    if (Object.prototype.toString.call(stake_time) === "[object Date]") {
         // it is a date
        if (isNaN(stake_time)) { // d.getTime() or d.valueOf() will also work
            // date object is not valid
            KV.debug("Date is invalid, retrying...");
            let retry = setTimeout(function(){
                timeToMaturity(t, x);
            }, 1000);
        } else {
            // date object is valid
            stake_time.setSeconds(stake_time.getSeconds() + x);
            return stake_time;
        }
    } else {
        // not a date object
    }

}

function toFixed(x) {
  if (Math.abs(x) < 1.0) {
    var e = parseInt(x.toString().split('e-')[1]);
    if (e) {
        x *= Math.pow(10,e-1);
        x = '0.' + (new Array(e)).join('0') + x.toString().substring(2);
    }
  } else {
    var e = parseInt(x.toString().split('+')[1]);
    if (e > 20) {
        e -= 20;
        x /= Math.pow(10,e);
        x += (new Array(e+1)).join('0');
    }
  }
  return x;
}

function calculateValueAfterAPY(input, dailyIntRate, durationInSecs){
    return (dailyIntRate/86400) * durationInSecs * input;
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

var domAnimLock = {};
function animateUp(domElem, start, end, duration){
    //prevent negative movements, and multiple animations running
    if (end - start < 1 || domAnimLock[domElem.id]){ return; }
    domAnimLock[domElem.id] = true;
    if (typeof duration == "undefined" || !parseInt(duration)){
        duration = 5; //default to 5 seconds
    }
    let increment = Math.floor((end - start) / (duration * 10));
    let counter = 1;
    domElem.value = start;
    let countUpInterval = setInterval(function(){
        if (counter >= duration * 10){
            domElem.value = formatNum(end);
            clearInterval(countUpInterval);
            domAnimLock[domElem.id] = false;
        }else{
            domElem.value = formatNum(start + (increment*counter));
            counter++;
        }
    }, 100);

}

function startCountdown(bool, maturity, DOM, claim, colNum){
    // clearInterval(interval);
    if (Object.prototype.toString.call(maturity) === "[object Date]") {
        // it is a date
        if (isNaN(maturity)) { // d.getTime() or d.valueOf() will also work
            // date object is not valid
            KV.debug("Date is not valid, retrying...");
            let retry = setTimeout(function(){
                startCountdown(bool, maturity, DOM, claim, colNum)
            }, 1000);
        } else {
            // date object is valid
            KV.debug("starting countdown: " + DOM.id);
            bool = true;
            let interval = setInterval(function(){
                var time_now = new Date().getTime();
                diff = (maturity.getTime() - time_now)/1000;
                if (DOM){
                    DOM.value = secToDHMS(diff);;
                }
                if (diff <= 0) {
                    clearInterval(interval);
                    DOM.value = "Ready To Claim";
                    if (claim){
                        claim.disabled = false;
                    }
                    bool = false;
                }
                if (time_now % 15000){
                    //update the accrued balance every X period
                    chain.read.JedstarStaking["interestEarnedUpToNowBeforeTaxesAndNotYetWithdrawn"+colNum](user_wallet).then(function(res){
                        let rewardsField = document.getElementById("value-after-apy"+colNum);
                        //reset the current reading to zero if there any issues with the data in the field
                        let currVal = parseInt(rewardsField.value.replaceAll(",","")) ? parseInt(rewardsField.value.replaceAll(",","")) : 0;
                        animateUp(rewardsField, currVal, remove18(res), 13);
                    }).catch(function(err){
                        console.error(err);
                    })
                }
            }, 1000);
        }
    } else {
        // not a date object
        console.error("Not a date object");
    }
}

function secToDHMS(seconds) {
    seconds = Number(seconds);
    var d = Math.floor(seconds / (3600*24));
    var h = Math.floor(seconds % (3600*24) / 3600);
    var m = Math.floor(seconds % 3600 / 60);
    var s = Math.floor(seconds % 60);

    var dDisplay = d > 0 ? d + " day" + (d == 1 ? " " : "s ") : "";
    var hDisplay = h > 0 ? h + " hour" + (h == 1 ? " " : "s ") : "";
    var mDisplay = m > 0 ? m + " minute"+(m == 1 ? " " : "s ") : "";
    var sDisplay = s > 0 ? s + " second"+(s == 1 ? " " : "s") : "";
    return dDisplay + hDisplay + mDisplay + sDisplay;
}

KV.ContractFns.when_ready('JedstarStakingRO').then(function(w3ct){ KV.debug("RO ready"); populateDataRO(); });
KV.ContractFns.when_ready('JEDToken').then(function(w3ct){ getBalance();});
let connected_once = false;
walletui.on("wallet_connected", function(wa){
    if (connected_once){
        modalWalletDisconnected();
    }else{
        populateData();
        connected_once = true;
    }
});
let modalWalletDisconnected = function(){
    // cloning the close button
    var old_btn = document.querySelector("#info-popup .title .close-button");
    var new_btn = old_btn.cloneNode(true);
    old_btn.parentNode.replaceChild(new_btn, old_btn);
    new_btn.addEventListener("click", function(){document.getElementById("popup2-button-text").click();});
    document.getElementById("popup-title").innerHTML = "Wallet disconnected";
    document.getElementById("popup-text").innerHTML = "Your wallet has been disconnected from this site. Please reload the page and reconnect.";
    document.getElementById("authorize-button-popup").innerHTML = '<div id = "popup2-button-text" class = "capitalise">Reload</div>';
    document.getElementById("popup2-button-text").addEventListener("click", function(){ window.location.reload(); });
    document.getElementById("stake-button-div").innerHTML = "";
    popup.classList.add('active');
    bg_img.classList.add('active');
    page.classList.add('active');
};
walletui.on("wallet_disconnected", modalWalletDisconnected);
KV.wallet.on_session_change(modalWalletDisconnected);
})()
Date.prototype.time_until = function(t){let d=t-this; return {days:Math.floor((((d/1000)/60)/60)/24),hours:Math.floor(((d/1000)/60)/60) % 24,mins:Math.floor((d/1000)/60) % 60,secs:Math.floor(d/1000) % 60}};