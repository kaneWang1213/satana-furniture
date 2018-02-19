var blockFormat = {message: '', timeout: 1000, css: {"font-size": '24px', 'padding':'20px', cursor:'default'}, overlayCSS: {"cursor": "default"}, onUnblock: function() {}};

$(function() {

    if(sessionStorage.userId == null) {
        $("body").removeClass("authoried");
    } else {
        $("body").addClass("authoried");
    }

    $(".navbar-right li").click(function(){
        if($(this).hasClass("login")) {             //登入
            //$.blockUI({message: $(".blackFrame .sign-frame")[0]});
            logInFun();
        }else if($(this).hasClass("logout")) {      //登出
            sessionStorage.clear();
            $("body").removeClass("authoried");
        } else {
            
        }
        
    });

    $(".sign-frame .fb").click(function(){
        checkLoginState();
    });
    
    $(".sign-frame .cross").click(function() {
        $(this).unbind();
        $(".sign-frame")[0].className = "sign-frame";
        $.unblockUI();
    });

    $(".sign-frame .registorBtn").click(function(){
        $(".sign-frame")[0].className = "sign-frame";
        $(".sign-frame").addClass("registor");
    });

    $(".sign-frame .registorConfirm").click(function(){
        alert("C");
        $.post("fetchdata.php", $("form#registor").serialize(), function(callback){
            var _data =  $.parseJSON(callback);


            if(!_data.PhoneState && !_data.EmailState) {
                alert("註冊完成");
                var _form = $("form#registor")[0];
                signState(_form.userId.value, _form.name.value, _form.email.value, _form.phone.value, _form.address.value, true);
                $.unblockUI();

            }
            if(_data.PhoneState) {
                alert("該電話已註冊過了");
            }
            if(_data.EmailState) {
                alert("該信箱已註冊過了");
            }
        });
    });

    $(".sign-frame .loginBtn").click(function(){
        $.post("fetchdata.php", $("form#login").serialize(), function(callback){
            var _data =  $.parseJSON(callback);
            if(_data.State == "SUCCESS") {
                blockFormat.message = "登錄成功";
                signState(_data.id, _data.name, _data.email, _data.phone, _data.address, false);
            } else {
                blockFormat.message = "登錄失敗";
            }
            blockFormat.onUnblock = function () {};
            $.blockUI(blockFormat);
        });
    });


    $(document).on("focus click", ".sign-frame input", function(event) {
        var _div = $(this).parent();
        $(_div).addClass("onFocus");
    });

    $(document).on("focusout", ".sign-frame input", function(event) {
        if(this.value == "") {
            var _div = $(this).parent();
            $(_div).removeClass("onFocus");
        }
    });

});



window.fbAsyncInit = function() {
	
    FB.init({
      appId      : appId,
      xfbml      : true,
      status	 : true,
      cookie	 : false,
      oauth		 : true,
      version    : 'v2.10'
    });
    FB.AppEvents.logPageView();
};



// (function(d, s, id){
//     var js, fjs = d.getElementsByTagName(s)[0];
//     if (d.getElementById(id)) {return;}
//     js = d.createElement(s); js.id = id;
//     js.src = "//connect.facebook.net/en_US/sdk.js";
//     fjs.parentNode.insertBefore(js, fjs);
// }(document, 'script', 'facebook-jssdk'));

// Get Facebook Connect JS and append it to the DOM
   (function() {
   var e = document.createElement('script'); 
    e.async = true;
    e.src = document.location.protocol+'//connect.facebook.net/en_US/all.js#xfbml=1';
    document.getElementById('fb-root').appendChild(e);
   }());

function userLogin(_id, _name, _email) {
    $.post("/satana/fetchdata.php", {"fbLogin":true ,"id": _id, "name": _name, "email": _email}, function(callback){
        var _data =  $.parseJSON(callback)

        signState(_id, _name, _email, _data.Phone, _data.Address, _data.newJoin);
        //$.unblockUI();
    });
}

function signState(_id, _name, _email, _phone, _address, _newJoin) {
    $("body").addClass("authoried");
    sessionStorage.userName = _name;
    sessionStorage.userId = _id;
    sessionStorage.userEmail = _email;
    sessionStorage.newJoin = _newJoin;
    sessionStorage.phone = _phone;
    sessionStorage.address = _address;
}

function logInFun() {
    $.blockUI({message: $(".blackFrame .sign-frame")[0], css: {"cursor": "default", "width":"520px", "border": "none"}, overlayCSS: {"cursor": "default"}});
    $(".blockUI .sign-frame")[0].className = "sign-frame";
    $(".blockUI .sign-frame").addClass("login");
    var _profileForm = $("form#profile")[0];
    var _profileArr = [
        [_profileForm.name, sessionStorage.userName],
        [_profileForm.phone, sessionStorage.phone],
        [_profileForm.email, sessionStorage.userEmail],
        [_profileForm.address, sessionStorage.address]
    ];

    $(_profileArr).each(function(){

        if(this[1] != "undefined" && this[1] != undefined) {
            this[0].value = this[1];
        }

    });

    $(".sign-frame .cross").click(function() {
        $(this).unbind();
        $.unblockUI();
    });
}

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
          tmp = item.split("=");
          if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
    return result;
}