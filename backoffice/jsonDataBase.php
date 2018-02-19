<?php
header("Content-Type:text/html;Charset:utf-8");
include 'include/config.php';

$tempArray = null;
$jsonType = "";
//單獨圖片刪除
if (isset($_GET['deleteLotteryImg'])) {
    $tmpArr = removeImage($_GET['imagepath'], true);
    echo json_encode($tmpArr);
}
//單獨圖片刪除
function removeImage($imgpath, $withData) {
    $dbResult = false;
    $imgResult = false;
    if($withData) {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        $mysqli->set_charset("utf8"); //設定UTF8
        $stmt = $mysqli -> prepare("UPDATE lottery_data SET IMAGE = null WHERE IMAGE = ?;");
        $stmt -> bind_param('s', $imgpath);
        $dbResult = $stmt -> execute();
        $stmt -> close();
        $mysqli -> close();
    }
    
    //刪除圖片
    if (file_exists($imgpath)) {
        $imgResult = unlink($imgpath);
    }
    $tempArray = array("State"=>"SUCCESS", "dbState" => $dbResult, "imgState" => $imgResult);
    return $tempArray;
}

//修改頁首連結
if (isset($_POST['updateType']) && $_POST["updateType"] == "menu") {
    /*$number = 0;
    $newText = "//nav1\n    $" . "navlist = [";
    while($number < count($_POST['navName'])) {
        if($number > 0) {
            $newText .= ",";
        }
        $newText .= "\n       {'name':'" . $_POST['navName'][$number] . "','url':'" . $_POST['navUrl'][$number] . "','active':" . $_POST['active'][$number] . "}";
        $number ++;
    }
    $newText .= "\n    ];\n//nav2";
    modifySource("source", $newText);
    $tempArray = array("State"=>"SUCCESS");
    echo json_encode($tempArray);
    */

    
    
    $number = 0;
    $newText = '"navlist": [';
    while($number < count($_POST['navName'])) {
        if($number > 0) {
            $newText .= ",";
        }
        $newText .= '{"name": "' . $_POST['navName'][$number] . '","url": "' . $_POST['navUrl'][$number] . '","active": "' . $_POST['active'][$number] . '","bondId": "' . $_POST['classBond'][$number]  . '","bondName": "' . $_POST['className'][$number] . '"}';
        $number ++;
    }
    $newText .= ']';
    modifySource("source", $newText);
    $tempArray = array("State"=>"SUCCESS");
    echo json_encode($tempArray);
    
}

//修改基本資料
if (isset($_POST['updateType']) && $_POST["updateType"] == "basic") {
    
    $bgState = "";
    $logoState = "";
    $footerState = "";
    $iconState = "";
    
    $removeArr = explode(",", $_post["removeImg"]);

    foreach($removeArr as $imgpath) {
        //刪除圖片
        if (file_exists($imgpath)) {
            $imgResult = unlink($imgpath);
        }
    }

    if($_POST["wbg"] == "") {
        $bgState = uploadImage("bgGround", "../img/");
    } else {
        $bgState = $_POST["wbg"];
    }

    if($_POST["limg"] == "") {
        $logoState = uploadImage("headerLogo", "../img/");
    } else {
        $logoState = $_POST["limg"];
    }

    if($_POST["fimg"] == "") {
        $footerState = uploadImage("footerLogo", "../img/");
    } else {
        $footerState = $_POST["fimg"];
    }

    if($_POST["icon"] == "") {
        //$iconState = uploadImage("titleIcon", "../img/");

        $file_name = $_FILES["icon"]['name'];
        $file_tmp = $_FILES["icon"]['tmp_name'];
        $file_type = explode(".", $file_name);
        //$file_type = explode("\.", $str_replace("jpeg","jpg",$_FILES["icon"]['type']));
        //$file_type = explode(".", $_FILES["icon"]['type']);

        $imgPath = "../img/favicon." . $file_type[1]; //產生圖片（隨機名）
        move_uploaded_file($file_tmp, $imgPath); //上傳圖片

        $iconState = "favicon.". $file_type[1];

    } else {
        $iconState = $_POST["icon"];
    }
    


    $txt .= '"source": {';
    $txt .= '"title": "' . $_POST["title"] . '",';
    $txt .= '"widthSize": "' . $_POST["widthSize"] . '",';
    $txt .= '"bodyGround": "' . (($bgState!="")? $bgState:"") . '",';
    $txt .= '"bodyColor": "' . $_POST["bgCode"] . '",';
    $txt .= '"titleIcon": "' . (($iconState!="")? $iconState:"") . '",';
    $txt .= '"headerlogo": "' . (($logoState!="")? $logoState:"") . '",';
    $txt .= '"headerText": "' . $_POST["headerText"] . '",';
    $txt .= '"footerLogo": "' . (($footerState)? $footerState:"") . '",';
    $txt .= '"footerText": "' . $_POST["footerText"] . '",';
    $txt .= '"folderName": "' . $_POST["folderName"] . '"';
    $txt .= '}';
    
    modifySource("navlist", $txt);
    $tempArray = array("State"=>"SUCCESS", "Image" => array($bgState, $logoState, $footerState, $iconState));
    echo json_encode($tempArray);
}

