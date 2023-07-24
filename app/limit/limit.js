
function init996(uuidd) {
	if ((uuidd ==  0 || uuidd =="")) {
		alert("ログインしなおしてください。");
		location.href = "../logout.php";
		return;
	}

	let ret = check_login();
	if (ret ==  0 || ret =="") {
		alert("ログイン情報が古くなっています\nログインしなおしてください。");
		location.href = "../logout.php";
		return;
	}

	let date = new Date();
	let now = `${date.getFullYear()}-${padding_num('00', date.getMonth()+1)}-${padding_num('00', date.getDate())}`;

	document.getElementById(`date1`).value = now;
	document.getElementById(`date2`).value = now;

	document.getElementById(`date1`).max = now;
	document.getElementById(`date2`).max = now;
	allflag=0;

	initTable(0);
}

function update() {

	let ret = check_login();
	if (ret ==  0 || ret =="") {
		alert("ログイン情報が古くなっています\nログインしなおしてください。");
		location.href = "../logout.php";
		return;
	}

	initTable(0);
	alert("画面を更新しました。");
}

function button_topmenu(userpermid) {
	let path = "../top.php";
	screen_transition(path);
	if (userpermid == 2 ||userpermid == 3 ){
		// 子機一覧を表示
		screen_transition( '../top.php');
		return;
	} else {
		//ゲート一覧を表示
		screen_transition("../limitgatelist/limitgatelist.php");
		return;
	}
	screen_transition(path);
}



function initTable() {

	let batch = get_batch();
	if (batch != "") {
		let batch_item = batch.split(",");

		let obj = document.getElementById("bulk_area");
		let main = document.getElementById("main-table");
		let btn = document.getElementById("bulk_btn");
		if (batch_item[1] == "1") {
			//一括
			btn.innerHTML = "一括変更▲";
			obj.classList.remove("nodisplay");
			main.classList.add("nodisplay");
			allflag = 1;
		} else {
			//個別
			btn.innerHTML = "一括変更▼";
			obj.classList.add("nodisplay");
			main.classList.remove("nodisplay");
			allflag = 0;
		}

		if (batch_item[2] == "1") {
			document.getElementById(`swbulk5`).checked = "checked";
		} else {
			document.getElementById(`swbulk5`).checked = "";
		}

		document.getElementById("upbulk5").value =  batch_item[3];
		document.getElementById("lowbulk5").value = batch_item[4];
		document.getElementById("date5").value =    batch_item[5].slice(0, 10);
		document.getElementById("date55").value =   batch_item[7].slice(0, 10);
		document.getElementById("time55").value =   batch_item[7].slice(-8);

		if (batch_item[6] == "1") {
			document.getElementById(`swbulk55`).checked = "checked";
		} else {
			document.getElementById(`swbulk55`).checked = "";
		}

		if (batch_item[8] == "1") {
			document.getElementById(`available55`).selected = "selected";
			document.getElementById(`available66`).selected = "";
		} else {
			document.getElementById(`available55`).selected = "";
			document.getElementById(`available66`).selected = "selected";
		}
		document.getElementById("bPeriod").value = batch_item[11];

		document.getElementById("bSpan").value = batch_item[12];
	}

	let lastlog = get_latest_data();
	if (lastlog.length == 0) {
		return;
	}

	let smap = lastlog.sort((a, b) => {
		return a.id - b.id;
	});

	let date = new Date();
	let now = `${date.getFullYear()}-${padding_num('00', date.getMonth()+1)}-${padding_num('00', date.getDate())}`;
	let disp = smap.map(val => {
		let id = val.id;

		let limit_raw = get_limit(id);

		let available1 = "";
		let upvalue = "";
		let lowvalue = "";	
		let deadline = "";
		let datevalue = "";
		let timevalue = "";
		let available2 = "";
		let available3 = "";
		let available4 = "";
		let period = "";
		let span = "";

		if (limit_raw != "") {
			let limit_items = limit_raw.split(",");

			//個別水位
			if (limit_items[14] == "1") {
				available1="checked";
			} else {
				available1="";
			}
		
            //available1 = "checked";
			upvalue = limit_items[15];
			lowvalue = limit_items[16];
			deadline = limit_items[17].slice(0, 10);
			//個別時間
			if (limit_items[27] == "1") {
				available2 = "checked";
			} else {
				available2 = "";
			}

			//available2 = "checked";
			datevalue = limit_items[28].slice(0, 10);
			timevalue = limit_items[28].slice(-8);

			if (limit_items[29] == "1") {
				available3 = "selected";
				available4 = "";
			} else {
				available3 = "";
				available4 = "selected";
			}

			period = limit_items[43];

			// 期日
			let date1 = new Date(limit_items[32]);
			let data2 = new Date(limit_items[28]);
			let difftime = date1 - data2;
			if (difftime != 0) {
				span = parseInt(difftime / 1000 / 60 / 60 / 24);
			} else {
				span = 0;
			}
			

		}

		let item = `
		<tr style="width: 100%;">
			<th align="left" colspan="6" style="width: 100%;">子機名：${get_name(id)}</th>
		</tr>
		<tr>
			<tr style="width: 100%;">
				<th align="center">OFF/ON</th>
				<th align="center" style="width:30%">上限(㎝)</th>
				<th align="center" style="width:30%" colspan="2">下限(㎝)</th>
				<th align="center" style="width:30%" colspan="2">期限</th>
			</tr>
			<td align="center">
				<label>
					<input name="sw" type="checkbox" class="switch_input" id="sw${id}" ${available1}>
					<span class="switch s4"></span>
				</label>
			</td>
			<td><input name="hi" type="number" class="fillnumber" step="0.5" value="${upvalue}" id="up${id}" oninput="num_limit(this)"></td>
			<td colspan="2"><input name="low" type="number" class="fillnumber" step="0.5" value="${lowvalue}" id="low${id}" oninput="num_limit(this)"></td>
			<td colspan="2"><input name="dt" type="date" class="filldate" id="date${id}" value="${deadline}" min="${now}" step="1"></td>
		</tr>
		<tr>
			<th>OFF/ON</th>
			<th>日付指定</th>
			<th>時間指定</th>
			<th>開閉</th>
			<th>期間</th>
			<th>開閉時間</th>
		</tr>
		<tr>
			<td align="center">
				<label>
					<input type="checkbox" class="switch_input" id="swbulk${id}" ${available2}>
					<span class="switch s4"></span>
				</label>
			</td>
			<td><input type="date" class="filldate" id="date4${id}" value="${datevalue}" min="${now}" step="1"></td>
			<td><input type="time" class="filltime" id="time4${id}" value="${timevalue}"></td>
			<td align="center">
				<select id="state${id}">
				<option value=1 ${available3}>開</option>
				<option value=0 ${available4}>閉</option>
				</select>
			</td>
			<td align="center">
				<input type="number" class="fitnumber" id="span${id}" oninput="num_date(this)" value="${span}">日
			</td>
			<td align="center">
				<input type="number" class="fitnumber" id="period${id}" oninput="num_period(this)" value="${period}">h
			</td>
		</tr>				
		`;
		return item;
	});

	document.getElementById("main-table").innerHTML = `<table style="width: 100%;">${disp.join('')}</table>`;
}

