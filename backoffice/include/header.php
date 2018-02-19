<?php
    session_start();
    if($_SESSION['UserName'] == null && !$login) {
        header('Location: login.php?message=error');
    }
    header("Content-Type:text/html;Charset:utf-8");
    include 'include/config.php';
?>

<head>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/backoffice.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1" />
    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.1/summernote.css" rel="stylesheet">
    <script
			  src="https://code.jquery.com/jquery-1.12.4.min.js"
			  integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
			  crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/backoffice.js"></script>
    <title><?= web_title; ?></title>
</head>


<?php if(!$login) { ?>

<nav class="navbar navbar-default navbar-fixed-top">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a href="index.php" class="navbar-brand">
            <img src="img/brand.png" >
        </a>
    </div>
     <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
            
            <li onclick="NavBtn(this)" class="basic"><a href="basic.php"><span>網站編輯</span></a></li>
            <li onclick="NavBtn(this)" class="product"><a href="product.php"><span>商品編輯</span></a></li>
            <li onclick="NavBtn(this)" class="banner"><a href="banner.php"><span>廣告管理</span></a></li>
            <li onclick="NavBtn(this)" class="order"><a href="order.php"><span>訂單管理</span></a></li>
        </ul>
    </div>
    <ul class="mobile-right">
        <li><a href="sales"><span></span></a></li>
    </ul>
</nav>

<script>

    $(document).ready(headIniti);

    function headIniti() {
        var _url = document.location.href, pos1 = 0, pos2 = 0, posTxt = "";
        pos1 = _url.indexOf("backoffice/");
        pos2 = _url.indexOf(".php");
        posTxt = _url.substring(pos1+11, pos2););
        $("nav div#navbar li." + posTxt).addClass("active");
    }

    </script>
<?php }?>