//撈產品
if(isset($_GET["selectProducts"])) {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    $mysqli->set_charset("utf8"); //設定UTF8

    if($_GET["selectProducts"] == 0) {
        $sqlStr = "SELECT P.ID, P.NAME, P.ALT_NAME, P.PRICE, P.BRIEF, P.DESCRIPTION, P.CLASSBOND, (SELECT GROUP_CONCAT(NAME) FROM products_image I WHERE I.BOND = P.ID) AS IMAGE, (SELECT GROUP_CONCAT(M.param_id, '|', PD.name) FROM products_param M LEFT JOIN parameter_data PD ON M.param_id = PD.id WHERE M.product_id = P.ID) AS PARAM FROM products_data P";
    } else {
        $sqlStr = "SELECT P.ID, P.NAME, P.ALT_NAME, P.PRICE, P.BRIEF, P.DESCRIPTION, P.CLASSBOND, (SELECT GROUP_CONCAT(NAME) FROM products_image I WHERE I.BOND = P.ID) AS IMAGE, (SELECT GROUP_CONCAT(M.param_id, '|', PD.name) FROM products_param M LEFT JOIN parameter_data PD ON M.param_id = PD.id WHERE M.product_id = P.ID) AS PARAM FROM products_data P WHERE P.CLASSBOND = " . $_GET["selectProducts"];
    }
    
    $result = $mysqli -> query($sqlStr);
    $productArray = array(array("id"=>0, "name"=>"select blow product"));
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            array_push($productArray, array("id" => $row[ID], "name" => $row["NAME"], "alt" => $row["ALT_NAME"], "price" => $row["PRICE"], "brief" => $row["BRIEF"], "image" => $row["IMAGE"], "description" => $row["DESCRIPTION"], "bond" => $row["CLASSBOND"], "param" => $row["PARAM"]));
        }
        $tempArray = array("State"=>"SUCCESS", "Data"=> $productArray, "Sql" => $sqlStr);
    } else {
        $tempArray = array("State"=>"NONE");
    }
    echo json_encode($tempArray);

    $mysqli->close();
    $result->close();
}