function limit_save(userpermid) {

	let ret = check_login();
	if (ret ==  0 || ret =="") {
		alert("ログイン情報が古くなっています\nログインしなおしてください。");
		location.href = "../logout.php";
		return;
	}


	if (userpermid == 2) {
		alert("操作権限がありません!");
		return;
	}

	if(allflag == 1) {
		let pcheck = exists_parmission();
		if(pcheck != 0) {
			alert("管理者に制限されている子機があるため一括設定できません。");
			return;
		}
	}

	let today = new Date();
	today.setHours(0);
	today.setMinutes(0);
	today.setSeconds(0);
	today.setMilliseconds(0);

	// 一括設定 水位
	let bLevelEnable = document.getElementById(`swbulk5`).checked ? "1" : "0";
	let bLebelUpLimit = document.getElementById(`upbulk5`).value.trim();
	if (bLebelUpLimit == "") {
		bLebelUpLimit = "NULL";
	}

	let bLebelDownLimit = document.getElementById(`lowbulk5`).value.trim();
	if (bLebelDownLimit == "") {
		bLebelDownLimit = "NULL";
	}

	let bLebelLimit = document.getElementById(`date5`).value;
	if (bLebelLimit == "") {
		bLebelLimit = "NULL";
	} else {
		bLebelLimit = bLebelLimit + " 23:59:59";
	}

	if (document.getElementById(`swbulk5`).checked) {
		if (bLebelUpLimit == "NULL") {
			alert("一括設定の水位上限値が入力されていません。");
			return;
		}
		if (bLebelDownLimit == "NULL") {
			alert("一括設定の水位下限値が入力されていません。");
			return;
		}
		if (bLebelLimit == "NULL") {
			alert("一括設定の期限が入力されていません。");
			return;
		}

		let limitdate = new Date(bLebelLimit);
		if (today.getTime() > limitdate.getTime()) {
			alert(`一括設定の期限が過ぎています。`);
			return;
		}
	}

	// 一括設定 期限
	let bTimeEnable = document.getElementById(`swbulk55`).checked ? "1" : "0";

	let bTimeDate = document.getElementById(`date55`).value;
	let bTimeTime = document.getElementById(`time55`).value;
	let bTimeLimit = "NULL";
	if (bTimeDate != "" && bTimeTime != "") {
		bTimeLimit = bTimeDate + " " + bTimeTime;
	}

	let bTimeOpenClose = document.getElementById(`state5`).value;

	let bTimePeriod = document.getElementById(`bPeriod`).value.trim();
	if (!bTimePeriod) {
		bTimePeriod = "0";
	}

	let bTimeSpan = document.getElementById(`bSpan`).value.trim();
	if (!bTimeSpan) {
		bTimeSpan = "0";
	}

	if (document.getElementById(`swbulk55`).checked) {
		if (bTimePeriod == "0") {
			alert("一括設定の開閉時間が入力されていません。");
			return;
		}
		if (bTimeSpan == "0") {
			alert("一括設定の期間が入力されていません。");
			return;
		}
	}

	let batchlist = new Array();
	// 有効無効
	batchlist.push(allflag);

	// 水位ONOFF
	batchlist.push(bLevelEnable);
	// 水位上限
	batchlist.push(bLebelUpLimit);
	// 水位下限
	batchlist.push(bLebelDownLimit);
	// 水位期限
	batchlist.push(bLebelLimit);

	// 日時 ONOFF
	batchlist.push(bTimeEnable);
	// 日時 期限
	batchlist.push(bTimeLimit);
	// 日時 開閉
	batchlist.push(bTimeOpenClose);
	// 日時 開閉時間
	batchlist.push(bTimePeriod);
	// 日時 期間
	batchlist.push(bTimeSpan);

	set_batch(batchlist.join(","));

	// グループ内子機の最新データ
	let lastlog = get_latest_data();
	if (lastlog.length == 0) {
		return;
	}

	if(allflag == 1) {

		//上限下限設定
		lastlog.forEach(elem => {

			let array = new Array();
			array.push(6);
			array.push(bLevelEnable);//on/offtrueを1にfalseを0に変更
			array.push(bLebelUpLimit);//上限
			array.push(bLebelDownLimit);//下限
			array.push(bLebelLimit);//期限 datetime
			array.push(""); // 開閉
			array.push(""); // 開閉期間
			array.push(""); // 期間
			set_limit(elem.id, array.join(","));
		});

		//時間設定+時間逆設定
		lastlog.forEach(elem => {
			let array = new Array();
			array.push(4);
			array.push(bTimeEnable);//一括のon/offtrueを1にfalseを0に変更
			array.push("");//上限
			array.push("");//下限
			array.push(bTimeLimit);//期限 datetime
			array.push(bTimeOpenClose); // 開閉
			array.push(bTimePeriod); // 開閉期間
			array.push(bTimeSpan); // 期間
			console.log(array);
			set_limit(elem.id, array.join(","));
		});

		alert("一括自動開閉を保存しました。");
	} else {
		for (let i = 0; i < lastlog.length; i++) {
			let elem = lastlog[i];
			let active = document.getElementById(`sw${elem.id}`).checked;

			if (active) {

				let datestr = document.getElementById(`date${elem.id}`).value;
				if (!datestr) {
					alert(`${get_name(elem.id)}の水位の期限が入力されていません。`);
					return;
				}

				let date = new Date(datestr);

				if (today.getTime() > date.getTime()) {
					alert(`${get_name(elem.id)}の水位の期限が過ぎています。`);
					return;
				}
			}

			let dateenable = document.getElementById(`swbulk${elem.id}`).checked;

			if (dateenable) {
				let datedate = document.getElementById(`date4${elem.id}`).value;
				let datetime = document.getElementById(`time4${elem.id}`).value;

				if (datedate == "" || datetime == "") {
					alert(`${get_name(elem.id)}の日時が指定されていません。`);
					return;
				}

				let tempdate = new Date(`${datedate} ${datetime}`);
				let now = new Date();
				if (now.getTime() > tempdate.getTime()) {
					alert(`${get_name(elem.id)}の日時が過ぎています。`);
					return;
				}

				let period = document.getElementById(`period${elem.id}`).value;
				if (period == "") {
					alert(`${get_name(elem.id)}の開閉時間が入力されていません。`);
					return;
				}
			}
		}

		let pstate = "0";
		//上限下限設定
		lastlog.forEach(elem => {

			if (check_parmission(elem.id) == "0") {
				let active = document.getElementById(`sw${elem.id}`).checked;
				let datestr = document.getElementById(`date${elem.id}`).value;

				let daytime = "NULL";
				if (datestr) {
					daytime = datestr + " 23:59:59";
				}
				let array = new Array();
				array.push(7);
				array.push(active ? "1" : "0");//on/offtrueを1にfalseを0に変更
				array.push(document.getElementById(`up${elem.id}`).value);//上限
				array.push(document.getElementById(`low${elem.id}`).value);//下限
				array.push(daytime);//期限 datetime
				array.push(""); // 開閉
				array.push(""); // 開閉期間
				array.push(""); // 期間
				console.log(array);
				//data=$pr , $offon , $up , $down , $time , $ctrl
				set_limit(elem.id, array.join(","));
			} else {
				pstate = "1";
			}
		});

		//時間設定+時間逆設定
		lastlog.forEach(elem => {

			if (check_parmission(elem.id) == "0") {
				let period = document.getElementById(`period${elem.id}`).value;
				if (!period) {
					period = "0";
				}
	
				let active = document.getElementById(`swbulk${elem.id}`).checked;
				let datestr = document.getElementById(`date4${elem.id}`).value;
				let timestr = document.getElementById(`time4${elem.id}`).value;
			
				let daytime = datestr + " " + timestr;
	
				let span = document.getElementById(`span${elem.id}`).value;
	
				let array = new Array();
				array.push(5);
				array.push(active ? "1" : "0"); //on/offtrueを1にfalseを0に変更
				array.push(""); //上限
				array.push(""); //下限
				array.push(daytime); // 期限 datetime
				array.push(document.getElementById(`state${elem.id}`).value); // 開閉
				array.push(period); // 開閉期間
				array.push(span); // 期間
	
				//data=$pr , $offon , $up , $down , $time , $ctrl
				console.log(array);
				set_limit(elem.id, array.join(","));
			}
		});
		alert("自動開閉を保存しました。");
		if (pstate == "1") {
			alert("管理者に制限されている子機は設定されません。");
		}
	}
}

