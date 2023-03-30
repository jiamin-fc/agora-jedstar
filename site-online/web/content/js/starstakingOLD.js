var populated = false;
var contract_address = "0x7d2A8685597D04bD5D42fcE2d25B31d86E420501";
var clickedStaked = 0;
var interval1 = false, interval2 = false, interval3 = false;
let JED = document.getElementById("stakeJED");
JED.addEventListener("click", function(){
    blockingModal(true, "Please wait...");
    const stopModal = setTimeout(function(){blockingModal(false);}, 6000);
    document.body.classList.add("jed");
});
let stakeBtns = document.getElementsByClassName("stakeBtn");
let stakeBtn1 = document.getElementById("stakeBtn1");
let stake1 = document.getElementById("stake1");
let popup = document.getElementById("info-popup");
let page = document.querySelector(".index-container .section");
let bg_img = document.querySelector(".index-container .background-image");
let close_button = document.querySelector(".close-button");
let authorize_button = document.getElementById("authorize-button-popup");
let stake_button = document.getElementById("stake-button-popup");
let allowance = 0;


/* Staking */
for (let stakeBtn of stakeBtns){
    let body, amount;
    stakeBtn.addEventListener("click", function(){
        if (document.body.classList.contains("connected")){
            blockingModal(true, "Checking your allowance...");
            chain.read.JEDToken.allowance(user_wallet, contract_address).then(function(res){
                blockingModal(false);
                if (res == 0){
                    allowance = 0;
                }else{
                    allowance = parseInt(remove18(res));
                }
                switch(stakeBtn.id){
                    case "stakeBtn1":
                        clickedStaked = 1;
                        body = document.getElementById('stakingAmount1');
                        if (body.classList.contains("exceed")){
                            showToast("Please check your Staking Amount input again", "bad");
                            return;
                        }
                        if (body.value > allowance){
                            popup.classList.toggle('active');
                            bg_img.classList.toggle('active');
                            page.classList.toggle('active');
                        }else{
                            blockingModal(true, "Staking...");
                            amount = add18(body.value);
                            chain.write.JedstarStaking.stake1(amount, console.log).then(function(res){
                                let locking = document.getElementById("locking1");
                                locking.innerHTML = "";
                                populated = false;
                                populateData();
                                const reload = setTimeout(function(){
                                    blockingModal(false);
                                    showToast("You have successfully staked with TX ID: " + res.blockHash, "good");
                                }, 5000);
                            }).catch(function(err){
                                showToast("Failed to stake", "bad");
                                blockingModal(false);
                                console.log(err);
                            });

                        }

                        break;
                    case "stakeBtn2":
                        clickedStaked = 2;
                        body = document.getElementById('stakingAmount2');
                        if (body.classList.contains("exceed")){
                            showToast("Please check your Staking Amount input again", "bad");
                            return;
                        }
                        if (body.value > allowance){
                            popup.classList.toggle('active');
                            bg_img.classList.toggle('active');
                            page.classList.toggle('active');
                        }else{
                            blockingModal(true, "Staking...");
                            amount = add18(body.value);
                            console.log(amount);
                            chain.write.JedstarStaking.stake2(amount, console.log).then(function(res){
                                let locking = document.getElementById("locking2");
                                locking.innerHTML = "";
                                populated = false;
                                populateData();
                                const reload = setTimeout(function(){
                                    blockingModal(false);
                                    showToast("You have successfully staked with TX ID: " + res.blockHash, "good");
                                }, 5000);
                            }).catch(function(err){
                                showToast("Failed to stake", "bad");
                                blockingModal(false);
                                console.log(err);
                            });

                        }
                        break;
                    case "stakeBtn3":
                        clickedStaked = 3;
                        body = document.getElementById('stakingAmount3');
                        if (body.classList.contains("exceed")){
                            showToast("Please check your Staking Amount input again", "bad");
                            return;
                        }
                        if (body.value > allowance){
                            popup.classList.toggle('active');
                            bg_img.classList.toggle('active');
                            page.classList.toggle('active');
                        }else{

                            blockingModal(true, "Staking...");
                            amount = add18(body.value);
                            chain.write.JedstarStaking.stake3(amount, console.log).then(function(res){
                                let locking = document.getElementById("locking3");
                                locking.innerHTML = "";
                                populated = false;
                                populateData();
                                const reload = setTimeout(function(){
                                    blockingModal(false);
                                    showToast("You have successfully staked with TX ID: " + res.blockHash, "good");
                                }, 5000);
                            }).catch(function(err){
                                showToast("Failed to stake", "bad");
                                blockingModal(false);
                                console.log(err);
                            });
                        }
                        break; 
                }
                
            });

        }else{
            showToast("Please log into your wallet before staking", "bad");
        }

    });    
}