//撈產品列表
if(isset($_GET["selectProductList"])) {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    $mysqli->set_charset("utf8"); //設定UTF8
    $number = $_GET["selectProductList"];
    $extraStr = "";
    
    if($_GET["selectClass"] == 0 || $_GET["selectClass"] == "0" || $_GET["selectClass"] == "") {
        $sqlStr = "SELECT P.ID, P.NAME, P.ALT_NAME, P.PRICE, P.CLASSBOND, (SELECT COUNT(*) FROM products_data) AS TOTAL FROM products_data P LEFT JOIN class_data C ON P.CLASSBOND = C.id ORDER BY P.ID DESC";
    } else {
        $extraStr = " CLASSBOND = ". $_GET["selectClass"]; 
        $sqlStr = "SELECT P.ID, P.NAME, P.ALT_NAME, P.PRICE, P.CLASSBOND, (SELECT COUNT(*) FROM products_data WHERE" . $extraStr . ") AS TOTAL FROM products_data P LEFT JOIN class_data C ON P.CLASSBOND = C.id WHERE" . $extraStr . " ORDER BY P.ID DESC";
    }
    
    $sqlStr .= (" LIMIT " . (($number) * 10)  . ", 10");

    
    $result = $mysqli -> query($sqlStr);
    $productArray = array();
    if (mysqli_num_rows($result) > 0) {
        $total = 0;
        while($row = mysqli_fetch_assoc($result)) {
            $total = $row["TOTAL"];
            array_push($productArray, array("prdid" => $row["ID"], "name" => $row["NAME"], "no" => $row["ALT_NAME"], "price" => $row["PRICE"], "class" => $row["CLASSBOND"]));
        }
        
    }
    $tempArray = array("State"=>"SUCCESS", "Data"=> $productArray, "Total" => $total, "Target" => $_GET["selectProductList"], "Class" => $_GET["selectClass"]);
    echo json_encode($tempArray);
    $mysqli->close();
    $result->close();
}

//撈產品列表
$param = null;

if(isset($_GET["Param"])) {
    $param = $_GET["Param"];
} else if(isset($_POST["Param"])) {
    $param = $_POST["Param"];
}

if($param  != null) {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    $mysqli->set_charset("utf8"); //設定UTF8

    if($param == "getting") {
        $sqlStr = "SELECT id, name FROM parameter_data";
        $result = $mysqli -> query($sqlStr);
        $paramArr = array();
        while($row = mysqli_fetch_assoc($result)) {
            array_push($paramArr, array("id" => $row["id"], "name" => $row["name"], "meth" => "database"));
        }
        
        $tempArray = array("State"=>"SUCCESS", "Data"=> $paramArr);
        echo json_encode($tempArray);

    } else if($param == "update") {
        $stmt = $mysqli -> prepare("UPDATE parameter_data SET name = ? WHERE id = ?;");
        $stmt -> bind_param('si', $_POST["name"], $_POST["id"]);
        $dbResult = $stmt -> execute();
        $stmt -> close();
        $mysqli -> close();
        $tempArray = array("State"=>"SUCCESS");
        echo json_encode($tempArray);
    } else if($param == "insert") {
        $stmt = $mysqli -> prepare("INSERT INTO parameter_data (name) VALUES (?);");
        $stmt -> bind_param('s', $_POST["name"]);
        $dbResult = $stmt -> execute();
        $stmt -> close();
        $mysqli -> close();
        $tempArray = array("State"=>"SUCCESS");
        echo json_encode($tempArray);
    } else if($param == "remove") {


        $mysqli -> close();
        $tempArray = array("State"=>"SUCCESS");
        echo json_encode($tempArray);
    }


    
}


//banner類別處理
if(isset($_POST["jsonType"])) {
    $jsonType = $_POST["jsonType"];
} else if(isset($_GET["jsonType"])) {
    $jsonType = $_GET["jsonType"];
}

