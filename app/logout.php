<?php
// ログアウト処理を行う
session_start();
$_SESSION = array();
session_destroy();
setcookie("secrndnumc", "" , time()-10000 );
//require_once './index.php';
header( "Location: ./index.php" );
