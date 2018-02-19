function statusChangeCallback(response) {
    //console.log('statusChangeCallback');
    //console.log(response);
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    
    
    
    if (response.status === 'connected') {
      // Logged into your app and Facebook.
      testAPI();
    } else {
      // The person is not logged into your app or we are unable to tell.
      //document.getElementById('status').innerHTML = 'Please log ' + 'into this app.';
      
      /*FB.init({
		    appId  : THEAPPPIDDDD,
		        status : true,
		        cookie : true,
		        xfbml  : true,
		});*/
      
      // FB.login(function(response) {

      //   testAPI();

      // }, {scope: 'public_profile,email'});
        var uri = encodeURI('http://www.satana-furniture.com.tw/site/');
        //window.location = encodeURI("https://www.facebook.com/dialog/oauth?client_id=" + appId + "&redirect_uri="+uri+"&response_type=token");
		window.open(encodeURI("https://www.facebook.com/dialog/oauth?client_id=" + appId + "&redirect_uri="+uri+"&response_type=token"), "_self");
        //FB.login(fbLoginCheck,{ scope: "user_about_me,user_location,user_birthday,email,publish_stream"});

    }
  }


  function fbLoginCheck(response){
     if(response.status != 'unknown'){
       //reload or redirect once logged in...
       
       window.location.reload();
    }
   }

  // This function is called when someone finishes with the Login
  // Button.  See the onlogin handler attached to it in the sample
  // code below.
  function checkLoginState() {
    
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }



  // // Load the SDK asynchronously
  // (function(d, s, id) {
  //   var js, fjs = d.getElementsByTagName(s)[0];
  //   if (d.getElementById(id)) return;
  //   js = d.createElement(s); js.id = id;
  //   js.src = "//connect.facebook.net/en_US/sdk.js";
  //   fjs.parentNode.insertBefore(js, fjs);
  // }(document, 'script', 'facebook-jssdk'));

  // Here we run a very simple test of the Graph API after login is
  // successful.  See statusChangeCallback() for when this call is made.
  function testAPI() {
    
    
    $.blockUI({message: 'Welcome!  Fetching your information.... ', timeout: 1000, css: {"font-size": '24px', 'padding':'20px'}});
    
    //console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', {fields: 'name, email' }, function(response) {
      // console.log('Successful login for: ' + response.name);
      // document.getElementById('status').innerHTML =
      //   'Thanks for logging in, ' + response.name + '!';
      userLogin(response.id, response.name, response.email);
    });
  }