if($jsonType !== "") {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    $mysqli->set_charset("utf8"); //設定UTF8

    switch ($jsonType) {
        case "gettingClass":
            $sqlStr = "SELECT id, name, alt, img FROM class_data;";
            $result = $mysqli -> query($sqlStr);
            if (mysqli_num_rows($result) > 0) {
                $datas = array();
                while($row = mysqli_fetch_assoc($result)) {
                    array_push($datas, array("id"=>$row["id"], "name"=>$row["name"], "alt"=>$row["alt"], "img"=>$row["img"]));
                }
                $tempArray = array("State"=> "SUCCESS", "Data" => $datas);
                echo json_encode($tempArray);
            }
        break;
        case "insertClass": //上傳banner類別
            
            $stmt = $mysqli -> prepare("INSERT INTO banner_class_data (id, name, width, height) VALUES(?, ?, ?, ?);");
            $stmt -> bind_param('isii', $_POST["id"], $_POST["name"], $_POST["width"], $_POST["height"]);
            $dbResult = $stmt -> execute();
            $stmt -> close();
            $mysqli -> close();

            if($dbResult == 1) {
                $tempArray = array("State"=>"SUCCESS", "Result"=> "Add Successfully");
            } else {
                $tempArray = array("State"=>"SUCCESS", "Result"=> "Add failurally");
            }
            
            echo json_encode($tempArray);
        break;
        case "updateClass" :
            $stmt = $mysqli -> prepare("UPDATE banner_class_data SET name = ?, width = ?, height = ? WHERE id = ?;");
            $stmt -> bind_param('siii', $_POST["name"], $_POST["width"], $_POST["height"], $_POST["id"]);
            $dbResult = $stmt -> execute();
            $stmt -> close();
            $mysqli -> close();

            if($dbResult) {
                $tempArray = array("State"=>"SUCCESS", "Result"=> "update Successfully");
            } else {
                $tempArray = array("State"=>"SUCCESS", "Result"=> "update failurally");
            }
            echo json_encode($tempArray);
        break;
        case "gettingBannerClass" :
            $sqlStr = "SELECT id, name, height, width FROM banner_class_data;";
            $result = $mysqli -> query($sqlStr);
            if (mysqli_num_rows($result) > 0) {
                $datas = array();
                while($row = mysqli_fetch_assoc($result)) {
                    array_push($datas, array("id"=> $row["id"],"name"=>  $row["name"],"height"=>  $row["height"],"width"=>  $row["width"]));
                }
                $tempArray = array("State"=> "SUCCESS", "Data" => $datas, "Classes" => $classes);
            } else {
                $tempArray = array("State"=> "FAILURE");
            }
            echo json_encode($tempArray);
        break;
        case "removeClass" :
            $stmt = $mysqli -> prepare("DELETE FROM banner_class_data WHERE id = ?");
            $stmt -> bind_param('i', $_POST["id"]);
            $dbResult = $stmt -> execute();
            $stmt -> close();
            $mysqli -> close();

            $tempArray = array("State"=> "SUCCESS", "Result" => $dbResult);
            echo json_encode($tempArray);
        case "removeBanner" :
            //bannerImg
            if(isset($_POST["img"]) && $_POST["img"] !== "") {
                removeImagePath("../img/bannerImg/" . $_POST["img"]);
            }
            
            $stmt = $mysqli -> prepare("DELETE FROM banner_image_data WHERE id = ?;");
            $stmt -> bind_param('i', $_POST["id"]);
            $dbResult = $stmt -> execute();
            $stmt -> close();
            $mysqli -> close();

            $tempArray = array("State"=> "SUCCESS", "Result" => $dbResult);
            echo json_encode($tempArray);
        break;
        case "addbanner" :
            $imgName = uploadImage("bannerImg", "../img/bannerImg/");
            $stmt = $mysqli -> prepare("INSERT INTO banner_image_data (class, image, instruction) VALUES(?, ?, ?);");
            $stmt -> bind_param("iss", $_POST["className"], $imgName, $_POST["instruction"]);
            $dbResult = $stmt -> execute();
            $stmt -> close();
            $mysqli -> close();

            $tempArray = array("State"=> "SUCCESS");
            echo json_encode($tempArray);

        break;
        case "updatebanner" :
            $imgName = uploadImage("bannerImg", "../img/bannerImg/");
            //$imgArr = $imgName.explode(",", $imgName);
            $pos = 1;
            if($imgName != "") {
                $stmt = $mysqli -> prepare("UPDATE banner_image_data SET class = ?, instruction = ?, img = ? WHERE id = ?;");
                $stmt -> bind_param("issi", $_POST["className"], $_POST["instruction"], $imgName, $_POST["id"]);
            } else {
                $stmt = $mysqli -> prepare("UPDATE banner_image_data SET class = ?, instruction = ? WHERE id = ?;");
                $stmt -> bind_param("isi", $_POST["className"], $_POST["instruction"], $_POST["id"]);
                $pos = 2;
            }
            

            
            $dbResult = $stmt -> execute();
            $stmt -> close();
            $mysqli -> close();

            $tempArray = array("State"=> "SUCCESS", "Position" => $pos);
            echo json_encode($tempArray);
            break;
        case "gettingBannerList" :
            $sqlStr = "SELECT id, class, image, instruction FROM banner_image_data ORDER BY id DESC  LIMIT 5";
            $result = $mysqli -> query($sqlStr);
            if (mysqli_num_rows($result) > 0) {
                $datas = array();
                while($row = mysqli_fetch_assoc($result)) {
                    array_push($datas, array("id"=> $row["id"],"class"=>  $row["class"],"image"=>  $row["image"], "instruction" => $row["instruction"]));
                }
                $tempArray = array("State"=> "SUCCESS", "Data" => $datas);
                echo json_encode($tempArray);
            }

        break;
        case "gettingSpecificBannerList" :
            $sqlStr = "SELECT id, class, image, instruction FROM banner_image_data WHERE class = " . $_GET["class"] . " ORDER BY id DESC";
            $result = $mysqli -> query($sqlStr);
            if (mysqli_num_rows($result) > 0) {
                $datas = array();
                while($row = mysqli_fetch_assoc($result)) {
                    array_push($datas, array("id"=> $row["id"], "class"=>  $row["class"], "image"=>  $row["image"], "instruction" => $row["instruction"]));
                }
                
                $tempArray = array("State"=> "SUCCESS", "Data" => $datas);
            } else { 
                $tempArray = array("State"=> "SUCCESS", "Data" => array());
            }
            echo json_encode($tempArray);
        break;
        case "removeProductImage" :

            $stmt = $mysqli -> prepare("DELETE FROM products_image WHERE NAME = ?;");
            $stmt -> bind_param("s", $_POST["IMG"]);
            $dbResult = $stmt -> execute();
            $stmt -> close();
            $mysqli -> close();

            if($dbResult) {
                $tempArray = array("State"=> "SUCCESS");
            } else {
                $tempArray = array("State"=> "FAILURE");
            }

            $callback = removeImage("../data/img/product/" . $_POST["IMG"], false);

            

            echo json_encode($tempArray);
        break;
        case "getOrderData" :

            $sqlStr = "SELECT OD.id AS orderId, UD.name, UD.phone, UD.address, PD.NAME AS productName, OD.productNum, OD.content, OD.createTime, OD.state FROM order_data OD LEFT JOIN user_data UD ON OD.userId = UD.id LEFT JOIN products_data PD ON OD.productId = PD.ID;";
            $result = $mysqli -> query($sqlStr);
            if (mysqli_num_rows($result) > 0) {
                $datas = array();
                while($row = mysqli_fetch_assoc($result)) {
                    array_push($datas, array("orderId"=>$row["orderId"], "name"=>$row["name"], "phone"=>$row["phone"], "address"=>$row["address"], "productName"=>$row["productName"], "productNum"=>$row["productNum"], "content"=>$row["content"], "createTime"=>$row["createTime"], "state"=>$row["state"]));
                }
                $tempArray = array("State"=> "SUCCESS", "Data" => $datas);
                
            } else {
                $tempArray = array("State"=> "FAILURE");
            }

            echo json_encode($tempArray);

        break;
        case "toggleOrderState" :
            $stmt = $mysqli -> prepare("UPDATE order_data SET state = ? WHERE id = ?;");
            $stmt -> bind_param('ii', $_GET["type"], $_GET["orderId"]);
            $UpdateResult = $stmt -> execute();
            $stmt -> close();
            $mysqli -> close();

            if($UpdateResult) {
                $tempArray = array("State"=>"SUCCESS");
            } else {
                $tempArray = array("State"=>"FAILURE");
            }
            echo json_encode($tempArray);
        break;
        default :
        break;
    }
}

