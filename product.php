<html ng-app="">
<head>

<?php
	require_once 'include/config.php';
	require_once 'include/common.php';
?>


<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/js/swiper.jquery.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/css/swiper.min.css" />
<script>

	$(function(){
		setTimeout(function(){
			
			var galleryTop = new Swiper('.gallery-top', {
				nextButton: '.swiper-button-next',
				prevButton: '.swiper-button-prev',
				spaceBetween: 10,
			});
		
			var galleryThumbs = new Swiper('.gallery-thumbs', {
				spaceBetween: 10,
				centeredSlides: true,
				slidesPerView: 'auto',
				touchRatio: 0.2,
				slideToClickedSlide: true
			});
			galleryTop.params.control = galleryThumbs;
			galleryThumbs.params.control = galleryTop;

		},500);
		
		$(".eMailBtn").click(function() {
			window.open("mailto:satana.tai1214@gmail.com","_self");
		});

		$(".needsBtn").click(function() {
			if($("body").hasClass("authoried")) {
				//checkLoginState();

				if(sessionStorage.phone == "undefined" || sessionStorage.phone == undefined) {
					
					blockFormat.message = "請增加電話號碼，方可連絡您";
					blockFormat.timeout = 1000;
					blockFormat.onUnblock = function () {
						
						$.blockUI({message: $(".blackFrame .sign-frame")[0]});
						$(".blockUI .sign-frame")[0].className = "sign-frame";
						$(".blockUI .sign-frame").addClass("update");

						var _updateForm = $(".blockUI .sign-frame form#update")[0];
						
						_updateForm.name.value = sessionStorage.userName;
						_updateForm.email.value = sessionStorage.userEmail;
						_updateForm.address.value = sessionStorage.address;
						_updateForm.update.value = sessionStorage.userId;
						$(".sign-frame .cross").click(function() {
							$(this).unbind();
							$.unblockUI();
						});

					};
					
					$(".updateConfirm").click(function(){
						
						$.post("fetchdata.php", $("form#update").serialize(), function(callback){
							$.unblockUI();
							var _data =  $.parseJSON(callback);
							if(_data["State"] == "SUCCESS") {
								blockFormat.message = "更新成功";
								
							} else {
								blockFormat.message = "更新失敗";
							}
							blockFormat.onUnblock = function() {};
							//blockFormat.timeout = 5000;
							$.blockUI(blockFormat);

						});

					});
					
					$.blockUI(blockFormat);

				} else {
					var _orderForm = $(".sign-frame form#order")[0];
					$.blockUI({message: $(".blackFrame .sign-frame")[0], overlayCSS: {"cursor": "default"}, css: {"cursor": "default"}});

					$(".blockUI .sign-frame")[0].className = "sign-frame";
					$(".blockUI .sign-frame").addClass("order");
					$("form#order .productName").text($("h2").text());
					$(".sign-frame form#order input[name=addOrder]")[0].value = $(".pathlink span")[0].id;
					$(".sign-frame form#order input[name=userId]")[0].value = sessionStorage.userId;
					$(".sign-frame .cross").click(function() {
						$(this).unbind();
						$.unblockUI();
					});

					$("form#order .orderBtn").click(function(){
						$.post("fetchdata.php", $("form#order").serialize(), function(callback){
							
							var _data =  $.parseJSON(callback);
							if(_data["State"] == "SUCCESS") {
								blockFormat.message = "謝謝訂購，我們會儘快與您連絡";
								blockFormat.timeout = 1000;
								$.blockUI(blockFormat);
							}

						});
					});
				}
			} else {
				logInFun();
			}
		});

	});
	
	//add github function

</script>



</head>
<body ng-style="bodyStyle">
	<?php require_once 'include/navlist.php'; ?>
	<div class="page product container" ng-style="pageStyle">
		<div class="inner">
			
			<?php
				$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    			$mysqli->set_charset("utf8"); //設定UTF8

				if(isset($_GET["p"])) {
					 $sqlStr = "SELECT PD.ID, PD.NAME, PD.ALT_NAME, PD.PRICE, PD.BRIEF, PD.DESCRIPTION, CD.name AS CLASSNAME, CD.id AS CLASS_ID,(SELECT GROUP_CONCAT(NAME) FROM products_image PI WHERE PI.BOND = PD.ID ORDER BY PI.ID DESC) AS IMAGE FROM products_data PD LEFT JOIN class_data CD ON PD.CLASSBOND = CD.id WHERE ALT_NAME = '" . $_GET["p"] . "'" ;
					 $result = $mysqli -> query($sqlStr);
					 $data = mysqli_fetch_assoc($result);

			?>
			
			<head><title><?= TITLE . "|" . $data['NAME']; ?></title></head>

			<div class="pathlink"><a target="_self" href="index.php">home</a> > <a href="class.php?id=<?= $data['CLASS_ID']; ?>"><?= $data["CLASSNAME"]; ?></a> > <span id="<?= $data["ID"] ?>"><?= $data["NAME"]; ?></span></div>

				<section>
					
					<div class="inner">
						<div>
							<div class="col-md-12">
								<h2><?= $data["NAME"]; ?></h2>
								<div class="row">

									<div class="swiper-container gallery-top">
										<div class="swiper-wrapper">
										
											<?php
												$pimage = explode(",", $data["IMAGE"]);
												foreach($pimage as $key => $img) {
											?>
												<div class="swiper-slide" style="background-image:url(data/img/product/<?= $img; ?>)"></div>

												
											<?php }	?>


										</div>
										<div class="swiper-button-next swiper-button-white"></div>
										<div class="swiper-button-prev swiper-button-white"></div>
									</div>

									<div class="swiper-container gallery-thumbs">
										<div class="swiper-wrapper">

											<?php
												$pimage2 = explode(",", $data["IMAGE"]);
												foreach($pimage2 as $key => $img) {
											?>
												<div class="swiper-slide" style="background-image:url(data/img/product/<?= $img; ?>)"></div>

												
											<?php }	?>



										</div>
									</div>

									<div class="buttons" style="text-align: right">
										<div class="needsBtn btn">需求單</div>
									</div>
									<div class="buttons" style="text-align: right;margin-top: 5px;">
										<div class="eMailBtn btn-primary btn">寄信給我</div>
									</div>
									<div class="buttons" style="text-align: right;margin-top: 5px;">
										<div class="phoneBtn btn">電話</div>
									</div>
									
									<div class="description">
										<h4>商品介紹</h4>
										<div class="briefInner">
											<?= $data["DESCRIPTION"]; ?>
										</div>
									</div>
									<!--<div class="price">NT: <?= $data["PRICE"]; ?></div> -->
								</div>

							</div>
						
						<div class="images"></div>

					</div>

				</section>
			
			<?php
				}	
			?>
			
		</div>

	</div>
	
	<div class="blackFrame itemFrame loadingFrame hide"><div class="loadingInner"><span>now loading...</span></div></div>
	<?php require_once 'include/footer.php'; ?>
	
</body>
</html>