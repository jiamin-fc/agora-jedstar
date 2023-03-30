// console.log("Index JS loaded");

var req, arr, start_countdown, token;
let countdown_time = 30;
let tx;
let MIN_PERCENTAGE = 10;

Agora.hook("login", function(msg){
  console.log("logged in");
  document.getElementById("mywallet2").innerHTML = "<div>"+msg.substr(0,5)+"..."+msg.substr(-4)+"</div>";
})
Agora.hook("logout", function(msg){
  document.getElementById("mywallet2").innerHTML = "<div>Connect Wallet</div>";
});
Agora.on("ready").then(function(){
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if(!entry.isIntersecting) return;
            let arr = entry.target.children;
            for(const elem of arr){
                updateMintCount(elem);
            }
            if (entry.isIntersecting) observer.unobserve(entry.target);

        })
    },{
        rootMargin: "70px",
    });

    // obeserving cards
    const cards = document.querySelectorAll(".cards");
    cards.forEach(card =>{
        observer.observe(card);
    });

    // 'Buy now' buttons
    /*const card_buttons = document.querySelectorAll(".card-button");
    for(let button of card_buttons){
        button.addEventListener('click', cardBuyButton);
    }*/

    // 'My Agora' button
    const buttons = document.querySelectorAll('.buttons');
    buttons.forEach(button => {
        button.addEventListener('click', navigationButtons);
    });

    //utilities
    if (document.getElementById("viewUtilities")){
        document.getElementById("viewUtilities").addEventListener("click", function(){
            modalPopup("utilities");
        });
    }

    //learn more
    if (document.getElementById("learnMore")){
        document.getElementById("learnMore").addEventListener("click", function(){
            window.open('https://www.jedstar.com/genesismint/', '_blank').focus();
        });
    }

    //how to buy
    if (document.querySelector(".about-more")){
        document.querySelector(".about-more").addEventListener("click", function(){
            document.querySelector(".about").classList.toggle("close");
        });
    }
    //how to buy
    document.getElementById("viewBuy").addEventListener("click", function(){
        modalPopup("buy");
    });
    //connect wallet btn
    document.getElementById("mywallet2").addEventListener("click", function(){
        document.getElementById("connect_btn").click();
    });
    //buy now button
    var cardBtn = document.getElementsByClassName("buynow");
    for(let x=0;x<cardBtn.length;x++){
      cardBtn[x].addEventListener("click", function(){
        window.location = "/buy/"+this.dataset["productid"];
      });
    }

});

function cardBuyButton(event) {
    // let curr_url = 'http://localhost/agora/html-cutups/agora-nft-page.html#';
    let curr_url = '../../../../agora/html-cutups/agora-nft-page.html#';
    let temp_id = '57896044618658097711785492504343953927315557066662158946655541218820101242883';
    let prod_id = event.target.attributes['data-productid'];
    if (prod_id === undefined) {
        prod_id = event.target.parentElement.attributes['data-productid'].value;
    }else{
        prod_id = prod_id.value;
    }
    // console.log("Click on product id: "+prod_id);
    window.location.href = curr_url + temp_id;
}

function navigationButtons(event) {
    let id = event.target.id;
    if (id === undefined || id === "") {
        id = event.target.parentElement.id;
    }

    switch(id){
        case 'myagora':
            // window.location.href = 'http://localhost/agora/html-cutups/agora-myagora.html';
          window.location.href = '/mine';

            break;
        case 'mywallet':
            break;
    }
}

function populateDepositHistory(token){
    servercomms.get_deposit_history(wallet, token).then(function(res){
        console.log("In deposit history");
        let data = res['tx'];
        console.log(data);
    }).catch(console.error);


}

function populateSpendHistory(token){
    servercomms.get_spend_history(wallet, token).then(function(res){
        console.log("In spend history");
        let data = res['tx'];
        for (let el of data){
            console.log(el);
        }
    }).catch(console.error);
}

function populateBalance(token){
    servercomms.get_balances(wallet, token).then(function(res){
        console.log("Getting the user's balance...");
        let balances = res['balances'];
        console.log(balances);
    }).catch(console.error);
}


function updateMintCount(card){
    if (!card.classList.contains('card')) return;
    let productId = card.attributes['data-productid'].value;
    let maxMint = card.attributes['data-maxmint'].value;
    // console.log("Updating card ID: " + productId);

    chainRead.minted_count(productId).then(function(res){
      if (res / maxMint >= 0.75){
        let num_remain = document.getElementById("remain_prod"+productId);
        num_remain.innerHTML = maxMint - res;
        checkMintCount(productId, res, maxMint);
      }else{
        let label_remain = document.getElementById("label-remain-prod"+productId);
        label_remain.innerHTML = "<p class='capitalise'>"+res+" minted</p>";
      }

    }).catch(console.error);
}

function checkMintCount(id, a, b) {
    if (a / b * 100 < MIN_PERCENTAGE) {
        // apply css
        let num_remain = document.getElementById("remain_prod"+id).parentNode;
        // console.log(num_remain);
        num_remain.style.color = "red";

    }
}

function updateCountdown() {
    // let countdown = document.getElementById("countdown");
    // countdown.innerHTML = time;
    countdown_time--;
    if (countdown_time === 0){
        clearInterval(start_countdown);
        // enable the refresh button
    }
}


// carousel pagination 

var carousel = document.querySelector(".carousel"),
		carouselWidth = carousel.scrollWidth / (carousel.children.length)
		page = document.querySelector(".carousel-pagination");
			
window.addEventListener("resize", (event) => {
	carouselWidth = carousel.scrollWidth / (carousel.children.length);
	
	clearPageActive();
	
	carousel.scrollTo({
		left: 0,
		behavior: 'smooth'
	});
	page.children[0].classList.add("active");
});

page.children[0].classList.add("active");

for(let x=1; x<page.children.length+1; x++){
	
	document.querySelector(".page"+x).addEventListener("click", function(){
		let tab = document.querySelector(".tabpage"+x);
		
		console.log("tab:", tab);
		clearPageActive();
		
		carousel.scrollTo({
			left: carouselWidth*(x-1),
			behavior: 'smooth'
		});
	});
}

function clearPageActive(){
	for(let x=0; x<page.children.length; x++){
		page.children[x].classList.remove("active");
	}
}