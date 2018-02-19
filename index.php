<?php 
	require_once 'include/config.php'; 
	require_once 'include/common.php';
?>

<html ng-app>
<head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/js/swiper.jquery.js"></script>
<script src="/site/js/index.js?<?= $timestamp;?>"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/css/swiper.min.css" />
<script>

	$(function() {
		//取得最新產品
		fetchData("fore");
		fetchData("new");
		fetchData("sale");
		
		setTimeout(function(){

			var mySwiper = new Swiper ('.swiper-container', {
				pagination: '.swiper-pagination',
				//paginationClickable: true,
				spaceBetween: 30,
				effect: 'fade',
				autoplay: 2500,
				loop: true
			});

		}, 200);

		$(document).on("click", ".bannerFrame", checkLoginState);
		//commit with github
	});

	

</script>
<style>
	.swiper-container {
        width: 100%;
        height: 100%;
    }
    .swiper-slide {
        background-position: center;
        background-size: cover;
    }

</style>

<title><?= TITLE; ?></title>

</head>
<body ng-style="bodyStyle">
	<?php require_once 'include/navlist.php'; ?>
	<div class="page" ng-style="pageStyle">
		
		<section class="fore">
			<div class="bannerFrame swiper-container" >
				<div class="inner swiper-wrapper" style="{{'width:' + foreBanner.length * 100 + '%'}}">
					<div class="item swiper-slide" style="background-image: url(/satana/img/bannerImg/{{banner.image}})" ng-repeat="banner in foreBanner">
						<div class="text">{{banner.instruction}}</text>
					</div>
				</div>
				<!--<div class="swiper-pagination swiper-pagination-white"></div>-->
			</div>
		</section>

		<section>
			<div id="status"></div> 
		</section>

		<section class="container interduce">
			<div class="row">
				<div class="col-md-12">
					<h1>紳泰蘭藤柚木傢俱</h1>
					<div class="text">
						（紳泰蘭藤柚木家具）本公司一直承襲著第一代所留下的的經營理念，秉持著第一代泰山籐業有限公司經營理念，幫客人挑選最優質的籐材料與緬甸柚木完成客人心中所想要的家具。藉由著台灣老師傅的手工，有手工編織的籐椅與老師傅緬甸柚木卡榫家具的作工。
每件家具都是在精選材料的原則下純手工製作完成，讓每件作品在家中呈現的感覺是溫暖與和諧。這就是紳泰蘭籐柚木家具所呈現的特質。
						
						
					</div>
				</div>
			</div>
		</section>

		<section class="new container">
			<h3>新品<span>new product</span></h3>
			<div class="lists" ng-controller="DataCtrl">
				<div class="item col-md-3 col-xs-6" ng-repeat="item in newItems">
					<a href="/site/product.php?p={{item.alt}}">
						<div class="img" style="background-image: url(/satana/data/img/product/{{item.image}})">
							<img alt="紳泰蘭藤柚木傢俱家具-新品" src="/satana/img/transparentCube.png" class="width2Side" />
						</div>
						<div class="content">
							<h4>{{item.name}}</h4>
							<!--<div class="price">{{item.price}}</div> -->
							<div class="brief">{{item.brief}}</div>
						</div>
					</a>
				</div>

				<div class="clear"></div>
			</div>
		</section>

		<section class="sale container">
			<h3>推薦商品<span>recommerdation</span></h3>
			<div class="saleFrame lists" ng-controller="DataCtrl">
				<div class="{{$index < 2 ? 'item col-md-4 col-xs-6':'item col-md-4 clearCol'}}" ng-repeat="item in saleItems track by $index">
		            <a href="/site/product.php?p={{item.alt}}">
						<div class="img" style="background-image: url(/satana/data/img/product/{{item.image}})">
							<img alt="紳泰蘭藤柚木傢俱家具-推薦" src="/satana/img/transparentCube.png" class="width2Side" />
						</div>
						<div class="content">
							<h4>{{item.name}}</h4>
						</div>
		            </a>
				</div>
				<div class="clear"></div>
			</div>
		</section>

	</div>
	<div class="blackFrame hidden">
		<div class="inner"></div>
	</div>
	<div class="blackFrame itemFrame loadingFrame hide"><div class="loadingInner"><span>now loading...</span></div></div>
	<?php require_once 'include/footer.php'; ?>
	
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/zh_TW/sdk.js#xfbml=1&version=v2.10";
	fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>

</body>
</html>