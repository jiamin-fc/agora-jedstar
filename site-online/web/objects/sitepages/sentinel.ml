<div class="index-container">
    <div id="myagora-cards" class="myagora-cards" style = "display:block">
        <div id = "userNotLogged" class="title">
            <h2 class="capitalise">please log in</h2>
            <p class="capitalise">Log in with your wallet to begin.</p>
            <button id="connect_btn2" class="capitalise"><div><img src="/img/agora/wallet.svg"> <span>Log in</span></div></button>
        </div>
    </div>

    <div class="content-container" id = "currencies" style = "display:none">
        <div class="title">
            <h2>The wallet connected is <span id = "walletaddress">0x8374…bb7f</span></h2>
        </div>
        
        <div class="currency-container">
            <div id = "JED" class="currency">
                <div id = "JEDlogo" class="currency-logo">
                    <img src="img/pick1.png" alt="menu">
                </div>
                <div class="currency-message auth-fee none">
                    <p>Status: disabled.<br>To enable, pay tax of <span class = "JEDunlockfee" >...</span> JED</p>
                </div>
                <div class = "currency-message auth-text none" id = "JED-authorised-text" ><p>Inactive<br>Pay <span class = "JEDunlockfee" >...</span> JED to activate wallet</p></div>
                <div class = "currency-message auth-text none" id = "JED-no-fee"><p>Active</p></div>
                <div class="button">
                    <button id = "JEDauth" class="capitalise authoriseBtn" disabled = "true"><div>authorise</div></button>
                </div>
            </div>
            <div id = "KRED" class="currency">
                <div id = "KREDlogo" class="currency-logo">
                    <img src="img/pick2.png" alt="menu">
                </div>
                <div class="currency-message auth-fee none">
                    <p>Status: disabled.<br>To enable, pay tax of <span class = "KREDunlockfee" >...</span> KRED</p>
                </div>
                <div class = "currency-message auth-text none" id = "KRED-authorised-text" ><p>Inactive<br>Pay <span class = "KREDunlockfee" >...</span> KRED to activate wallet</p></div>
                <div class = "currency-message auth-text none" id = "KRED-no-fee"><p>Active</p></div>
                <div class="button">
                    <button id = "KREDauth" class="capitalise authoriseBtn" disabled = "true"><div>authorise</div></button>
                </div>
            </div>
        </div>
        <div class="button activate">
            <div id = "activate-message" class = "none" ><p>Authorise both currencies in order to activate your wallet</p></div>
            <button id = "activate-button" class="capitalise" disabled = "true"><div>activate</div></button>
        </div>
    </div>
</div>