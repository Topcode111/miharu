<?php
if(!isset($_SESSION)){
    session_start();
}

switch ($_POST["func"]) {
    case "ck" :
        echo isset($_SESSION['gwid']);
        break;
}
?>