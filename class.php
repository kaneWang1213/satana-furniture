<?php
	require_once 'include/config.php'; 
	require_once 'include/common.php';

	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	$mysqli->set_charset("utf8"); //設定UTF8

	//$sqlStr = "SELECT * FROM products_data";
	$sqlStr = "SELECT PD.NAME, PD.ALT_NAME, PD.BRIEF, CD.img AS classImg, (SELECT NAME FROM products_image PI WHERE BOND = PD.ID LIMIT 1) AS IMAGE, CD.name AS CLASSNAME FROM products_data PD LEFT JOIN class_data CD ON PD.CLASSBOND = CD.id WHERE CLASSBOND = " . $_GET["id"];
	$classImage = null;
	$result = $mysqli -> query($sqlStr);
    $data = null;
	$titleName = null;

	 if (mysqli_num_rows($result) > 0) {
		$data = array();
		while($row = mysqli_fetch_assoc($result)) {
			
			if($titleName==null) {
				$titleName = $row['CLASSNAME'];
			}

			if($classImage==null) {
				$classImage = $row['classImg'];
			}

			array_push($data, array("name"=> $row["NAME"], "alt"=> $row["ALT_NAME"], "brief"=> $row["BRIEF"], "image"=> $row["IMAGE"], ));

		}
	} else {
        $sqlStr = "SELECT img FROM class_data WHERE id = " . $_GET["id"];
        $result = $mysqli -> query($sqlStr);
        if (mysqli_num_rows($result) > 0) {
            $imgData = mysqli_fetch_assoc($result);
            $classImage = $imgData["img"];
        }

		echo "<script>window.location.href='/site';</script>";

    }

   
?>
<html ng-app>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/css/swiper.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/js/swiper.jquery.min.js"></script>
<script>
	$(function(){
		
	});

    function mainCtrl($scope) {
        $scope.items = <?=  json_encode($data); ?>
    }

</script>

<title><?= TITLE . "|" . $titleName; ?></title>

</head>
<body ng-style="bodyStyle">
	<?php require_once 'include/navlist.php'; ?>
	<div class="page" ng-style="pageStyle">
		<div class="inner container">
			<div class="pathlink"><a target="_self" href="index.php">home</a> > <?= $titleName; ?></div>

			<?php if($classImage!=null) { ?>
				<div class="banner">
					<img alt="紳泰蘭傢俱-<?= $titleName; ?>" src="data/img/<?= $classImage;?>" />
				</div>
			<?php }; ?>

			<section class="class">		
				<div class="inner">
					<div class="row">
                        <div class="col-md-12">
							<h2><?= $titleName; ?></h2>
							<div class="row" ng-controller="mainCtrl">
                                <div class="item col-md-3 col-xs-6" ng-repeat="item in items" >
                                    <a href="product.php?p={{item.alt}}">
					                    <div class="img" style="background-image: url(data/img/product/{{item.image}})">
					                        <img alt="{{item.name}}" src="img/transparentCube.png" class="width2Side" />
					                    </div>
					                    <div class="content">
					                        <h4>{{item.name}}</h4>
					                    </div>
                                    </a>
				                </div>
								
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
	<div class="blackFrame hidden">
		<div class="inner"></div>
	</div>
	
	<?php require_once 'include/footer.php';  $mysqli -> close();$result -> close(); ?>
</body>
</html>