//上傳圖片
function uploadImageWithFileObject($img, $path) {
	$file_name = $img['name'];
    $file_size = $img['size'];
    $file_tmp = $img['tmp_name'];
    $file_type = substr(str_replace("jpeg","jpg",$img['type']),-3);
	
    $imgState = null;

    $uniqueName = uniqid();
    $imgPath = $path . $uniqueName . "." . $file_type; //產生圖片（隨機名）

    if($file_size > 1041152){ //檔案圖片過大1041152
         $uploadState = resizeDim($img['tmp_name'] , $imgPath);
    } else {
        $uploadState = move_uploaded_file($file_tmp, $imgPath); //上傳圖片
    }
    return ($uploadState)? $uniqueName . "." . $file_type:"";
}

//上傳圖片
function uploadImage($img, $path) {
    $file_name = $_FILES[$img]['name'];
    $file_size = $_FILES[$img]['size'];
    $file_tmp = $_FILES[$img]['tmp_name'];
    $file_type = substr(str_replace("jpeg","jpg",$_FILES[$img]['type']),-3);
	
    $imgState = null;

    $uniqueName = uniqid();
    $imgPath = $path . $uniqueName . "." . $file_type; //產生圖片（隨機名）

    if($file_size > 1041152){ //檔案圖片過大1041152
        // $imgState = array("State" => "FAILURE");
        // return $imgState;
        $uploadState = resizeDim($_FILES[$img]['tmp_name'] , $imgPath);

    } else {

        $uploadState = move_uploaded_file($file_tmp, $imgPath); //上傳圖片
    }
	return ($uploadState)? $uniqueName . "." . $file_type:"";
}

