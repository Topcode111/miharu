let params = [];

function init(uuidd) {
	// uuidd = 1;
	if (uuidd ==  0||uuidd == "") {
		alert("ログインしなおしてください。");
		location.href = "../logout.php";
		//window.close();
		return;
	}

	params = get_url_param();
	let name = get_name(params.id);
	// document.getElementById('field_name').innerHTML = `圃場：${name} - ${params.id}`;
	// document.getElementById('field_name').innerHTML = `子機「${name}」詳細画面`;
	document.getElementById('hyname').innerHTML = `子機「${name}」詳細画面<br>${params.id}`;

	init_seldays(params.id);

	let select = document.getElementById('days');
	disp(select.options[0].value);
}

function update() {
	
	params = get_url_param(params.id);
	let name = get_name(params.id);
	// document.getElementById('field_name').innerHTML = `圃場：${name} - ${params.id}`;
	document.getElementById('field_name').innerHTML = `圃場：${name}`;

	init_seldays(params.id);

	let select = document.getElementById('days');
	disp(select.options[0].value);
	alert("画面を更新しました。");
}


function dispweek() {
	let date = document.getElementById('days').value;
	let logs = get_week_average(params.id, date).reverse();

	let day = new Date(date.replace(/-/g,"/"));

	let labellist = new Array();
	let datalist = new Array();
	let templist = new Array();
	let html = `<table><tr><th>日時</th><th>水位</th><th>水温</th></tr>`;

	for (let i = 0; i < 7; i++) {

		let dateitem = "--";
		let tempitem = "--";

		logs.forEach(elem => {
			let items = elem.trim().split(",");
			let dateitems = items[0].split("-");
			let target = new Date(dateitems.join("/"));
			
			if (day.getTime() == target.getTime()) {
				
				dateitem = items[1];
				tempitem = items[2];
		
				html += `
				<tr>
					<td>${items[0]}</td>
					<td>${(items[1] == "--" ? "--" : parseFloat(items[1]).toFixed(1)) + "㎝"}</td>
					<td>${(items[2] == "--" ? "--" : parseFloat(items[2]).toFixed(1)) + "℃"}</td>
				</tr>
				`;
			}
		});

		labellist.push((day.getMonth()+1) + "/" + day.getDate());
		datalist.push(dateitem);
		templist.push(tempitem);
		day.setDate(day.getDate() - 1);
	}
	labellist.reverse();
	datalist.reverse();
	templist.reverse();

	day.setDate(day.getDate() + 1);

	let tempdate = new Date(date.replace(/-/g,"/"));
	let datestr = (day.getMonth()+1) + "/" + day.getDate() + "～" + (tempdate.getMonth()+1) + "/" + tempdate.getDate();

	drawWlevel(datestr, labellist, datalist);
	drawWtemp(datestr, labellist, templist);

	html += `</table>`;
	document.getElementById('tabbody').innerHTML = html;
}

function disp(datestr) {
	let logs = get_day_data(params.id, datestr);
	let today = datestr;//.split("-").join("/");

	let dataMap = logs
		.filter(val => val.id == params.id && val.date === today);
	let sortMap = dataMap.sort((a, b) => {
		return a.getDateTime() - b.getDateTime();
	});

	let labellist = new Array();
	let datalist = new Array();
	let templist = new Array();

	for (let i = 0; i < 24; i++) {
		let ave = sortMap
			.filter(val => val.getDateTime().getHours() == i);
		let level_count = 0;
		let level_total = 0;

		let temp_count = 0;
		let temp_total = 0;

		ave.forEach(row => {
			let level = row.getLevel2();
			if (level != "--" && level != "??") {
				level_total += parseFloat(level);
				level_count++;
			}
			let temp = row.getTemperature();
			if (temp != "--") {
				temp_total += parseFloat(temp);
				temp_count++;
			}
		});

		labellist.push(i + "時");
		if (level_count > 0) {
			datalist.push((level_total/level_count).toFixed(1));
		} else {
			datalist.push("--");
			//datalist.push(0);
		}

		if (temp_count > 0) {
			templist.push((temp_total/temp_count).toFixed(1));
		} else {
			templist.push("--");
			//templist.push(0);
		}
	}
	// drawWlevel(today, labellist, datalist);
	// drawWtemp(today, labellist, templist);

	let label = new Array();
	let wlevel = new Array();
	let wtemp = new Array();
	let prev_level = "";
	let prev_temp = "";
	let prev_label = "";
	for (let i = 0; i < labellist.length; i++) {
		if (i % 2 == 0) {
			prev_label = labellist[i];
			prev_level = datalist[i];
			prev_temp = templist[i];
		} else {

			label.push(prev_label);

			if (prev_level == "--") {
				wlevel.push(datalist[i]);
			} else {
				if (datalist[i] == "--") {
					wlevel.push(prev_level);
				} else {
					wlevel.push(((parseFloat(prev_level) + parseFloat(datalist[i])) / 2).toFixed(1));
				}
			}

			if (prev_temp == "--") {
				wtemp.push(templist[i]);
			} else {
				if (templist[i] == "--") {
					wtemp.push(prev_temp);
				} else {
					wtemp.push(((parseFloat(prev_temp) + parseFloat(templist[i])) / 2).toFixed(1));
				}
			}
		}
	}
	
	drawWlevel(today, label, wlevel);
	drawWtemp(today, label, wtemp);

	let listmap = dataMap.sort((a, b) => {
		return b.getDateTime() - a.getDateTime();
	});

	let html = `<table><tr><th>日時</th><th>水位</th><th>水温</th></tr>`;
	let disp = listmap.map(val => {
		let item = `
		<tr>
			<td>${val.date} ${val.time}</td>
			<td>${val.getLevel_unit()}</td>
			<td>${val.getTemp_unit()}</td>
		</tr>
		`;
		return item;
	});
	html += disp.join('') + `</table>`;
	document.getElementById('tabbody').innerHTML = html;

}

