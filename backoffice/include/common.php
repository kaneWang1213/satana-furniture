<?php
    $time = new DateTime();
    $timestamp = date_timestamp_get($time);
?>
<script src="//code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<!-- Latest compiled and minified CSS -->
<script src="js/backoffice.js"></script>
<script src="js/jquery.simple-dtpicker.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.1.5/angular.min.js"></script>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico?101234">
<link href="css/index.css?<?= $timestamp; ?>" rel="stylesheet">
<?php
function handleImages($delimg) {
    $mainInfo = array("State" => 0,"UploadImg" => array(), "Errors" => array(), "DelImg" => array());
    //產生目錄
    $structure = '../data/' . $_POST["updateId"] . '/img/';
    umask(0); //755 to 777
    if (!file_exists($structure)) {
        mkdir($structure, 0777, true);
    }
    $errorsArray = array(); //總錯誤
    $expensions= array("jpeg","jpg","png","gif"); //圖片格式
    $currentNum = 0; //目前圖片
    //圖片上傳偵測
    while($currentNum < imgNumber) {
        $errors = "";
        $file_name = $_FILES['img' . $currentNum]['name'];
        $file_size = $_FILES['img' . $currentNum]['size'];
        $file_tmp = $_FILES['img' . $currentNum]['tmp_name'];
        $file_type = substr(str_replace("jpeg","jpg",$_FILES['img' . $currentNum]['type']),-3);
        $file_name = strtolower($file_name);
        $file_Arr = explode('.', $file_name);
        $file_ext = end($file_Arr);

        if($file_name !== "" && in_array($file_ext,$expensions)=== false){
            $errors .= "<li>image formate</li>";
        }

        if($file_size > 2097152){
            $errors .= "<li>image size over than 2MB</li>";
        }

        if($errors == ""){
            if($file_name !=="") {
                
                $delimg = str_replace((string)$currentNum, "", $delimg);
                if($currentNum < 4) {
                    array_push($mainInfo["UploadImg"], ", BACK_IMG_" . $currentNum . " = 'img" . $currentNum . "." .  $file_type . "'");
                    removeExistImage("../data/" . $_POST["updateId"]."/img/", "img" . $currentNum);
                    move_uploaded_file($file_tmp, $structure . ("/img" . $currentNum . "." .  $file_type));	
                } else {
                    array_push($mainInfo["UploadImg"], " , BUTTON_IMG = 'buttonImg." .  $file_type . "'");
                    removeExistImage("../data/" . $_POST["updateId"]."/img/", "buttonImg");
                    move_uploaded_file($file_tmp, $structure . ("/buttonImg." .  $file_type));
                }
                
            }
        } else {
            array_push($mainInfo["Errors"], $errors);
        }            
        $currentNum++;
    }

    if(count($mainInfo["UploadImg"]) == 0) {
        $currentNum = 0;
    }
    

    if($delimg !== "") {
        $delarray = explode(",",$delimg);
        foreach($delarray as $delvalue) {
            if($delvalue < 5) {
                array_push($mainInfo["DelImg"], (($currentNum !== 0)? " ,":"") . "BACK_IMG_" . $delvalue . " = null");	
                removeExistImage("../data/" . $_POST["updateId"]."/img/", "img" . $delvalue );
            } else {
                array_push($mainInfo["DelImg"], (($currentNum !== 0)? " ,":"") . "BUTTON_IMG = null");
                removeExistImage("../data/" . $_POST["updateId"]."/img/", "buttonImg");
            }
            $currentNum++;
        }
    }
    if(!count($mainInfo["Errors"]) > 0){
        //無發生錯誤
        $mainInfo["State"] = 1;
    }

    return $mainInfo;
}

function removeExistImage($_filePath, $_fileName) {
    $imgForm = array(".jpg", ".gif", ".png");
    foreach($imgForm as $value) {
        $filedata = $_filePath. $_fileName . $value;
        error_log($filedata);
        if (file_exists($filedata)) {
            unlink($filedata);
        }
    }
}

function uploadImage($img, $path) {
    $file_name = $_FILES[$img]['name'];
    $file_size = $_FILES[$img]['size'];
    $file_tmp = $_FILES[$img]['tmp_name'];
    $file_type = substr(str_replace("jpeg","jpg",$_FILES[$img]['type']),-3);

    $imgState = null;

    if($file_size > 1041152){ //檔案圖片過大
        $imgState = array("State" => "FAILURE");
        return $imgState;
    }
    
    $imgPath = $path . uniqid() . "." . $file_type; //產生圖片（隨機名）
    move_uploaded_file($file_tmp, $imgPath); //上傳圖片
    $imgState = array("State" => "SUCCESS", "Image" => $imgPath);
    return $imgState;
}

?>