function resizeDim($img, $path) {
	
	$maxDim = 960;
    list($width, $height, $type, $attr) = getimagesize( $img );
    if ( $width > $maxDim || $height > $maxDim ) {
        //$target_filename = $_FILES['myFile']['tmp_name'];
        //$fn = $_FILES['myFile']['tmp_name'];
        
        $size = getimagesize( $img );
        $ratio = $size[0]/$size[1]; // width/height
        if( $ratio > 1) {
            $width = $maxDim;
            $height = $maxDim/$ratio;
        } else {
            $width = $maxDim*$ratio;
            $height = $maxDim;
        }
        $src = imagecreatefromstring( file_get_contents( $img ) );
        $dst = imagecreatetruecolor( $width, $height );
        
        $source = imagecreatefromjpeg($img);
        
        imagecopyresampled( $dst, $source, 0, 0, 0, 0, $width, $height, $size[0], $size[1] );
        imagedestroy( $src );
        //imagepng( $dst, $img ); // adjust format as needed
        imagejpeg($dst, $path, 100);
        //imagedestroy( $dst );
        return true;
    }
	
	

}



//上傳多張圖片
function uploadImages($imgs, $path) {
	
	$uploadStr = array();

    $file_ary = reArrayFiles($_FILES[$imgs]);
	
	foreach($file_ary as $key =>$img) {
		array_push($uploadStr, uploadImageWithFileObject($img, $path));
	}
	
	return $uploadStr;
	
    
}

function reArrayFiles($file_post) {
	
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }
    return $file_ary;
}

function modifySource($key, $newSource) {

    $objectJson = json_decode("../data/source.json", true);
    $source = $objectJson[$key];



    $string = file_get_contents("../data/source.json");
    $objectJson = json_decode($string, true);
    $keepData = $objectJson[$key];

    $jsonStr = "{";
    $jsonStr .= ('"' . $key . '":' . json_encode($keepData) . ',');
    $jsonStr .= $newSource;
    $jsonStr .= "}";


    $handle = fopen("../data/source.json", "w");
    fwrite($handle, $jsonStr);
    fclose($handle);

}

