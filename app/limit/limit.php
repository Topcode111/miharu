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
		if (!isset($_SESSION)) {
			session_start();
		}

		// define('TODAY', date("YmdHi"));
		define('TODAY', date("YmdHi"));
		require_once(dirname(__FILE__).'/../php/db.php');
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
		if (($secrndnum = $secrndnumc) && ($auth == 1) && ($keikatime < 4000)) {
			$uuidd = 1;
		} else {
			$uuidd = 0;
		}
	?>
	<head>
		<title>水田モニター</title>
		<meta charset="utf-8">
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />

		<!-- <meta name="viewport" content="width=device-width,initial-scale=1.0"> -->
		<link rel="stylesheet" type="text/css" href="../css/miharu-common.css?<?php echo TODAY?>">
		<link rel="stylesheet" type="text/css" href="../css/theme.css?<?php echo TODAY;?>">
		<link rel="stylesheet" type="text/css" href="./limit.css?<?php echo TODAY;?>">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

		<script type="text/javascript" src="../js/selfmade.js?<?php echo TODAY;?>"></script>
		<script type="text/javascript" src="../js/miharu-data.js?<?php echo TODAY;?>"></script>
		<script type="text/javascript" src="../js/data.js?<?php echo TODAY;?>"></script>
		<script type="text/javascript" src="./limit.js?<?php echo TODAY;?>"></script>
	</head>
	<body onload="init996(<?php echo  $uuidd;?>)">

		<?php
			$gwlistno = $_GET['gwlistno'];

			if($gwlistno < 1) {
				$seekusergroupid = $_SESSION['usergroupid'];
			}
			//管理者の場合,該当小グループidを$gwlistnoの番号と大グループidから取得　
			if($gwlistno > 0) {
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
			$_SESSION['gwip'] = $sgatewayip;
			$_SESSION['gwport '] = $sgatewayport;	
			
			$dbrowno = $_GET['gwlistno'] - 1;

			if ($uuidd == 1 ) {
		?>

		<div name="footer">水田モニター Ver1.1</div>
		<div id="hyname">バルブ自動開閉設定</div>
		<div>水位の上限・下限を設定すると、自動的にバルブの開閉を行います。<br>
			上限水位を設定すると達した場合に閉じ、下限水位を設定すると下回った場合に開かれます。<br>
			数値を入力後、最後に「保存」ボタンを押してください。<br>
			※履歴ダウンロードはPC版ブラウザのみの機能です。
		</div>
		<div class="sticky fix-header">
			<div class="btnLeft">
				<button onclick="button_topmenu('<?php echo $permid;?>')">戻る</button>
			</div>
			<div class="btnRight">
				<input type="date" class="fitdate" id="date1" min="2010-01-01" step="1">
				～
				<input type="date" class="fitdate" id="date2" min="2010-01-01" step="1">
				<button onclick="dl_hist()">履歴DL</button>
				<button onclick="limit_save('<?php  echo  $permid;?>')">保存</button>
			</div>
		</div>
		<div style="margin-left: 5%;margin-right: 5%;">

			<div class="btnLeft"></div>

			<div><button id="bulk_btn" onclick="bulk_open(this)">一括変更▼</button></div>
			<div class="nodisplay" id="bulk_area" style="background-color: orange;">
			<table style="width: 100%;">			
				<tr style="width: 100%;">
					<th>OFF/ON</th>
					<th style="width:30%">上限(㎝)</th>
					<th style="width:30%" colspan="2">下限(㎝)</th>
					<th style="width:30%" colspan="2">期限</th>
				</tr> 
				<tr style="width: 100%;">
					<td align="center">
						<label>
							<input type="checkbox" class="switch_input" id="swbulk5">
							<span class="switch s4"></span>
						</label>
					</td>
					<td align="center" style="width:30%"><input type="number" class="fillnumber" step="0.5" id="upbulk5" oninput="num_limit(this)"></td>
					<td align="center" style="width:30%" colspan="2"><input type="number" class="fillnumber" step="0.5" id="lowbulk5" oninput="num_limit(this)"></td>
					<td align="center" style="width:30%" colspan="2">
						<input type="date" class="filldate" id="date5" min="<?php echo date('Y-m-d'); ?>" step="1">
					</td>
				</tr>
				<tr style="width: 100%;">
					<th>OFF/ON</th>
					<th>開始日付</th>
					<th>時間指定</th>
					<th>開閉</th>
					<th>期間</th>
					<th>開閉時間</th>
				</tr>
				<tr style="width: 100%;">
					<td align="center">
						<label>
							<input type="checkbox" class="switch_input" id="swbulk55">
							<span class="switch s4"></span>
						</label>
					</td>
					<td align="center" style="width:30%"><input type="date" class="filldate" id="date55" min="<?php echo date('Y-m-d'); ?>" step="1"></td>
					<td align="center"><input type="time" class="filltime" id ="time55"></td>
					<td align="center">
						<select id="state5">
							<option value="1" id="available55">開</option>
							<option value="0" id="available66">閉</option>
						</select> 
					</td>
					<td align="center">
						<input type="number" class="fitnumber" id ="bSpan" oninput="num_date(this)">日
					</td>
					<td align="center">
						<input type="number" class="fitnumber" id ="bPeriod" oninput="num_period(this)">h
					</td>
				</tr>
			</table>
		</div>
		<div id="main-table"></div>
		<?php
			}
		?>
	</body>
</html>