/* Popup close button */
close_button.addEventListener("click", function(){
    if (popup.classList.contains('active')){
        popup.classList.toggle('active');
    }
    page.classList.toggle("active");
    bg_img.classList.toggle("active");
});


/* Allowance authorize button */
authorize_button.addEventListener("click", function(){
    if (document.getElementById("popup-button-text").innerHTML == "Stake"){return;}
    let body, sum;
    console.log("authorizing...");
    blockingModal(true, "Authorizing...");
    switch(clickedStaked){
        case 1:
            chain.read.JEDToken.allowance(user_wallet, contract_address).then(function(res){
                if (res == 0 ){
                    allowance = parseInt(res);
                }else{
                    allowance = parseInt(remove18(res));
                }
                body = parseInt(document.getElementById('stakingAmount1').value);
                sum = allowance + body;
                allowance = sum;
                chain.write.JEDToken.approve(contract_address,add18(sum), console.log).then(function(res){
                    blockingModal(false);
                    console.log(res);
                    stakingPopup(1);
                    showToast("Successfully approved allowance of " + formatNum(sum) + " JED" , "good");
                }).catch(function(err){
                    blockingModal(false);
                    console.log(err);
                    showToast("Failed to approve allowance", "bad");
                });

            });
            break;
        case 2:
            chain.read.JEDToken.allowance(user_wallet, contract_address).then(function(res){
                if (res == 0 ){
                    allowance = parseInt(res);
                }else{
                    allowance = parseInt(remove18(res));
                }
                body = parseInt(document.getElementById('stakingAmount2').value);
                sum = allowance + body;
                allowance = sum;
                chain.write.JEDToken.approve(contract_address,add18(sum), console.log).then(function(res){
                    blockingModal(false);
                    console.log(res);
                    stakingPopup(2);
                    showToast("Successfully approved allowance of " + formatNum(sum) + " JED" , "good");
                }).catch(function(err){
                    blockingModal(false);
                    console.log(err);
                    showToast("Failed to approve allowance", "bad");
                });

            });
            break;
        case 3:
            chain.read.JEDToken.allowance(user_wallet, contract_address).then(function(res){
                if (res == 0 ){
                    allowance = parseInt(res);
                }else{
                    allowance = parseInt(remove18(res));
                }
                body = parseInt(document.getElementById('stakingAmount3').value);
                sum = allowance + body;
                allowance = sum;
                chain.write.JEDToken.approve(contract_address,add18(sum), console.log).then(function(res){
                    blockingModal(false);
                    console.log(res);
                    stakingPopup(3);
                    showToast("Successfully approved allowance of " + formatNum(sum) + " JED" , "good");
                }).catch(function(err){
                    blockingModal(false);
                    console.log(err);
                    showToast("Failed to approve allowance", "bad");
                });

            });
            break;
        case 0:
            blockingModal(false);
            showToast("Failed to approve allowance", "bad");
            break;
    }
});

/* Staking popup button */

stake_button.addEventListener("click", function(){
    let close_button = document.querySelector(".close-button");
    let stake = document.getElementById("stakeBtn" + clickedStaked);
    stake.click();
    close_button.click();
    resetPopup();
});

/* Claim reward button */
let claims = document.getElementsByClassName("claimBtn");
for (let claim of claims){
    claim.addEventListener("click", function(){
        blockingModal(true, "Claiming your rewards...");
        switch(claim.id){
            case "claim1":
                chain.read.JedstarStaking.addressMap1(user_wallet).then(function(res){
                    let amount = res.stakeAmt;
                    chain.write.JedstarStaking.unstake1(amount, console.log).then(function(res){
                        claim.disabled = true;
                        console.log(res);
                        populated = false;
                        populateData();
                        const reload = setTimeout(function(){
                            blockingModal(false);
                            showToast("Succesfully claimed your rewards", "good");
                        }, 5000);
                    }).catch(function(err){
                        console.log(err);
                        showToast("Failed to claim your rewards", "bad");
                        blockingModal(false);
                    });
                });
                break;
            case "claim2":
                chain.read.JedstarStaking.addressMap2(user_wallet).then(function(res){
                    let amount = res.stakeAmt;
                    chain.write.JedstarStaking.unstake2(amount, console.log).then(function(res){
                        claim.disabled = true;
                        console.log(res);
                        populated = false;
                        populateData();
                        const reload = setTimeout(function(){
                            blockingModal(false);
                            showToast("Succesfully claimed your rewards", "good");
                        }, 5000);
                    }).catch(function(err){
                        console.log(err);
                        showToast("Failed to claim your rewards", "bad");
                        blockingModal(false);
                    });
                });
                break;
            case "claim3":
                chain.read.JedstarStaking.addressMap3(user_wallet).then(function(res){
                    let amount = res.stakeAmt;
                    chain.write.JedstarStaking.unstake3(amount, console.log).then(function(res){
                        claim.disabled = true;
                        console.log(res);
                        populated = false;
                        populateData();
                        const reload = setTimeout(function(){
                            blockingModal(false);
                            showToast("Succesfully claimed your rewards", "good");
                        }, 5000);
                    }).catch(function(err){
                        console.log(err);
                        showToast("Failed to claim your rewards", "bad");
                        blockingModal(false);
                    });
                });
                break;
        }
    });
}