function dl_hist() {
	let lastlog = get_latest_data();
	if (lastlog.length == 0){
		return;
	}
	let smap = lastlog.sort((a, b) => {
		return a.id - b.id;
	});

	let date1 = document.getElementById(`date1`).value;
	let date2 = document.getElementById(`date2`).value;

	let span = `${date1},${date2}`;

	let csvdata = "";
	smap.forEach(elem => {
		let id = elem.id;
		let raw = get_hist_a(id, span).trim();
		if (raw != "") {
			let childname = get_name(id);
			let rows = raw.split("\n");

			rows.forEach(row => {
				let items = row.split(",");
				csvdata += `${id},${childname},${items[1]},${items[2]},${items[3]}\n`;
			});
		}
	});

	if (csvdata == "") {
		alert("期間内の開閉履歴がありません。");
		return;
	}

	let name = `${date1}～${date2}_開閉履歴.csv`;
	download_csv(name, csvdata);
}


function bulk_open(elem) {
	let obj = document.getElementById("bulk_area");
	let main = document.getElementById("main-table");
	if (elem.innerHTML == "一括変更▼") {
		elem.innerHTML = "一括変更▲";
		obj.classList.remove("nodisplay");
		main.classList.add("nodisplay");
		//一括
		allflag = 1;
	} else {
		elem.innerHTML = "一括変更▼";
		obj.classList.add("nodisplay");
		main.classList.remove("nodisplay");
		allflag = 0;
		//個別
	}
}

function num_limit(obj) {

	if (obj.value == "") {
		return;
	}

	let input = Number(obj.value);
	if (isNaN(input)) {
		obj.value = "";
		return;
	}

	if (input > 20) {
		obj.value = 20;
		return;
	}

	if (input < 0) {
		obj.value = 0;
		return;
	}
	obj.value = input;
}

function num_period(obj) {

	if (obj.value == "") {
		return;
	}

	let input = Number(obj.value);
	if (isNaN(input)) {
		obj.value = "";
		return;
	}

	if (input > 23) {
		obj.value = 23;
		return;
	}

	if (input < 0) {
		obj.value = 0;
		return;
	}
	obj.value = input;

}

function num_date(obj) {

	if (obj.value == "") {
		return;
	}

	let input = Number(obj.value);
	if (isNaN(input)) {
		obj.value = "";
		return;
	}

	if (input > 99) {
		obj.value = 99;
		return;
	}

	if (input < 0) {
		obj.value = 0;
		return;
	}
	obj.value = input;

}
