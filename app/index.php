<?php
    if(!isset($_SESSION)){
		session_start();
	}
	
    $_SESSION['auth'] = 0;
	setcookie('secrndnumc',rand(1, 9999),time()+60*60);
    require_once './login.php';
	// define('TODAY', date("YmdHi"));
	//$user = $_COOKIE["userID"];
	//include("./top.html");

	//echo $_SESSION['secrndnum'];
	//echo $_POST['secrndnum'];
    //$_SESSION['secrndnum2'] = $_POST['secrndnum'];
	include("./top.php");
?>
