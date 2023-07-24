<?php
	header('Expires: Tue, 1 Jan 2019 00:00:00 GMT');
	header('Last-Modified:' . gmdate( 'D, d M Y H:i:s' ) . 'GMT');
	header('Cache-Control:no-cache,no-store,must-revalidate,max-age=0');
	header('Cache-Control:pre-check=0,post-check=0',false);
	header('Pragma:no-cache');
?>
<!DOCTYPE html>
<html>
<?php
	require_once(dirname(__FILE__).'/../php/db.php');
	if(!isset($_SESSION)){
		session_start();
		//$_SESSION['auth'] = 0;
	}
	//$auth = $_SESSION['auth1'] ;
	//$userid = $_SESSION['userid1'];
	//$username = $_SESSION['username1'];
	//$permid = $_SESSION['permid1'] ;
	//$usergroupid =  $_SESSION['usergroupid1'] ;
	//$secrndnum = $_SESSION['secrndnum1'];
	//$secrndnumc = $_COOKIE['secrndnum1'];
	//$_SESSION['auth'] = $auth;
	//$_SESSION['userid'] = "YSEO88";
	//$_SESSION['username'] = $username;
	//$_SESSION['permid'] = $permid;
	//$_SESSION['usergroupid'] = "001";
	//$_SESSION['secrndnum'] = $secrndnum;
	//$_COOKIE['secrndnum'] = $secrndnum;		
	//$_SESSION['auth'] = 0;
	$auth = $_SESSION['auth'] ;
	$userid = $_SESSION['userid'];
	$username = $_SESSION['username'];
	$permid = $_SESSION['permid'] ;
	$usergroupid =  $_SESSION['usergroupid'] ;
	$secrndnum = $_SESSION['secrndnum'];
	$secrndnumcarry = dbmgr::readcsrf($userid);
	$nowdatetime = date("Y/m/d H:i:s");
	$keikatime = strtotime($nowdatetime) - strtotime($secrndnumcarry["0"]["CSRF_DATE"]);
	$secrndnumc = $secrndnumcarry["0"]["CSRF_CD"];
	if (($secrndnum =  $secrndnumc )&&( $auth==1)&&($keikatime<4000)) {
		$uuidd = 1;
	} else {
		$uuidd = 0;
	}
       	
		
		
	//$uuidd = 1;
    //require_once '../../login.php';
	define('TODAY', date("YmdHi"));
	//$user = $_COOKIE["userID"];
	//include("./top.html");
	//include("./top.php");
	//$_COOKIE["userID"] = 'YSEO88';
	//$userid = $_COOKIE["userID"];
	//echo ($uuidd);
	//echo $_SESSION['tokennew'] ;
	//echo $_SESSION['tokennew'];$uuidd
	//echo $_SESSION['token'] ;
	//echo $post['token'] ;
	//echo ($_SESSION['auth']);
	//echo ($_SESSION['auth']);
	//echo ("-");
	//echo ($_SESSION['secrndnum']);
	//echo ("-");
	//echo ($_COOKIE['secrndnumc']);
	//echo ("-");
	//echo ($secrndnum);
	//echo ($secrndnumc);
	//echo ( $_SESSION['userid']);
	//echo $_SESSION['usergroupid'];
    //echo session_id();
	//$uuidd=1;
?>

	<head>
		<title>水田モニター</title>
		<meta charset="utf-8">
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />

		<!-- <meta name="viewport" content="width=device-width,user-scalable=0"> -->

		<link rel="stylesheet" type="text/css" href="../css/miharu-common.css?<?php echo TODAY?>">
		<link rel="stylesheet" type="text/css" href="./settinggatelist.css?<?php echo TODAY?>">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script type="text/javascript" src="../js/selfmade.js?<?php echo TODAY?>"></script>
		<script type="text/javascript" src="../js/user.js?<?php echo TODAY?>"></script>
		<script type="text/javascript" src="./settinggatelist.js?<?php echo TODAY?>"></script>

	</head>
	<body onload="init998(<?php echo  $uuidd;?>)">
    <!--<body onload="init('<?php //echo $userid;?>')">-->
	