/* Populates and updates the DOM */
function populateData(){
    resetPopup();
    if (populated){
        return;
    }else{
        populated = true;
    }
    console.log("populating data...");
    // if (document.body.classList.contains("connected")){
    //     blockingModal(true, "Please wait...");
    // }
    
   let  mybalance,
        stake_period1,
        stake_period2,
        stake_period3,
        total_staked1,
        total_staked2,
        total_staked3,
        staked1,
        staked2,
        staked3,
        stakeable1,
        stakeable2,
        stakeable3,
        mystaked1,
        mystaked2,
        mystaked3,
        apy1,
        apy2,
        apy3,
        myallocated1,
        myallocated2,
        myallocated3;

    var maturity1,
        maturity2,
        maturity3,
        diff,
        minstake1,
        minstake2,
        minstake3,
        maxstake1,
        maxstake2,
        maxstake3;



    /*attach animation listener to total rewards (listen to the countdown bar)*/
    
    /* Update My Balance */
    console.log("updating balance...");
    chain.read.JEDToken.balanceOf(user_wallet).then(function(res){
        mybalance = res;
        let mykred = document.querySelectorAll(".mykred.balance");
        for (let kred of mykred) {
            kred.innerHTML = "My Available $JED: " + formatNum(remove18(mybalance));
        }
    });


    /* Minimum Staking Periods */
    chain.read.JedstarStakingRO.minStakePeriod1().then(function(res){
        console.log("retrieved stake period 1");
        stake_period1 = res;
        let period = document.getElementById("min-period1");
        if (period){
            period.value = secToDHMS(stake_period1);
        }
    });

    chain.read.JedstarStakingRO.minStakePeriod2().then(function(res){
        console.log("retrieved stake period 2");
        stake_period2 = res;
        let period = document.getElementById("min-period2");
        if (period){
            period.value = secToDHMS(stake_period2);
        }
    });

    chain.read.JedstarStakingRO.minStakePeriod3().then(function(res){
        console.log("retrieved stake period 3");
        stake_period3 = res;
        let period = document.getElementById("min-period3");
        if (period){
            period.value = secToDHMS(stake_period3);
        }
    });


    /* Update all the APYs */

    let updateAPY = setTimeout(function(){
        console.log("updating apy...");
        chain.read.JedstarStakingRO.dailyInterestRate1().then(function(res){
            let apy = document.getElementById("apy1");
            apy1 = res/10000;
            apy.value = (res / 10000 * 365) + "%";
        }).catch(function(err){
            console.log(err);
            updateAPY();
        });

        chain.read.JedstarStakingRO.dailyInterestRate2().then(function(res){
            let apy = document.getElementById("apy2");
            apy2 = res/10000;
            apy.value = (res / 10000 * 365) + "%";
        }).catch(function(err){
            console.log(err);
            updateAPY();
        });

        chain.read.JedstarStakingRO.dailyInterestRate3().then(function(res){
            let apy = document.getElementById("apy3");
            apy3 = res/10000;
            apy.value = (res / 10000 * 365) + "%";
        }).catch(function(err){
            console.log(err);
            updateAPY();
        });

    }, 1000);

    /* Update all the remaining staking amount (stakeable - total staked)*/

    let updateStakeable = setTimeout(function(){
        console.log("updating stakeable...");
        chain.read.JedstarStakingRO.totalStakeable1().then(function(res){
            if (res <= 0){
                stakeable1 = parseInt(res);
            }else{
                stakeable1 = parseInt(remove18(res));
            }
            let stakeable = document.getElementById("stakeable1");
            chain.read.JedstarStakingRO.totalStaked1().then(function(res){
                if (res <= 0){
                    staked1 = parseInt(res);
                }else{
                    staked1 = parseInt(remove18(res));
                }
                let result = (stakeable1 - staked1).toString();
                stakeable.innerHTML =  formatNum(result) + " JED";
            });
        }).catch(function(err){
            console.log(err);
            updateStakeable();
        });

        chain.read.JedstarStakingRO.totalStakeable2().then(function(res){
            if (res <= 0){
                stakeable2  = parseInt(res);
            }else{
                stakeable2 = parseInt(remove18(res));
            }
            let stakeable = document.getElementById("stakeable2");
            chain.read.JedstarStakingRO.totalStaked2().then(function(res){
                if (res <= 0){
                    staked2 = parseInt(res);
                }else{
                    staked2 = parseInt(remove18(res));
                }
                let result = (stakeable2 - staked2).toString();
                stakeable.innerHTML =  formatNum(result) + " JED";
            })
        }).catch(function(err){
            console.log(err);
            updateStakeable();
        });

        chain.read.JedstarStakingRO.totalStakeable3().then(function(res){
            if (res <= 0){
                stakeable3 = parseInt(res);
            }else{
                stakeable3 = parseInt(remove18(res));
            }
            let stakeable = document.getElementById("stakeable3");
            chain.read.JedstarStakingRO.totalStaked3().then(function(res){
                if (res <= 0){
                    staked3 = parseInt(res);
                }else{
                    staked3 = parseInt(remove18(res));
                }
                let result = (stakeable3 - staked3).toString();
                stakeable.innerHTML =  formatNum(result) + " JED";
            })
        }).catch(function(err){
            console.log(err);
            updateStakeable();
        });
    }, 2000);


/* Update all the cards */
let updateStakes = setTimeout(function(){
    console.log("updating cards...");
    chain.read.JedstarStaking.addressMap1(user_wallet).then(function(res){
        let mykred = document.getElementById("mykred1");
        let max = document.getElementById("max1");
        // let total_rewards_p = document.getElementById("total-rewards-p1");
        let stake = document.getElementById("stake1");
        let input = document.getElementById("stakingAmount1");
        let rewards = document.getElementById("rewardsLabel1");
        mystaked1 = res;
        if (res.stakeAmt != 0){
            stake.classList.add("staked");
            mykred.style.display = "none";
            max.style.display = "none";
            // total_rewards_p.style.display = "none";
            rewards.innerHTML = "Total Accrued Reward Until Now";
            let staked = document.getElementById("stakingAmount1");
            if (staked){
                staked.value = remove18(res.stakeAmt);
            }
            maturity1 = timeToMaturity(parseInt(res.stakeTime)*1000, parseInt(stake_period1)); // maturity1 = timeToMaturity("December 28, 2022 15:31:10", 30)
            // const temp = new Date(parseInt(res.stakeTime)*1000);
            // console.log(temp);
            // console.log(maturity1);
           // Starts Countdown
           let time = document.getElementById("maturity1");
           let claim = document.getElementById("claim1");
           if (!interval1){
                console.log(1);
               startCountdown(interval1, maturity1, time, claim);
           }
           input.disabled = true;
           input.style.color = "white";
        }else{
            stake.classList.remove("staked");
            mykred.style.display = "block";
            max.style.display = "block";
            // total_rewards_p.style.display = "block";
            input.disabled = false;
            rewards.innerHTML = "Total Potential Reward based on 'Staking Amount'";
        }
    });
    
    chain.read.JedstarStaking.addressMap2(user_wallet).then(function(res){
        let mykred = document.getElementById("mykred2");
        let max = document.getElementById("max2");
        // let total_rewards_p = document.getElementById("total-rewards-p2");
        let stake = document.getElementById("stake2");
        let input = document.getElementById("stakingAmount2");
        let rewards = document.getElementById("rewardsLabel2");
        mystaked2 = res;
        if (res.stakeAmt != 0){
            stake.classList.add("staked");
            mykred.style.display = "none";
            max.style.display = "none";
            // total_rewards_p.style.display = "none";
            rewards.innerHTML = "Total Accrued Reward Until Now";
            let staked = document.getElementById("stakingAmount2");
            if (staked){
                staked.value = remove18(res.stakeAmt);
            }
            // let now = new Date();
            // now = Date.now();
            // maturity2 = timeToMaturity(now, 900);
            maturity2 = timeToMaturity(parseInt(res.stakeTime)*1000, parseInt(stake_period2)); // maturity2 = timeToMaturity("December 28, 2022 15:31:10", 30)
    
           // Starts Countdown
           let time = document.getElementById("maturity2");
           let claim = document.getElementById("claim2");
           if (!interval2){
               startCountdown(interval2, maturity2, time, claim);
           }
           input.disabled = true;
           input.style.color = "white";
        }else{
            stake.classList.remove("staked");
            mykred.style.display = "block";
            max.style.display = "block";
            // total_rewards_p.style.display = "block";
            input.disabled = false;
            rewards.innerHTML = "Total Potential Reward based on 'Staking Amount'";
        }
    });
    
    chain.read.JedstarStaking.addressMap3(user_wallet).then(function(res){
        let mykred = document.getElementById("mykred3");
        let max = document.getElementById("max3");
        // let total_rewards_p = document.getElementById("total-rewards-p3");
        let stake = document.getElementById("stake3");
        let input = document.getElementById("stakingAmount3");
        let rewards = document.getElementById("rewardsLabel3");
        mystaked3 = res;
        if (res.stakeAmt != 0){
            stake.classList.add("staked");
            mykred.style.display = "none";
            max.style.display = "none";
            // total_rewards_p.style.display = "none";
            rewards.innerHTML = "Total Accrued Reward Until Now";
            let staked = document.getElementById("stakingAmount3");
            if (staked){
                staked.value = remove18(res.stakeAmt);
            }
            maturity3 = timeToMaturity(parseInt(res.stakeTime)*1000, parseInt(stake_period3)); // maturity3 = timeToMaturity("December 28, 2022 15:31:10", 30)
    
           // Starts Countdown
           let time = document.getElementById("maturity3");
           let claim = document.getElementById("claim3");
           if (!interval3){
               startCountdown(interval3, maturity3, time, claim);
           }
           input.disabled = true;
           input.style.color = "white";
        }else{
            stake.classList.remove("staked");
            mykred.style.display = "block";
            max.style.display = "block";
            // total_rewards_p.style.display = "block";
            input.disabled = false;
            rewards.innerHTML = "Total Potential Reward based on 'Staking Amount'";
        }
    });
}, 1000);

    /* Total Staked Amount */
    var calculateTVL = function(){
        chain.read.JedstarStakingRO.totalStakedAmount1().then(function(res) {
            if (res <= 0){
                total_staked1 = parseInt(res);
            }else{
                total_staked1 = parseInt(remove18(res).toString());
            }
            let staked = document.querySelector("#period1 .total-staked-amount");
            if (staked) {
                staked.innerHTML = total_staked1 + " JED";
            }
            chain.read.JedstarStakingRO.totalStakedAmount2().then(function(res) {
                if (res <= 0){
                    total_staked2 = parseInt(res);
                }else{
                    total_staked2 = parseInt(remove18(res).toString());
                }
                let staked = document.querySelector("#period2 .total-staked-amount");
                if (staked) {
                    staked.innerHTML = total_staked2 + " JED";
                }
                chain.read.JedstarStakingRO.totalStakedAmount3().then(function(res) {
                    if (res <= 0){
                        total_staked3 = parseInt(res);
                    }else{
                        total_staked3 = parseInt(remove18(res).toString());
                    }   
                    let staked = document.querySelector("#period3 .total-staked-amount");
                    if (staked) {
                        staked.innerHTML = total_staked3 + " JED";
                    }
                    let tvl = document.getElementById('tvl');
                    if (tvl) {
                        /* 4000 (-18 0s) */
                        let result = (total_staked1 + total_staked2 + total_staked3).toString()
                        tvl.innerHTML =  formatNum(result) + " KRED";
                    }
                    // blockingModal(false);
                });
            });
        });
    }
    calculateTVL();


    /* Get Min and Max Stake Amount */
    let getMinAndMax = setTimeout(function(){
        console.log("updating min and max...");
        // Mins
        chain.read.JedstarStaking.minStakeAmt1().then(function(res){
            minstake1 = res;
        }).catch(function(err){
            console.log(err);
        });
        chain.read.JedstarStaking.minStakeAmt2().then(function(res){
            minstake2 = res;
        }).catch(function(err){
            console.log(err);
        });
        chain.read.JedstarStaking.minStakeAmt3().then(function(res){
            minstake3 = res;
        }).catch(function(err){
            console.log(err);
        });

        // Maxs
        chain.read.JedstarStaking.maxStakeAmt1().then(function(res){
            maxstake1 = res;
        }).catch(function(err){
            console.log(err);
        });
        chain.read.JedstarStaking.maxStakeAmt2().then(function(res){
            maxstake2 = res;
        }).catch(function(err){
            console.log(err);
        });
        chain.read.JedstarStaking.maxStakeAmt3().then(function(res){
            maxstake3 = res;
        }).catch(function(err){
            console.log(err);
        });
    }, 2000);

    /* Update all the Total Rewards Accrued */
    let updateAllRewardsAccrued = setTimeout(function(){
        console.log("updating all rewards accrued...");
        chain.read.JedstarStaking.interestEarnedUpToNowBeforeTaxesAndNotYetWithdrawn1(user_wallet).then(function(res){
            myallocated1 = formatNum(parseInt(res.slice(0,-16)));
            // let allocated = document.querySelector("#period1 .myallocated");
            let allocated = document.getElementById("value-after-apy1");
            if (allocated) {
                allocated.value = myallocated1;
            }
        }).catch(function(err){
            console.log(err);
            updateAllRewardsAccrued();
        });
    
        chain.read.JedstarStaking.interestEarnedUpToNowBeforeTaxesAndNotYetWithdrawn2(user_wallet).then(function(res){
            myallocated2 = formatNum(parseInt(res.slice(0,-16)));
            let allocated = document.getElementById("value-after-apy2");
            if (allocated){
                allocated.value = myallocated2;
            }
        }).catch(function(err){
            console.log(err);
            updateAllRewardsAccrued();
        });
    
        chain.read.JedstarStaking.interestEarnedUpToNowBeforeTaxesAndNotYetWithdrawn3(user_wallet).then(function(res){
            myallocated3 = formatNum(parseInt(res.slice(0,-16)));
            let allocated = document.getElementById("value-after-apy3");
            if (allocated) {
                allocated.value = myallocated3;
            }
        }).catch(function(err){
            console.log(err);
            updateAllRewardsAccrued();
        }); 
    }, 3000);

    /* Total Rewards Remaining ( stakeable - total staked) * min period * daily rate */

    let UpdateRewardsRemaining = setTimeout(function(){
        console.log("updating trr...");
        let trr = document.getElementById("trr");
        let rr1 = (stakeable1 - staked1) * convertSecToDay(stake_period1) * apy1;
        let rr2 = (stakeable2 - staked2) * convertSecToDay(stake_period2) * apy2;
        let rr3 = (stakeable3 - staked3) * convertSecToDay(stake_period3) * apy3;
        trr.innerHTML = formatNum((rr1 + rr2 + rr3).toFixed(2)) + " KRED";
    }, 5000);
    
    
    /* Value after APY */

    let stakingAmount1 = document.getElementById("stakingAmount1");
    stakingAmount1.addEventListener('input', function(event){
        let warning = document.getElementById("warning1");
        if (parseInt(stakingAmount1.value) > parseInt(remove18(maxstake1)) && !stakingAmount1.classList.contains("exceed")){
            stakingAmount1.classList.toggle("exceed"); 
            warning.classList.toggle("visible");
            warning.innerHTML = "Maximum stake amount is " + formatNum(remove18(maxstake1)) + " JED";
        }
        else if (parseInt(stakingAmount1.value) < parseInt(remove18(minstake1)) && !stakingAmount1.classList.contains("exceed")){
            stakingAmount1.classList.toggle("exceed"); 
            warning.classList.toggle("visible");
            warning.innerHTML = "Minimum stake amount is "+ formatNum(remove18(minstake1)) + " JED";

        }
        else if (stakingAmount1.classList.contains("exceed") && parseInt(stakingAmount1.value) >= parseInt(remove18(minstake1)) && parseInt(stakingAmount1.value) <= parseInt(remove18(maxstake1))){
            stakingAmount1.classList.toggle("exceed");
            warning.classList.toggle("visible");
        }
        let valueAfterAPY = document.getElementById("value-after-apy1");
        let apy = document.getElementById("apy1").value;
        let locking = document.getElementById("locking1");
        let after = calculateValueAfterAPY(stakingAmount1.value, apy);

        valueAfterAPY.value = after;
        locking.innerHTML = "Locking: " +after+ " JED for 30 Days";
    });

    let stakingAmount2 = document.getElementById("stakingAmount2");
    stakingAmount2.addEventListener('input', function(){
        let warning = document.getElementById("warning2");
        if (parseInt(stakingAmount2.value) > parseInt(remove18(maxstake2)) && !stakingAmount2.classList.contains("exceed")){
            stakingAmount2.classList.toggle("exceed"); 
            warning.classList.toggle("visible");
            warning.innerHTML = "Maximum stake amount is " + formatNum(remove18(maxstake2)) + " JED";
        }
        else if (parseInt(stakingAmount2.value) < parseInt(remove18(minstake2)) && !stakingAmount2.classList.contains("exceed")){
            stakingAmount2.classList.toggle("exceed"); 
            warning.classList.toggle("visible");
            warning.innerHTML = "Minimum stake amount is "+ formatNum(remove18(minstake2)) + " JED";

        }
        else if (stakingAmount2.classList.contains("exceed") && parseInt(stakingAmount2.value) >= parseInt(remove18(minstake2)) && parseInt(stakingAmount2.value) <= parseInt(remove18(maxstake2))){
            stakingAmount2.classList.toggle("exceed");
            warning.classList.toggle("visible");
        }
        let valueAfterAPY = document.getElementById("value-after-apy2");
        let apy = document.getElementById("apy2").value;
        let locking = document.getElementById("locking2");
        let after = calculateValueAfterAPY(stakingAmount2.value, apy);

        valueAfterAPY.value = after;
        locking.innerHTML = "Locking: " +after+ " JED for 60 Days";
    });

    let stakingAmount3 = document.getElementById("stakingAmount3");
    stakingAmount3.addEventListener('input', function(){
        let warning = document.getElementById("warning3");
        if (parseInt(stakingAmount3.value) > parseInt(remove18(maxstake3)) && !stakingAmount3.classList.contains("exceed")){
            stakingAmount3.classList.toggle("exceed"); 
            warning.classList.toggle("visible");
            warning.innerHTML = "Maximum stake amount is " + formatNum(remove18(maxstake3)) + " JED";
        }
        else if (parseInt(stakingAmount3.value) < parseInt(remove18(minstake3)) && !stakingAmount3.classList.contains("exceed")){
            stakingAmount3.classList.toggle("exceed"); 
            warning.classList.toggle("visible");
            warning.innerHTML = "Minimum stake amount is "+ formatNum(remove18(minstake3)) + " JED";

        }
        else if (stakingAmount3.classList.contains("exceed") && parseInt(stakingAmount3.value) >= parseInt(remove18(minstake3)) && parseInt(stakingAmount3.value) <= parseInt(remove18(maxstake3))){
            stakingAmount3.classList.toggle("exceed");
            warning.classList.toggle("visible");
        }
        let valueAfterAPY = document.getElementById("value-after-apy3");
        let apy = document.getElementById("apy3").value;
        let locking = document.getElementById("locking3");
        let after = calculateValueAfterAPY(stakingAmount3.value, apy);

        valueAfterAPY.value = after;
        locking.innerHTML = "Locking: " +after+ " JED for 90 Days";
    });

    /* Max Buttons */

    let max1 = document.getElementById("max1");
    max1.addEventListener("click", function(){
        stakingAmount1.value = parseInt(remove18(maxstake1));
        let valueAfterAPY = document.getElementById("value-after-apy1");
        let apy = document.getElementById("apy1").value;
        let locking = document.getElementById("locking1");
        let after = calculateValueAfterAPY(stakingAmount1.value, apy);

        valueAfterAPY.value = after;
        locking.innerHTML = "Locking: " +after+ " JED for 30 Days";
    });



    let max2 = document.getElementById("max2");
    max2.addEventListener("click", function(){
        stakingAmount2.value = parseInt(remove18(maxstake2));
        let valueAfterAPY = document.getElementById("value-after-apy2");
        let apy = document.getElementById("apy2").value;
        let locking = document.getElementById("locking2");
        let after = calculateValueAfterAPY(stakingAmount2.value, apy);

        valueAfterAPY.value = after;
        locking.innerHTML = "Locking: " +after+ " JED for 60 Days";
    });

    let max3 = document.getElementById("max3");
    max3.addEventListener("click", function(){
        stakingAmount3.value = parseInt(remove18(maxstake3));
        let valueAfterAPY = document.getElementById("value-after-apy3");
        let apy = document.getElementById("apy3").value;
        let locking = document.getElementById("locking3");
        let after = calculateValueAfterAPY(stakingAmount3.value, apy);

        valueAfterAPY.value = after;
        locking.innerHTML = "Locking: " +after+ " JED for 90 Days";
    });
}