//類別處理
if(isset($_POST["classEvent"])) {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    $mysqli->set_charset("utf8"); //設定UTF8
    switch ($_POST["classEvent"]) {
        case "insert" :
            if($_FILES['img']['name'] != "") {
                $newImg = uploadImage("img", "../data/img/");
                $stmt = $mysqli -> prepare("INSERT INTO class_data (name, alt, img) VALUES(?, ?, ?);");
                $stmt -> bind_param('sss', $_POST["className"], $_POST["alt"], $newImg);
                $dbResult = $stmt -> execute();
                $stmt -> close();
                $tempArray = array("State"=>"SUCCESS", "Img"=> $newImg);
            } else {
                $stmt = $mysqli -> prepare("INSERT INTO class_data (name, alt) VALUES(?, ?)");
                $stmt -> bind_param("ss", $_POST["className"], $_POST["alt"]);
                $dbResult = $stmt -> execute();
                $stmt -> close();
                $tempArray = array("State"=>"SUCCESS", "Img"=>"NONE");
            }
        break;
        case "removeImg" :
            removeImagePath($_POST["img"]);
            $imgArr = explode("/", $_POST["img"]);
            $empty = "";
            $stmt = $mysqli -> prepare("UPDATE class_data SET img = ? WHERE img = ?");
            $stmt -> bind_param('ss', $empty, $imgArr[3]);
            $dbResult = $stmt -> execute();
            $stmt -> close();
            
            $tempArray = array("State"=>"SUCCESS");


        break;
        case "update" :
            if($_FILES['img']['name'] != "") {
                $newImg = uploadImage("img", "../data/img/");
                $stmt = $mysqli -> prepare("UPDATE class_data SET name = ?, alt = ?, img = ? WHERE id = ?");
                $stmt -> bind_param('sssi', $_POST["className"], $_POST["alt"], $newImg, $_POST["classId"]);
            } else {
                $stmt = $mysqli -> prepare("UPDATE class_data SET name = ?, alt = ? WHERE id = ?");
                $stmt -> bind_param('ssi', $_POST["className"], $_POST["alt"], $_POST["classId"]);
            }
            
            
            $dbResult = $stmt -> execute();
            $stmt -> close();
            $tempArray = array("State"=>"SUCCESS");
        break;
        case "remove" :
            if($_POST["wbg"] !== "") {
                removeImage($_POST["wbg"], false);
            }

            $stmt = $mysqli -> prepare("DELETE FROM class_data WHERE id = ?;");
            $stmt -> bind_param('i', $_POST["classId"]);
            $dbResult = $stmt -> execute();
            $stmt -> close();
            $tempArray = array("State"=>"SUCCESS", "RemoveState"=>$dbResult);
        break;
        case "getting" :
            $sqlStr = "SELECT id, name FROM class_data;";
            $result = $mysqli -> query($sqlStr);
            
            if (mysqli_num_rows($result) > 0) {
                $datas = array();
                while($row = mysqli_fetch_assoc($result)) {
                    array_push($datas, array("id"=> $row["id"],"name"=>  $row["name"]));
                }
            }
            $tempArray = array("State"=> "SUCCESS", "Data" => $datas);
        break;
        default:
        break;
    }
    echo json_encode($tempArray);
}

