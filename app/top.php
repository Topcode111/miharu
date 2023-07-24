<?php
	header('Expires: Tue, 1 Jan 2019 00:00:00 GMT');
	header('Last-Modified:' . gmdate( 'D, d M Y H:i:s' ) . 'GMT');
	header('Cache-Control:no-cache,no-store,must-revalidate,max-age=0');
	header('Cache-Control:pre-check=0,post-check=0',false);
	header('Pragma:no-cache');
?>
<!DOCTYPE html>
<?php
    require_once(dirname(__FILE__).'/php/db.php');
   	if(!isset($_SESSION)){
			session_start();
    }
    $auth = $_SESSION['auth'] ;
    $userid = $_SESSION['userid'];
	$username = $_SESSION['username'];
    $permid = $_SESSION['permid'];
    $usergroupid =  $_SESSION['usergroupid'];
    $secrndnum = $_SESSION['secrndnum'];
    $secrndnumcarry = dbmgr::readcsrf($userid);
	$nowdatetime = date("Y/m/d H:i:s");
	$keikatime = strtotime($nowdatetime) - strtotime($secrndnumcarry["0"]["CSRF_DATE"]);
	$secrndnumc = $secrndnumcarry["0"]["CSRF_CD"];

	if (($secrndnum =  $secrndnumc ) && ($auth == 1) && ($keikatime < 4000)) {
		$uuidd = 1;
	} else {
		$uuidd = 0;
	}
    
	//echo ($uuidd);
	//echo ("-");

	//echo ($auth);
	//echo ("-");
	//echo ($secrndnum);
	//echo ("-");
	//echo ($secrndnumc);
	//echo ("-");
	//echo ($userid);
	//echo ("-");
	//echo session_id();
	//echo ("-");
	//echo ($permid );
	//$uuidd = 1;	
?> 
<html>
	<head>
		<title>水田モニター</title>
		<meta charset="utf-8">
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />

		<!-- <meta name="viewport" content="width=device-width,user-scalable=0"> -->

		<link rel="stylesheet" type="text/css" href="./css/miharu-common.css?<?php define('TODAY', date("YmdHi")); echo TODAY?>">
		<link rel="stylesheet" type="text/css" href="./top/top.css?<?php echo TODAY?>">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script type="text/javascript" src="./js/selfmade.js?<?php echo TODAY?>"></script>
		<script type="text/javascript" src="./js/user.js?<?php echo TODAY?>"></script>
		<script type="text/javascript" src="./top/top.js?<?php echo TODAY?>"></script>

	</head>
	<body onload="init999(<?php echo  $uuidd;?>)">
		<?php
			if ($uuidd == 1) {

				$groupdata = dbmgr::getdbgroup($usergroupid);
				$groupname = $groupdata[0]['GROUP_NAME']; 
				
				if($permid < 2) {
					$bottanname = "グループ一覧";
				} else {
					$bottanname = "子機一覧";
				}
			
				$vtopcont='hidden';
				if ($permid == 1 || $permid == 3) {
					$vtopcont='visible';
				}
		?>
	
        <div name="footer">水田モニター Ver1.1
		<br>
		    <div class="btnLeft">
				<button onclick="button_logout()" type="button">ログアウト</button>
			</div>
			<br>
		</div>
		<div style="text-align:center; width: 100%;" id="hyname"><p>AV自動水栓遠隔開閉システム</p></div>
        <div style="margin-left:5%;margin-right:5%;">
			<div style="font-size: 30px;margin-left:15%;" ><?php echo $groupname;?></div>
			<div style="margin-left:15%;margin-right:15%;margin-top:5%;text-align:centpermier;">
				<button onclick="button_gatelist('<?php  echo  $permid;?>')" style="width: 100%;"><?php  echo  $bottanname;?></button>
				<br>
				<br>
				<button id="btnlimit" onclick="button_limitgate('<?php  echo  $permid;?>')" style="width: 100%;">バルブ自動開閉</button>
				<br>
				<br>
				<button id="btnsetting" onclick="button_settinggate('<?php  echo  $permid;?>')" style="width: 100%;">通知設定</button>
				<br>
				<br>
				<button onclick="button_password()"  style="width: 100%;">パスワード変更</button>
				<br>
				<br>
				<button id="btnmanage" onclick="button_managegate('<?php  echo  $permid;?>')" style="width: 100%;">管理者機能</button>
			</div>
			<script>
				//初期表示は非表示
				document.getElementById("btnsetting").style.visibility ="<?php  echo  $vtopcont;?>";
				document.getElementById("btnmanage").style.visibility ="<?php  echo  $vtopcont;?>";
			</script>
        </div>
		<br>
		<br>
		<div style="margin-left:15%;margin-right:15%;text-align:center;">
			<img src="src/logo.png" alt="asahi" title="asahi" style="width: 50%;">
		</div>
		<div style="text-align:right;width: 95%;font-size: 30px;">
			旭有機材株式会社
		</div>

		<?php
				exit();
			}
		?>
    </body>
</html>

