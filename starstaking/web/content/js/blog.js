(function(exports){
  var showToast = function(msg, classname){
    let all_toasts = document.getElementsByClassName("toast");
    let top_position;
    if (all_toasts.length > 0){
      top_position = (all_toasts[all_toasts.length - 1].offsetTop + all_toasts[all_toasts.length - 1].offsetHeight + 25)+"px";
    }
    let rand_id = Math.floor(Math.random()*1000);
    let t = document.createElement("DIV");
    t.id = "toast"+rand_id;
    t.innerHTML = msg;
    t.className = "toast "+classname;
    if (top_position) t.style.top = top_position;

    document.getElementsByTagName("body")[0].appendChild(t);
    t.addEventListener("mouseover", function(){
      t.classList.add("animpaused");
    });
    t.addEventListener("mouseout", function(){
      t.classList.remove("animpaused");
    });
    t.addEventListener("animationend", function(ev){
      if (ev.animationName == "slide-in"){
        t.parentElement.removeChild(t);
      }
    });
  };
  var checkHuman = function(){
    if (typeof grecaptcha == 'object' && grecaptcha != null && typeof grecaptcha.execute == "function"){
      setTimeout(function(){ grecaptcha.reset(); grecaptcha.execute(); },10);
    }else{
      setTimeout(checkHuman, 2000);
    }
  }
  var recapFn = function(captchaToken){
    let email = document.getElementById("emailaddress");
    let joinbtn = document.getElementById("joinnewsletter");
    ajax({
      "action": "joinlist",
      "email": email.value,
      "token": captchaToken
    }).then(function(res){
      if (res.status == "ok"){
        showToast("We have sent you a confirmation message! Please check your mailbox!", "good");
        email.value = "";
      }else{
        showToast("There was an error adding you to the mailing list! The server said: "+res.msg, "bad");
      }
      joinbtn.disabled = false;
      joinbtn.src = joinbtn.original_src;
      email.disabled = false;
    }).catch(function(err){
      showToast("There was a problem adding you to the mailing list! Please try again later.", "bad");
      joinbtn.disabled = false;
      joinbtn.src = joinbtn.original_src;
      email.disabled = false;
    });
  };
  exports.recaptchaResult = recapFn;
  var enable_email_subscription_btn = function(){
    var joinbtn = document.getElementById("joinnewsletter");
    joinbtn.addEventListener("click", function(){
      if (joinbtn.disabled){ return false; }

      let email = document.getElementById("emailaddress");
      if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value)){
        email.disabled = true;
        joinbtn.disabled = true;
        joinbtn.original_src = joinbtn.src;
        joinbtn.src = "https://assets.jedstar.space/img/sv_loading_lo.gif";
        checkHuman();
      }else{
        showToast("Your email address does not appear correct. Please check it and try again!", "bad");
      }

    })
  };
  shield("https://www.google.com/recaptcha/api.js", enable_email_subscription_btn, "head", "script", 0);
})(window)