/* Helper Functions */

function convertSecToDay(s) {
    return s / 86400;
    // return s / 30;
}

function timeToMaturity(t, x) {
    let stake_time = new Date(t);

    if (Object.prototype.toString.call(stake_time) === "[object Date]") {
         // it is a date
        if (isNaN(stake_time)) { // d.getTime() or d.valueOf() will also work
            // date object is not valid
            console.log("Date is invalid, retrying...");
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

String.prototype.replaceAt = function(i, x) {
    return this.substring(0, i) + x + this.substring(i + x.length);
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

function calculateValueAfterAPY(input, apy){
    return ((1 + (parseInt(apy) / 365 * 30)) * input).toFixed(2);
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

function stakingPopup(x){
    let popup = document.getElementById("info-popup");
    let title = document.getElementById("popup-title");
    let text = document.getElementById("popup-text");
    let button = document.getElementById("popup-button-text");
    let close_button = document.querySelector(".close-button");
    let authorize_button = document.getElementById("authorize-button-div");
    let stake_button = document.getElementById("stake-button-div");
    if (!authorize_button.classList.contains("popup-button-hidden")){authorize_button.classList.toggle("popup-button-hidden");}
    if (stake_button.classList.contains("popup-button-hidden")){stake_button.classList.toggle("popup-button-hidden");}
    title.innerHTML = "Staking";
    text.innerHTML = "Your allowance has been approved. Click the button below to proceed to staking.";
    // button.innerHTML = "Stake";
    button.addEventListener('click', function(){
        let stake = document.getElementById("stakeBtn" + x);
        stake.click();
        close_button.click();
    });
}

function resetPopup(){
    let title = document.getElementById("popup-title");
    let text = document.getElementById("popup-text");
    let button = document.getElementById("popup-button-text");
    let authorize_button = document.getElementById("authorize-button-div");
    let stake_button = document.getElementById("stake-button-div");
    title.innerHTML = "Authorization";
    text.innerHTML = "You don't have enough staking allowance. Please click the authorize button to increase your staking allowance.";
    // button.innerHTML = "Authorize";
    if (authorize_button.classList.contains("popup-button-hidden")){authorize_button.classList.toggle("popup-button-hidden");}
    if (!stake_button.classList.contains("popup-button-hidden")){stake_button.classList.toggle("popup-button-hidden");}
}

function startCountdown(bool, maturity, DOM, claim){
    // clearInterval(interval);
    if (Object.prototype.toString.call(maturity) === "[object Date]") {
        // it is a date
        if (isNaN(maturity)) { // d.getTime() or d.valueOf() will also work
            // date object is not valid
            console.log("Date is not valid, retrying...");
            let retry = setTimeout(function(){
                startCountdown(bool, maturity, DOM, claim)
            }, 1000);
        } else {
            // date object is valid
            console.log("starting countdown: " + DOM.id);
            bool = true;
            var interval = setInterval(function(){
                var time_now = new Date().getTime();
                diff = maturity - time_now;
                var days = Math.floor(diff / (1000 * 60 * 60 * 24));
                var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
                var dDisplay = days > 0 ? days + (days== 1 ? " day " : " days ") : "";
                var hDisplay = hours > 0 ? hours + (hours == 1 ? " hour " : " hours ") : "";
                var mDisplay = minutes > 0 ? minutes + (minutes == 1 ? " minute " : " minutes ") : "";
                var sDisplay = seconds > 0 ? seconds + (seconds == 1 ? " second" : " seconds") : "";
        
                // let time = document.querySelector("#period1 .maturity");
                // let time = document.getElementById("maturity1");
                if (DOM){
                    DOM.value = dDisplay + hDisplay + mDisplay + sDisplay;
                }
        
                if (diff <= 0) {
                    clearInterval(interval);
                    DOM.value = "Ready To Claim";
                    if (claim){
                        claim.disabled = false;
                    }
                    bool = false;
                }
                }, 1000);
        }
    } else {
        // not a date object
    }
}

function secToDHMS(seconds) {
    seconds = Number(seconds);
    var d = Math.floor(seconds / (3600*24));
    var h = Math.floor(seconds % (3600*24) / 3600);
    var m = Math.floor(seconds % 3600 / 60);
    var s = Math.floor(seconds % 60);

    var dDisplay = d > 0 ? d + (d == 1 ? " day " : " days ") : "";
    var hDisplay = h > 0 ? h + (h == 1 ? " hour " : " hours ") : "";
    var mDisplay = m > 0 ? m + (m == 1 ? " minute " : " minutes ") : "";
    var sDisplay = s > 0 ? s + (s == 1 ? " second" : " seconds") : "";
    return dDisplay + hDisplay + mDisplay + sDisplay;
}

Date.prototype.time_until = function(t){let d=t-this; return {days:Math.floor((((d/1000)/60)/60)/24),hours:Math.floor(((d/1000)/60)/60) % 24,mins:Math.floor((d/1000)/60) % 60,secs:Math.floor(d/1000) % 60}};

/*start_datetime=new Date(Date.parse(a[c].start_time.replace(" ","T")+".000Z")) (in UTC)*/