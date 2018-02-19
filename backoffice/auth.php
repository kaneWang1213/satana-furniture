<?php
header("Content-Type:text/html;Charset:utf-8");
require_once 'include/config.php';

$isCheck = false;

// DB
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

$username = $mysqli->real_escape_string($_POST['account']);
$password = $mysqli->real_escape_string($_POST['password']);
$auth = "";

$result = mysqli_query($mysqli, "SELECT name, role FROM authorization_data WHERE name = '" . $username . "' AND password = '" . $password . "'");

echo mysqli_num_rows($result);

if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);
    $auth = $data["role"];
	$isCheck = true;
}

$result -> close();
$mysqli -> close();

if($isCheck)
{
    try {
       session_start();
       $_SESSION['UserName'] = $username;
       $_SESSION['Authorize'] = $auth;
       session_write_close();
       header('Location: home.php');  
    } catch (Exception $e) {
        
    };
} else {
	$_SESSION['UserName'] = null;
	header('Location: login.php?message=error');
}

?>