//上傳產品資料
if(isset($_POST["productEvent"])) {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    $mysqli->set_charset("utf8"); //設定UTF8
    switch ($_POST["productEvent"]) {
        case "insert" :
            $stmt = $mysqli -> prepare("INSERT INTO products_data (NAME, ALT_NAME, PRICE, BRIEF, DESCRIPTION, CLASSBOND) VALUES(?, ?, ?, ?, ?, ?);");
            $stmt -> bind_param('sssssi', $_POST["productName"], $_POST["altName"], $_POST["price"], $_POST["brief"], $_POST["content"], $_POST["className"]);
            $stmt -> execute();
            $stmt -> close();

            if($_FILES['productImg']['name'] != "") {
                $newImgs = uploadImages("productImg", "../data/img/product/");
                $result = $mysqli -> query("SELECT MAX(ID) AS ID FROM products_data;");
                $data = mysqli_fetch_assoc($result);
                $newId = $data["ID"];

                foreach($newImgs as $img) {
                    $stmt = $mysqli -> prepare("INSERT INTO products_image (NAME, BOND) VALUES(?, ?);");
                    $stmt -> bind_param('si', $img, $newId);
                    $stmt -> execute();
                    $stmt -> close();
                }
            }
            $tempArray = array("State"=>"SUCCESS", "InsertId"=> $newId);
            echo json_encode($tempArray);

        break;
        case "update" :
            // && $_FILES['productImg']['name'] != "undefined"
			
			
			 
			
            if($_FILES['productImg']['name'] != "" && $_FILES['productImg']['name'] != "undefined"&& $_FILES['productImg']['name'] != null && $_FILES['productImg']['name'] != "null") {
                //echo "http://www.satana-furniture.com.tw/site/data/img/product/";
                $newImgs = uploadImages("productImg", "../data/img/product/");
                foreach($newImgs as $key => $img) {
                   /*echo $img;*/
                    $imgSecName = explode('.', $img);
                    if($imgSecName[1] == "") {
                        
                    } else {
                        $stmt = $mysqli -> prepare("INSERT INTO products_image (NAME, BOND) VALUES(?, ?);");
                        $stmt -> bind_param('si', $img, $_POST["id"]);
                        $stmt -> execute();
                        $stmt -> close();
                    }
                }
            }

            
            $stmt = $mysqli -> prepare("UPDATE products_data SET NAME = ?, ALT_NAME = ?, PRICE = ?, BRIEF = ?, DESCRIPTION = ?, CLASSBOND = ? WHERE ID = ?;");
            $stmt -> bind_param('sssssii', $_POST["productName"], $_POST["altName"], $_POST["price"], $_POST["brief"], $_POST["content"], $_POST["className"], $_POST["id"]);
            $dbResult = $stmt -> execute();
            $stmt -> close();
            

            if(count($_POST["parameter"]) > 0) {
                foreach($_POST["parameter"] as $param) {
                    $stmt = $mysqli -> prepare("INSERT INTO products_param (param_id, product_id) VALUES(?, ?);");
                    $stmt -> bind_param('ii', $param, $_POST["id"]);
                    $dbResult = $stmt -> execute();
                    $stmt -> close();
                    $mysqli -> close();
                }
            }

            $tempArray = array("State"=>"SUCCESS");
            echo json_encode($tempArray);
        break;

        case "remove" :

            $sqlStr = "SELECT NAME FROM products_image WHERE BOND = " . $_POST["productId"] . ";";
            $result = $mysqli -> query($sqlStr);
            if (mysqli_num_rows($result) > 0) {
                $datas = array();
                while($row = mysqli_fetch_assoc($result)) {
                    $imgpath = "../data/img/product/" . $row["NAME"];
                    if (file_exists($imgpath)) {
                        $imgResult = unlink($imgpath);
                    }
                }
            }

            $stmt = $mysqli -> prepare("DELETE FROM products_image WHERE BOND = ?");
            $stmt -> bind_param('i', $_POST["productId"]);
            $dbResult = $stmt -> execute();
            $stmt -> close();
            
            $stmt = $mysqli -> prepare("DELETE FROM products_data WHERE ID = ?");
            $stmt -> bind_param('i', $_POST["productId"]);
            $dbResult = $stmt -> execute();
            $stmt -> close();

            $mysqli -> close();
            $tempArray = array("State"=>"SUCCESS", "REALSTATE" => $_POST["productId"]);
            echo json_encode($tempArray);
        break;
        default :
        break;
    }
}


function removeImagePath($imgpath) {
    //刪除圖片
    if (file_exists($imgpath)) {
        return unlink($imgpath);
    } else {
        return false;
    }
    
};

/*if(isset($_POST["loginEvent"])) {
    $sqlStr = "SELECT * FROM authorization_data WHERE name = '" + $_POST["account"] + "' AND password = '" + $_POST["password"] + "';";
    $result = $mysqli -> query($sqlStr);
    
    if (mysqli_num_rows($result) > 0) {
        $tempArray = array("State"=> "SUCCESS");
    } else {
        $tempArray = array("State"=> "NONE");
    }
    echo json_encode($tempArray);
}*/




?>