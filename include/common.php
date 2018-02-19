<?php
    $time = new DateTime();
    $timestamp = date_timestamp_get($time);
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, max-scale=1.0">
<meta name="description" content="紳泰蘭柚木家具(傢俱)致力於全柚木籐傢俱，無貼皮無合板打造簡單幸福的生活空間" />
<script src="//code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<!-- Latest compiled and minified CSS -->

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.1.5/angular.min.js"></script>

<script src="/<?= FOLDER_NAME; ?>/js/fb.js?<?= $timestamp;?>"></script>

<script src="/<?= FOLDER_NAME; ?>/js/common.js?<?= $timestamp;?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>

<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<link rel="shortcut icon" type="image/x-icon" href="/<?= FOLDER_NAME; ?>/img/favicon.ico?=<?= $timestamp; ?>">
<link type="text/css" href="/<?= FOLDER_NAME; ?>/css/index.css?<?= $timestamp;?>" rel="stylesheet">


<meta property="og:image" content="../img/ogImage.jpg" />

<script>
    var appId = "<?= APP_ID; ?>";
    function NavBtn(evt){

        if(location.href.indexOf("index") < 0 || !window.matchMedia("(min-width: 768px)").matches) {
            location.href = "index.php?position=" + evt.className;
        }

        animateTo($("#" + evt.className)[0]);

    };

    function animateTo(_obj) {
        var _hgt = 140;

        if (!window.matchMedia("(min-width: 768px)").matches) {
             _hgt = 58;
        }

        $('html, body').animate({
            scrollTop: $(_obj).offset().top - _hgt
        }, 500);
    };

    function MainCtrl($scope) { //navlist

        $scope.navlists = [];
        $scope.basic = [];
    };

    function DataCtrl($scope) {
        $scope.newItems = [];
        $scope.foreBanner = [];
    }

    $(function(){
        
        $.getJSON('/satana/data/source.json?n=23126', function(data) {
            var scope = angular.element($("#navData")[0]).scope();
            scope.$apply(function(){
                scope.navlists = data.navlist;
                scope.basic = data.source;
                
            });
    
            scope = angular.element($("body")[0]).scope();
            scope.$apply(function(){
                scope.bodyStyle = {"background-color": data.source.bodyColor, "background-image": "url(/" + data.source.folderName + "/img/" + data.source.bodyGround + ")"};
            });

            scope = angular.element($("body > div.page")[0]).scope();


            scope.$apply(function(){
                if(data.source.widthSize.indexOf("%") > 0) {
                    scope.pageStyle = {"width": data.source.widthSize};
                } else {
                    scope.pageStyle = {"width": data.source.widthSize + "px"};
                }
                
            });

            scope = angular.element($("footer")[0]).scope();
            scope.$apply(function(){
                scope.footerText = data.source.footerText;
                scope.footerImage = ("/" + data.source.folderName + "/img/" + data.source.footerLogo);
            });

        });

    });

</script>