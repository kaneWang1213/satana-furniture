<html xmlns:fb="https://www.facebook.com/2008/fbml" xmlns:og="https://ogp.me/ns#">

    <?php
        header("Content-Type:text/html;Charset:utf-8");
        $login = true;
        include 'include/header.php';
    ?>

	<script>

        
        function getParameterByName(name) {
    	   var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    	   return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
		}
		
		//$(document).ready(initi);
		var _userName = "";
		
		$(function () {
			var message = window.getParameterByName("message");

			if(message=='error'){
				alert('帳號密碼錯誤!!');
			} else if(message=='logtime') {
				alert('網路逾時, 請重新登入!!');
			}
						
			$("input[type=text],input[type=password]").keypress(function(event) {
                if ( event.which == 13 ) {
                    buttonEvent(event);
                }
            }); 
		});

		
		function buttonEvent(event){
		    _table = $(event.target).parents(".innerFrame");
            var _account = $(_table).find("input[type=text]")[0];
            var _password = $(_table).find("input[type=password]")[0];
            
            if(_account.value !='' && _password.value != '')
            {
                var form = document.createElement("form");
                form.setAttribute("method", 'post');
                form.setAttribute("action", 'auth.php');
                
                var field = document.createElement("input");
                field.setAttribute("type", "hidden");
                field.setAttribute("name", 'account');
                field.setAttribute("value", _account.value);
                form.appendChild(field);
                
                var field = document.createElement("input");
                field.setAttribute("type", "hidden");
                field.setAttribute("name", 'password');
                field.setAttribute("value", _password.value);
                form.appendChild(field);
                
                document.body.appendChild(form);
                form.submit();
            }
            else
            {
                alert('請輸入帳密');
            }   
		}



	</script>
	<style>
	    .navbar-brand {
            left: 50%;
            margin-left: -93px !important;
            width: 187px;
        }
	</style>


<body>
    <div class="container">
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container"> 
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="#" class="navbar-brand"></a>
                </div>
            </div>
        </nav>
       
        
        <div class="mainContent container">
            <div class="contentInner">
              
              
                <div class="section login">
                        <div class="innerFrame">
                            <div class="titleDivid">
                                <h2><?= web_title ?></h2>
                                <h5>後台管理 輸入帳號密碼</h5>
                            </div>
                             
                             <div class="typeDivid">
                                <label>帳號：</label>
                                <input type="text" placeholder="input your account" />
                             </div>
                             
                             <div class="typeDivid">
                                <label>密碼：</label>
                                <input type="password" placeholder="input your password" />
                             </div>

                             <div>
                                <input type="button" class="login" onclick="buttonEvent(event)" value="LOGIN IN" />
                             </div>
                         </div>
                    </div>
                
            </div>
        </div>
    </div>
    <footer>
        <div class="container" style="text-align:center;">
            copyright © 2014 FASHIONER All Rights Reserved
        </div>
        <!--<nav class="navbar navbar-fixed-bottom"></nav>-->
    </footer> 
    
    <div class="blackFrame itemFrame loadingFrame hide"><div class="loadingInner"><span>now loading...</span></div></div>
</body>
</html>