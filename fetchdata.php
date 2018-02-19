<?php
	header("Content-Type:text/html;Charset:utf-8");
	include 'include/config.php';
	
	$tempArray = null;
	if(isset($_GET["gettingData"])) {
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    	$mysqli->set_charset("utf8"); //設定UTF8


		switch ($_GET['gettingData']) {
			case "new" :
				$sqlStr = "SELECT NAME, ALT_NAME, PRICE, BRIEF ,(SELECT NAME FROM products_image PI WHERE PI.BOND = PD.ID ORDER BY PI.ID DESC LIMIT 1) AS IMAGE FROM products_data PD ORDER BY ID DESC LIMIT 4";
				$result = $mysqli -> query($sqlStr);
				$productArray = array();
				if (mysqli_num_rows($result) > 0) {
					while($row = mysqli_fetch_assoc($result)) {
						array_push($productArray, array("name" => $row["NAME"], "alt" => $row["ALT_NAME"], "price" => $row["PRICE"], "brief" => $row["BRIEF"], "image" => $row["IMAGE"]));
					}
					$tempArray = array("State"=>"SUCCESS", "Data"=> $productArray);
				} else {
					$tempArray = array("State"=>"NONE");
				}
				break;
			case "fore" :
				$sqlStr = "SELECT BC.width, BC.height, BI.image, BI.instruction FROM banner_class_data BC INNER JOIN banner_image_data BI ON BI.class = BC.id WHERE BC.id = 1";
				$result = $mysqli -> query($sqlStr);
				$bannerArray = array();
				if (mysqli_num_rows($result) > 0) {
					while($row = mysqli_fetch_assoc($result)) {
						array_push($bannerArray, array("image" => $row["image"], "instruction" => $row["instruction"], "width" => $row["width"], "height" => $row["height"]));
					}
					$tempArray = array("State"=>"SUCCESS", "Data"=> $bannerArray);
				} else {
					$tempArray = array("State"=>"NONE");
				}
				break;
			case "sale" :

				$sqlStr = "SELECT PD.NAME AS NAME, PD.ALT_NAME, (SELECT PI.NAME FROM products_image PI WHERE PI.BOND = PD.ID ORDER BY RAND() LIMIT 1) AS IMAGE FROM products_param PP LEFT JOIN products_data PD ON PP.product_id = PD.ID WHERE PP.param_id = 1 LIMIT 3";
				$result = $mysqli -> query($sqlStr);
				$saleArray = array();
				if (mysqli_num_rows($result) > 0) {
					while($row = mysqli_fetch_assoc($result)) {
						array_push($saleArray, array("name" => $row["NAME"], "image" => $row["IMAGE"], "alt" => $row["ALT_NAME"]));
					}
					$tempArray = array("State"=>"SUCCESS", "Data"=> $saleArray);
				} else {
					$tempArray = array("State"=>"NONE");
				}

				break;
			default:
			break;
		}


		echo json_encode($tempArray);

	}

	if(isset($_POST["registor"])) {
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    	$mysqli->set_charset("utf8"); //設定UTF8

		$sqlStr = "SELECT * FROM user_data WHERE phone = '" . $_POST["phone"] . "' OR email = '" . $_POST["email"] . "'";
		$result = $mysqli -> query($sqlStr);
		if (mysqli_num_rows($result) > 0) {

			$data = mysqli_fetch_assoc($result);

			$tempArray = array("State"=>"SUCCESS", "PhoneState" => $data["phone"] == $_POST["phone"], "EmailState" => $data["email"] == $_POST["email"]);
		} else {		
			$stmt = $mysqli -> prepare("INSERT INTO user_data (user_id, name, phone, email ,address) VALUES(?, ?, ?, ?, ?);");

			$stmt -> bind_param('sssss', $_POST["userId"], $_POST["name"], $_POST["phone"], $_POST["email"], $_POST["address"]);
			$stmt -> execute();
			$stmt -> close();
			$tempArray = array("State"=>"SUCCESS", "PhoneState" => false, "EmailState" => false);
		}

		$mysqli -> close();
		echo json_encode($tempArray);
	}

	if(isset($_POST["update"])) {
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    	$mysqli->set_charset("utf8"); //設定UTF8

		$sqlStr = "SELECT * FROM user_data WHERE user_id = '" . $_POST["update"] . "'";
		$result = $mysqli -> query($sqlStr);
		if (mysqli_num_rows($result) > 0) {

			$stmt = $mysqli -> prepare("UPDATE user_data SET name = ?, phone = ?, email = ?, address = ? WHERE user_id = ?;");
			$stmt -> bind_param('sssss', $_POST["name"], $_POST["phone"], $_POST["email"], $_POST["address"], $_POST["update"]);
			$stmt -> execute();
			$stmt -> close();
			
			$tempArray = array("State"=>"SUCCESS");
		} else {		
			$tempArray = array("State"=>"FAILURE");
		}

		$mysqli -> close();
		echo json_encode($tempArray);
	}

	if(isset($_POST["registorPhone"])) {
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    	$mysqli->set_charset("utf8"); //設定UTF8
		$sqlStr = "SELECT * FROM user_data WHERE phone = '" . $_POST["registorPhone"] . "' AND user_id = '" . $_POST["userId"] . "'";
		$result = $mysqli -> query($sqlStr);

		if (mysqli_num_rows($result) > 0) {
			$data = mysqli_fetch_assoc($result);

			$tempArray = array("State"=>"SUCCESS", "id" => $data["user_id"], "name" => $data["name"], "email" => $data["email"], "phone" => $data["phone"], "address" => $data["address"]);
		}else {
			$tempArray = array("State"=>"FAILURE");
		}
		echo json_encode($tempArray);
	}
	

	if(isset($_POST["fbLogin"])) {
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    	$mysqli->set_charset("utf8"); //設定UTF8


		$sqlStr = "SELECT * FROM user_data WHERE user_id = '" . $_POST["id"] . "'";
		$result = $mysqli -> query($sqlStr);

		$newJoin = false;
		$phone = "";
		$address = "";
		if (mysqli_num_rows($result) > 0) {
			$data = mysqli_fetch_assoc($result);
			$phone = $data["phone"];
			$address = $data["address"];
			
			$stmt = $mysqli -> prepare("UPDATE user_data SET name = ?, email = ?  WHERE user_id = ?;");
		} else {
			$stmt = $mysqli -> prepare("INSERT INTO user_data (name, email, user_id) VALUES(?, ?, ?);");
			$newJoin = true;
		}
		$stmt -> bind_param('sss', $_POST["name"], $_POST["email"], $_POST["id"]);
		
		
        $dbResult = $stmt -> execute();
        $stmt -> close();
        $mysqli -> close();

		$tempArray = array("State"=>"SUCCESS", "SqlStatus"=> $dbResult, "newJoin" => $newJoin, "Phone" => $phone, "Address" => $address);
		echo json_encode($tempArray);
	}

	

	if(isset($_POST["productId"])) {
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    	$mysqli->set_charset("utf8"); //設定UTF8

		$state = false;

		 $sqlStr = "SELECT id FROM user_data WHERE user_id = '" . $_POST["userId"] . "'";
		 $result = $mysqli -> query($sqlStr);

		if (mysqli_num_rows($result) > 0) {
			 $data = mysqli_fetch_assoc($result);

			
			 $stmt = $mysqli -> prepare("INSERT INTO order_data (userId, productId) VALUES(?, ?);");

			 $stmt -> bind_param('ii', $data["userId"], $_POST["productId"]);
			 $stmt -> execute();
			 $stmt -> close();
			 $state = true;
		}

		 $mysqli -> close();
		

		$tempArray = array("State"=>"SUCCESS");
		echo json_encode($tempArray);
	}

	if(isset($_POST["addOrder"])) {
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    	$mysqli->set_charset("utf8"); //設定UTF8

		$state = false;

		 $sqlStr = "SELECT id FROM user_data WHERE user_id = '" . $_POST["userId"] . "'";
		 $result = $mysqli -> query($sqlStr);

		if (mysqli_num_rows($result) > 0) {
			 $data = mysqli_fetch_assoc($result);
			 $stmt = $mysqli -> prepare("INSERT INTO order_data (userId, productId, productNum, content, createTime) VALUES(?, ?, ?, ?, NOW());");
			 $stmt -> bind_param('iiis', $data["id"], $_POST["addOrder"], $_POST["productNumber"], $_POST["content"]);
			 $stmt -> execute();
			 $stmt -> close();
			 $state = true;
		}

		 $mysqli -> close();
		

		$tempArray = array("State"=>"SUCCESS", "SqlStatus"=> $state);
		echo json_encode($tempArray);
	}

?>