let myLineChart = "";
// 水位グラフの描画処理
function drawWlevel(date, labellist, datalist) {

	let ctx = document.getElementById("chartwlevel");

	Chart.defaults.global.defaultFontSize = 25;
	if (myLineChart !== "") myLineChart.destroy();

	myLineChart = new Chart(ctx, {
		type: 'line',
		data: {
			labels: labellist,
			datasets: [
				{
					label: '水位',
					data: datalist,
					borderColor: "rgba(0,0,255,1)",
					backgroundColor: "rgba(0,0,0,0)"
				}
			],
		},
		options: {
			title: {
				display: true,
				text: date
			},
			scales: {
				yAxes: [{
					ticks: {
						min: 0,
						max: 20,
						callback: function (value, index, values) {
							return value + '㎝'
						}
					}
				}]
			},
			elements: {
				line: {
					tension: 0, // ベジェ曲線を無効にする
				}
			}
		}
	});
	myLineChart.update();
}

let myTempChart = "";
function drawWtemp(date, labellist, templist) {

	let ctx = document.getElementById("chartwtemp");

	Chart.defaults.global.defaultFontSize = 25;
	if (myTempChart !== "") myTempChart.destroy();

	myTempChart = new Chart(ctx, {
		type: 'line',
		data: {
			labels: labellist,
			datasets: [
				{
					label: '水温',
					data: templist,
					borderColor: "rgba(255,0,0,1)",
					backgroundColor: "rgba(0,0,0,0)"
				}
			],
		},
		options: {
			title: {
				display: true,
				text: date
			},
			scales: {
				yAxes: [{
					ticks: {
						min: 0,
						max: 45,
						callback: function (value, index, values) {
							return value + '℃'
						}
					}
				}]
			},
			elements: {
				line: {
					tension: 0, // ベジェ曲線を無効にする
				}
			}
		}
	});
	myTempChart.update();
}

// 日付選択の作成
function init_seldays(id) {
	const MAX_LIMIT = 7;

	// logファイル名から過去日付を取得
	let ret0 = get_date_dates(id);
	let ret = ret0.split(',');
	//let ret = [["2021-10-03"], ["2021-10-02"], ["2021-10-01"], ["2021-09-30"], ["2021-09-29"], ["2021-09-28"], ["2021-09-27"], ["2021-09-26"]];

	//　選択日付の上限の設定
	let limit = ret.length < MAX_LIMIT ? ret.length : MAX_LIMIT;

	// 日付子要素の生成
	let items = "";
	for (let i = 0; i < limit; i++) {
		items = items + `<option value="${ret[i]}">${ret[i]}</option>`;
	}
	document.getElementById('days').innerHTML = items;
}

// select選択時処理
function change_date(obj) {

	const modes = document.getElementsByName("mode");
	let str = "";
	for (let i = 0; i < modes.length; i++) {
		if (modes[i].checked) {
			str = modes[i].value;
			break;
		}
	}

	if (str === "week") {
		dispweek();
	} else {
		// 選択された日付でデータを取得し再表示
		disp(obj.value);
	}
}

function change_radio(obj) {
	let val = obj.value;
	if (val === "week") {
		dispweek();
	} else {
		let select = document.getElementById('days');
		disp(select.value);
	}
}

function cyclebtn(userpermid) {

	if (userpermid ==2 ){
		alert( "操作権限がありません。!" );
		return;
	}

	let time = document.getElementById("inputcycle").value;
	let times = time.split(":");
	if (times[0] == "00" && parseInt(times[1]) < 5) {
		alert("5分未満の周期は設定できません。");
		return;
	}

	var result = window.confirm(`子機の通信間隔を${parseInt(times[0])}時間${parseInt(times[1])}分周期へ変更します。`);
	if (!result) {
		return;
	}

	let pr = 3;
	let res = change_cycle( params.id, times[0], times[1], pr);
	switch (res) {
		case "00":
			alert("正常に受け付けました。");
			//create_table();
			break;
		case "04":
			alert("制御実行待機中です。");
			break;
		//case "":
		//	alert("ゲートウェイへの通信に失敗しました。");
		//	break;
		default :
			//alert("[code:" + res + "] 不明なエラー");
			//
			alert("異常エラーです。");
	}
}

function update_volt(userpermid) {

	if (userpermid ==2 ){
		alert( "操作権限がありません。!" );
		return;
	}

	let data = get_latest_one(params.id);

	if (data == "") {
		alert("通信に失敗しました。");
		return;
	}
	let dataary=data.split(',');
	dataary[14]

	if (dataary[14] == "--") {
		alert("水位センサーが正常に動作していません。");
		return;
	}
	var result = window.confirm(`現在の水位を0㎝として設定します。`);
	if (!result) {
		return;
	}

	if (!send_volt(params.id, dataary[14])) {
		alert("水位補正に失敗しました。");
		return;
	}
	alert("水位補正を正常に完了しました。");
	//init();
}

function btn_close() {
	chart_clear();
	history.back();
	window.close();
}

function chart_clear() {
	if (myLineChart !== "") {
		myLineChart.clear();
		myLineChart.destroy();
	}
	if (myTempChart !== "") {
		myTempChart.clear();
		myTempChart.destroy();
	}
}

// function btn_change() {
// 		// 入力ダイアログを表示 ＋ 入力内容を user に代入
// 		let name = window.prompt("変更する名称を入力してください", "");
// 		name = name.trim();
// 		if (name) {
// 			change_name(params.id, name);
// 		} else {
// 			alert('キャンセルされました');
// 		}
// }