<?php   
		//グループ名
		$groupdata = dbmgr::getdbgroup($usergroupid);
        $groupname = $groupdata[0]['GROUP_NAME']; 
		//対象ユーザーのグループ数
        //$items = dbmgr::getdbgroup($usergroupid);
		//$userid = 'YSEO88';
        //$userdata = dbmgr::getuserdata($userid);
		//$usergroupid="001";
		//対象ユーザのゲートウエイIDとゲートウエイ名
        //$items = dbmgr::getdbgroup($usergroupid);
        //$gwid = $items[0][GROUP_GW_NUMBER]; 
        //$usergwtno = $userdata[0]['USER_GW_ＴNUMBER'];
        //グループid内のゲート数を取得
		//$usergwtno0　=  dbmgr::readdbmingroupg($usergroupid);
		//小グループの数(ゲートの数）取得
		$usergwtno0=  dbmgr::readdbmingroupg($usergroupid);
		$usergwtno=$usergwtno0[0]["COUNT(GROUP_ID)"];

		$groupname00=dbmgr::readdbmingroupdata($usergroupid) ;
		$lastno = $usergwtno +1;
		for ($i = 1; $i < $lastno; $i++){
   			if($groupname00[$i-1]['GROUP_NAME']){
  			   $groupmname[$i] = $groupname00[$i-1]['GROUP_NAME'];
  			  }else{
  			  $groupmname[$i]="";
 			}
		}

		$vhcont1='visible';
		$vhcont2='hidden';
		$vhcont3='hidden';
		$vhcont4='hidden';
		$vhcont5='hidden';
		$vhcont6='hidden';
		$vhcont7='hidden';
		$vhcont8='hidden';
		



		if ($usergwtno>1){
			$vhcont2='visible';
			}
 	    if ($usergwtno>2){
			$vhcont3='visible';
			}
			if ($usergwtno>3){
				$vhcont4='visible';
				}
				if ($usergwtno>4){
					$vhcont5='visible';
					}
					if ($usergwtno>5){
						$vhcont6='visible';
						}
						if ($usergwtno>6){
							$vhcont7='visible';
							}
							if ($usergwtno>7){
								$vhcont8='visible';
								}
								
						
					//if ($usergwtno>5){
					//	$vhcont3='visible';
					//	}
					//echo $usergwtno	;		
					
					if ($uuidd == 1 ) {
						
								  
?>

<!--<body onload="set_visibility('<?php  echo  $usergwtno;?>');">-->
<!--<body onload="set_visibilitygate();">-->

<div name="footer">水田モニター Ver1.1</div>
			<div class="btnLeft">
				<button onclick="button_topmenu()" type = "button"    >メニュー</button>
			</div>
			<div class="btnRight">
				<!--<button onclick="update()" >更新</button>-->
			</div>
	
		<div style="margin-left: 5%;margin-right: 5%;">
			<p id="tabcontrol"></p>
			<div id="tabbody"></div>
		</div>
		<input type="hidden" id="machine_code" value="">
		<div id="update" name="footer"></div>
			<br>
			<br>
		</div>
        <div style="margin-left:5%;margin-right:5%;">
			<div style="text-align:center;" id="hyname">AV自動水栓遠隔開閉システム</div>
			<!-- <div style="margin-left:15%;margin-right:15%;text-align:center;">
				<img src="src/title.png" alt="MIHARU" title="MIHARU" style="width: 80%;">
			</div> -->
			
			<div style="font-size: 30px;margin-left:15%;" id="groupname0"><?php echo $groupname;?></div>
			<div style="margin-left:15%;margin-right:15%;margin-top:5%;text-align:center;">
				<button id="btgatw1" onclick="button_gatw(1)" style="width: 100%;"><?php  echo  $groupmname[1];?></button>
				<br>
				<br>
				<br>
				<button id="btgatw2" onclick="button_gatw(2)" style="width: 100%;"><?php  echo  $groupmname[2];?></button>
				<br>
				<br>
				<br>
				<button id="btgatw3" onclick="button_gatw(3)" style="width: 100%;"><?php  echo  $groupmname[3];?></button>
				<br>
				<br>
				<br>
				<button id="btgatw4" onclick="button_gatw(4)" style="width: 100%;"><?php  echo  $groupmname[4];?></button>
				<br>
				<br>
				<br>
				<button id="btgatw5" onclick="button_gatw(5)" style="width: 100%;"><?php  echo  $groupmname[5];?></button>				
				<br>
				<br>
				<br>
				<button id="btgatw6" onclick="button_gatw(6)" style="width: 100%;"><?php  echo  $groupmname[6];?></button>
				<br>
				<br>
				<br>
				<button id="btgatw7" onclick="button_gatw(7)" style="width: 100%;"><?php  echo  $groupmname[7];?></button>	
				<br>
				<br>
				<br>
				<button id="btgatw8" onclick="button_gatw(8)" style="width: 100%;"><?php  echo  $groupmname[8];?></button>

				<!-- <br>
				<br>
				<br>
				<button onclick="button_timer()" style="width: 100%;">開閉予約</button> -->
			</div>

			<script>
          //初期表示は非表示
              document.getElementById("btgatw1").style.visibility ="<?php  echo  $vhcont1;?>";
              document.getElementById("btgatw2").style.visibility ="<?php  echo  $vhcont2;?>";
			  document.getElementById("btgatw3").style.visibility ="<?php  echo  $vhcont3;?>";
			  document.getElementById("btgatw4").style.visibility ="<?php  echo  $vhcont4;?>";
			  document.getElementById("btgatw5").style.visibility ="<?php  echo  $vhcont5;?>";
              document.getElementById("btgatw6").style.visibility ="<?php  echo  $vhcont5;?>";
			  document.getElementById("btgatw7").style.visibility ="<?php  echo  $vhcont5;?>";
			  document.getElementById("btgatw8").style.visibility ="<?php  echo  $vhcont5;?>";
			 


           </script>
        
        </div>
		<br>
		<br>
        <!--<div id="update" name="footer" style="position: fixed; bottom: 10px; left:0px; width: 100%">
			<div style="margin-left:15%;margin-right:15%;text-align:center;">
				<img src="src/logo.png" alt="asahi" title="asahi" style="width: 50%;">
			</div>
			<div style="text-align:right;width: 95%;font-size: 30px;">
				旭有機材株式会社
			</div>-->
		</div>
		
    </body>

</html>
<?php
}
?>