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

		define('TODAY', date("YmdH"));
		require_once(dirname(__FILE__).'/../php/db.php');
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

		<link rel="stylesheet" type="text/css" href="../css/miharu-common.css?<?php echo TODAY?>">
		<link rel="stylesheet" type="text/css" href="../css/theme.css?<?php echo TODAY;?>">
		<link rel="stylesheet" type="text/css" href="./manage.css?<?php echo TODAY;?>">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

		<script type="text/javascript" src="../js/selfmade.js?<?php echo TODAY;?>"></script>
		<script type="text/javascript" src="../js/miharu-data.js?<?php echo TODAY;?>"></script>
		<script type="text/javascript" src="../js/data.js?<?php echo TODAY;?>"></script>
		<script type="text/javascript" src="./manage.js?<?php echo TODAY;?>"></script>

	</head>
	<?php

		$gwlistno = $_GET['gwlistno'];

		if ($gwlistno < 1) {
			$seekusergroupid = $_SESSION['usergroupid'];
		}

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
		$_SESSION['gwip'] = $sgatewayip;
		$_SESSION['gwport'] = $sgatewayport;

		$dbrowno =  $_GET['gwlistno'] - 1;
	?>
	<body onload="init993('<?php echo $uuidd;?>')">
		<?php
			if ($uuidd == 1 ) {
		?>
		<div name="footer">水田モニター Ver1.1</div>
		<div id="hyname">管理者機能</div>
		<div></div>
		<div class="sticky fix-header">
			<div class="btnLeft">
				<button onclick="page_back(<?php echo $permid; ?>)">戻る</button>
			</div>

			<div class="btnRight">
				<button <?php if ($permid > 1) { echo "disabled"; } ?> onclick="manage_save(<?php echo $permid; ?>)" >保存</button>
			</div>
		</div>

		<div style="margin-left: 5%;margin-right: 5%;">

			<div>
				<table style="width: 100%;">
					<tr>
						<th colspan="2">子機周期設定</th>
					</tr>
					<tr>
						<td><select id="hour">
							<option value="00">00</option><option value="01">01</option><option value="02">02</option>
							<option value="03">03</option><option value="04">04</option><option value="05">05</option>
							<option value="06">06</option><option value="07">07</option><option value="08">08</option>
							<option value="09">09</option><option value="10">10</option><option value="11">11</option>
							<option value="12">12</option><option value="13">13</option><option value="14">14</option>
							<option value="15">15</option><option value="16">16</option><option value="17">17</option>
							<option value="18">18</option><option value="19">19</option><option value="20">20</option>
							<option value="21">21</option><option value="22">22</option><option value="23">23</option>
							</select>時間<select id="minu">
							<option value="00">00</option><option value="05">05</option><option value="10">10</option>
							<option value="15">15</option><option value="20">20</option><option value="25">25</option>
							<option value="30">30</option><option value="35">35</option><option value="40">40</option>
							<option value="45">45</option><option value="50">50</option><option value="55">55</option>
						</select>分</td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td><button onclick="all_sycle()">全機変更</button></td>
					</tr>
					<tr>
						<td><select style="width: 100%;" id="chaild"></select></td>
						<td><button onclick="one_sycle()">選択変更</button></td>
					</tr>
					<tr><td colspan="2"> </td></tr>
					<tr>
						<th colspan="2">一斉開閉</th>
					</tr>
					<tr>
						<td><select id="state">
							<option value=1>開く</option><option value=0>閉める</option>
						</select></td>
						<td><button onclick="all_state()">全機開閉</button></td>
					</tr>

					<tr>
						<th colspan="2">子機データダウンロード</th>
					</tr>
					<tr>
						<td><select style="width: 100%;" id="chaild3"></select></td>
						<td><button style="width: 100%;" onclick="one_histdata()">選択DL</button></td>
					</tr>
					<tr>
						<td colspan="2"><button style="width: 100%;" onclick="all_histdata()">全子機DL</button></td>
					</tr>

					<tr>
						<th colspan="2">子機データ削除</th>
					</tr>
					<tr>
						<td><select style="width: 100%;" id="chaild2"></select></td>
						<td><button style="width: 100%;" onclick="one_delete()">選択削除</button></td>
					</tr>
					<tr>
						<td colspan="2"><button style="width: 100%;" onclick="all_delete()">全子機削除</button></td>
					</tr>

				</table>
			</div>
			<br>
			<div>
				<table style="width: 100%;">

					<tr>
						<th colspan="2">子機命令データ削除</th>
					</tr>
					<tr>
						<td><select style="width: 100%;" id="chaild4"></select></td>
						<td><button style="width: 100%;" onclick="one_order_delete()">選択削除</button></td>
					</tr>
					<!-- <tr>
						<td colspan="2"><button style="width: 100%;" onclick="all_delete()">全子機削除</button></td>
					</tr> -->

				</table>
			</div>
			<br>
			<br>
			<?php
				if ($permid < 2) {
			?>
			<div><button onclick="all_set()">全設定</button></div>
			<div id="user-table"></div>
			<?php
				}
			?>
		</div>
		<?php
			}
		?>
	</body>

</html>
