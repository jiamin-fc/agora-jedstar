let data;

window.onload = function(){
    let curr_url = window.location.href;
    let token = curr_url.split('#')[1];
    if (token) {
      retrieveData(token);
    }else{
      retrieveData("57896044618658097711785492504343953927315557066662158946655541218820101242883");
    }


  const buttons = document.querySelectorAll('.buttons');
    buttons.forEach(button => {
        button.addEventListener('click', navigationButtons);
    });
  
  const buy_button = document.getElementById('purchase-button');
  buy_button.addEventListener('click', function(){
    console.log("Purchase button clicked!");
    // servercomms.sign_po(item_name, product_id, ticker, value);
  })
}

// This function retrieves the data by calling an API
function retrieveData(token){
    let req = new XMLHttpRequest();
    URL = "https://nft.jedstar.com/token/" + token;
    req.open("GET", URL);
    req.setRequestHeader("Accept", "application/json");
    req.send();
    req.onload=function(){
        data = JSON.parse(req.responseText);
        console.log(data);
        populateData(data);
				document.querySelector(".loading-screen").style.display = "none";
    }
}


// This function populates and manipulates the DOM with data
function populateData(data){
    let traits = [];
    let boosts = [];
    let bonuses = [];
		let name = document.getElementsByClassName("nft-detail-name")[0].children[1].children[0];
    let image = document.getElementsByClassName("nft-card-image")[0].children[0];
    let description = document.getElementsByClassName("nft-detail-description")[0].children[3];
    let attributes = document.getElementsByClassName("nft-detail-attributes")[0].children[2];
    let nft_boost = document.getElementsByClassName("nft-detail-boosts")[0].getElementsByClassName("boost");

    name.textContent = data.name;
    image.src = data.image;
		if(description) description.textContent = data.description;

    // Assigning each trait_type into its respective arrays
    for (let attribute of data.attributes){
        if (attribute.max_value !== undefined) {
            traits.push(attribute);
        }else if (attribute.display_type !== undefined) {
            boosts.push(attribute);
        }else{
            bonuses.push(attribute);
        } 
    }

    // Populate the attribute numbers
    let attributes_values = attributes.getElementsByClassName("attr-val");
		
    /*for (let i = 0; i < attributes_values.length; i++){
        for (let j = 0; j < traits.length; j++){
            if (attributes_values[i].id === traits[j].trait_type){
                attributes_values[i].textContent = traits[j].value;
								console.log("a", attributes_values[i]);
								attributes_values[i].css.width = traits[j].value+"%";
            }
        }
    }*/
		
		for (let i = 0; i < traits.length; i++){
			let t = document.querySelector("#"+traits[i].trait_type);
			
			if(traits[i].value > 0){
				t.childNodes[0].childNodes[0].style.width = traits[i].value+"%";
				t.childNodes[1].innerHTML = traits[i].value;
			}else{
				t.classList.add("disabled");
			}
			
		}

    // Populate the boost percentage numbers
    for (let boost of boosts) {
        switch(boost.trait_type) {
            case "Silvervolt Bid Boost":
                document.getElementById("boost-bid").textContent = boost.value + "%";
								document.getElementById("boost-bid").parentNode.parentNode.parentNode.classList.remove("off");
                break;
            case "Silvervolt Mining Boost":
                document.getElementById("boost-mining").textContent = boost.value + "%";
								document.getElementById("boost-mining").parentNode.parentNode.parentNode.classList.remove("off");
                break;
            case "Silvervolt Referral Boost":
                document.getElementById("boost-referral").textContent = boost.value + "%";
								document.getElementById("boost-referral").parentNode.parentNode.parentNode.classList.remove("off");
                break;
        }
    }

    // Populate the bonus percentage numbers
    for (let bonus of bonuses) {
        switch(bonus.trait_type) {
            case "Silvervolt Wheel Spin Bonus":
                document.getElementById("boost-wheel").textContent = bonus.value[0] + "%";
								document.getElementById("boost-wheel").parentNode.parentNode.parentNode.classList.remove("off");
                break;
            case "Silvervolt Lottery Bonus":
                document.getElementById("boost-lottery").textContent = bonus.value[0] + "%";
								document.getElementById("boost-lottery").parentNode.parentNode.parentNode.classList.remove("off");
                break;
        }
    }
}

function navigationButtons(event) {
  let id = event.target.id;
  if (id === undefined || id === "") {
      id = event.target.parentElement.id;
  }
  
  switch(id){
      case 'myagora':
          // window.location.href = 'http://localhost/agora/html-cutups/agora-myagora.html';
          window.location.href = './agora-myagora.html';
          break;
      case 'mywallet':
          break;
  }
}