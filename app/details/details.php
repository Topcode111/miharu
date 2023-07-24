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
		if (!isset($_SESSION)) {
			session_start();
		}
		$auth = $_SESSION['auth'];
		$userid = $_SESSION['userid'];
		$username = $_SESSION['username'];
		$permid = $_SESSION['permid'];
		$usergroupid = $_SESSION['usergroupid'];
		$secrndnum = $_SESSION['secrndnum'];
		$secrndnumcarry = dbmgr::readcsrf($userid);
		$nowdatetime = date("Y/m/d H:i:s");
		$keikatime = strtotime($nowdatetime) - strtotime($secrndnumcarry["0"]["CSRF_DATE"]);
		$secrndnumc = $secrndnumcarry["0"]["CSRF_CD"];
		if (($secrndnum = $secrndnumc) && ($auth == 1) && ($keikatime < 4000)) {
			$uuidd = 1;
		} else {
			$uuidd = 0;
		}
		define('TODAY', date("YmdHi"));
	?>

	<head>
		<title>水田モニター</title>
		<meta charset="utf-8">
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<link rel="stylesheet" type="text/css" href="../css/miharu-common.css?<?php echo TODAY?>">
		<link rel="stylesheet" type="text/css" href="./details.css?<?php echo TODAY?>">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>

		<script type="text/javascript" src="../js/selfmade.js?<?php echo TODAY?>"></script>
		<script type="text/javascript" src="../js/miharu-data.js?<?php echo TODAY?>"></script>
		<script type="text/javascript" src="../js/data.js?<?php echo TODAY?>"></script>
		<script type="text/javascript" src="./details.js?<?php echo TODAY?>"></script>
	
	</head>
	<?php
		//$listlast=dbmgr::readdbgroupslaveuplastlink('001');
		//echo ($listlast);
		//$_GET['gwlistno']=1;
		$gwlistno = $_GET['gwlistno'];
		//gwlistno=0は管理者付き農家または一般農家
		//gwlistno>0は管理者で数はグループ内ゲート番号
		//$_SESSION['usergroupid']="001";
		//管理者以外の場合,所属グループidを取得
		//$gwlistno=1;
		//$_SESSION['usergroupid']="001";
		if ($gwlistno < 1) {
			$seekusergroupid = $_SESSION['usergroupid'];
		}
		//管理者の場合,該当小グループidを$gwlistnoの番号と大グループid
		//から取得　
		if ($gwlistno > 0) {
			$minigroupArry = dbmgr::readdbmingroupdata($_SESSION['usergroupid']);
			$seekusergroupid = $minigroupArry[$gwlistno-1]['GROUP_ID'];
		}
		
		//該当グループidから各種ゲート情報取得
		$sgatewayinfo = dbmgr::readdbgroupgw($seekusergroupid);
		$sgatewayid = $sgatewayinfo[0]['GW_ID'];
		$sgatewayip = $sgatewayinfo[0]['GW_IP_AD'];
		$sgatewayport = $sgatewayinfo[0]['GW_DOWN_PORT'];
		$_SESSION['sgroupid'] = $seekusergroupid;
		$_SESSION['gwid'] = $sgatewayid;
		$_SESSION['gwip'] = $sgatewayip ;
		$_SESSION['gwport '] = $sgatewayport;
		$slaveid=$_GET['id'];
		//$slaveid="d84a87fffefe03ca";

		$slavepilog = dbmgr::readdbslavepisetlog($slaveid);

		$syokisyuuki = sprintf("%02d", (string)$slavepilog[0]['SLAVE_PI_KANNRI_TIMEH']). ":". sprintf("%02d", (string)$slavepilog[0]['SLAVE_PI_KANNRI_TIMEM']);
		
		//$dbrowno = $_GET['gwlistno']-1;
		//ゲートid取得
		//$gwidArray = dbmgr::readdbgroupgw($seekusergroupid );
		//$gwid = $gwidArray[0]['GW_ID'];
		//$_SESSION['gwid'] = $gwid ;
		//echo ($gwlistno);
		//echo ("-");
		//echo ($_SESSION['auth']);
		//echo ("-");
		//echo ($_SESSION['secrndnum']);
		//echo ("-");
		//echo ($_COOKIE['secrndnum']);
		//echo ("-");
		//echo ($secrndnum);
		//echo ($secrndnumc);
		//echo ( $_SESSION['userid']);
		//echo ( $_SESSION['usergroupid']);
		//echo ( $_SESSION['gwid']);
		
		//$uuidd = 1;
	?>
	<body onload="init('<?php echo $uuidd;?>')" onbeforeunload="chart_clear()">
		<?php
			if ($uuidd == 1) {
		?>
		<div name="footer">水田モニター Ver1.1</div>
		<div id="hyname">詳細画面</div>

		<div class="btnRight">
			<button onclick="btn_close()">閉じる</button>
		</div>

		<br>
		<div style="text-align: right;font-size:30px;">周期設定：<input id="inputcycle" type="time" value=<?= $syokisyuuki ?>> <button onclick="cyclebtn('<?php  echo  $permid;?>')">周期変更</button></div>
		<div style="text-align: right;">※周期変更は基本30分間隔です。最短で5分以上ですが、30分以内の短い時間に設定すると電池の持ちが少なくなります。</div>
		<br>
		<div style="text-align: right;">※電源ON時、水位センサーの初期値を得るため、水位が0cmの状態で初回1回のみ実行 <button onclick="update_volt('<?php  echo  $permid;?>')">水位補正</button></div>
		<div style="font-size:45px;">
			<select id="days" onchange="change_date(this)"></select>　期間：
			<label><input type="radio" name="mode" value="today" onchange="change_radio(this)" checked>１日　
			<input type="radio" name="mode" value="week" onchange="change_radio(this)">１週間</label>
		</div>
		<div style="background-color: white;">
			<canvas id="chartwlevel" ></canvas>
		</div>
		<br>
		<div style="background-color: white;">
			<canvas id="chartwtemp"></canvas>
		</div>
		
		<div style="margin-left:5%;margin-right:5%;">
			<div id="tabbody"></div>
		</div>
		<input type="hidden" id="machine_code" value="">
		<div id="update" name="footer"></div>
		<?php
			}
		?>
	</body>

</html>
