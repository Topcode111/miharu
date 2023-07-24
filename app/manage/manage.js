function init993(uuidd) {
	if ((uuidd ==  0||uuidd =="") ) {
		alert("ログインしなおしてください。");
		location.href = "../logout.php";
		//window.close();
		//return;
	}

	init_chaildselect();
	if (document.getElementById("user-table")) {
		create_usertable();
	}
}

function update() {
	init_chaildselect();

	//lert("画面を更新しました。");
}

function init_chaildselect() {
	// ====================== COMMENTED BY SACREDDEVKING - BEGIN ===================
	// let lastlog = get_latest_data();
	// ====================== COMMENTED BY SACREDDEVKING - END ===================
	let lastlog = get_latest_data_sort_by_id();
	if (lastlog.length == 0){
		return;
	}

	let html = "";
	lastlog.forEach(elem => {
		let id = elem.id;
		// ====================== COMMENTED BY SACREDDEVKING - BEGIN ===================
		// let childname = get_name(id);
		// ====================== COMMENTED BY SACREDDEVKING - BEGIN ===================
		let childname = elem.name;
		html += `<option value="${id}">${childname}</option>`;
	});
	document.getElementById("chaild").innerHTML = html;

	if (!document.getElementById("chaild2").innerHTML) {
		document.getElementById("chaild2").innerHTML = html;
		document.getElementById("chaild3").innerHTML = html;
		document.getElementById("chaild4").innerHTML = html;
	}
}

function page_back(userpermid) {
	
	if (userpermid == 2 ||userpermid == 3) {
		// 子機一覧を表示
		screen_transition("../top.php");
		//let path = "./list/list.php";
	} else {
		//ゲート一覧を表示
		screen_transition("../managegatelist/managegatelist.php");
	}
}

function all_sycle() {
	// ====================== COMMENTED BY SACREDDEVKING - BEGIN ===================
	// let lastlog = get_latest_data();
	// ====================== COMMENTED BY SACREDDEVKING - END ===================
	let lastlog = get_latest_data_sort_by_id();
	if (lastlog.length == 0){
		return;
	}

	let hour = document.getElementById("hour").value;
	let minu = document.getElementById("minu").value;

	if (hour == "00" && minu == "00") {
		alert("5分未満の周期は設定できません。");
		return;
	}

	var result = window.confirm(`全子機の通信間隔を${parseInt(hour)}時間${parseInt(minu)}分周期へ変更します。`);
    if (!result) {
        return;
    }

	let list = new Array();
	let pr = 1
	lastlog.forEach(elem => {
		let id = elem.id;
		let res = change_cycle( id, hour, minu, pr);
		    switch (res) {
				case "00":
					list.push(`${id} - 正常に受け付けました。`);
					//alert(`${id} - 正常に受け付けました。`);
					break;
				case "04":
					list.push(`${id} - 制御実行待機中です。`);
					//alert(`${id} - 制御実行待機中です。`);
					break;
				default :
					list.push(`${id} - 異常エラーです。`);
					//alert(`${id} - 不明なエラー`);
			}
	});

	if (list.length > 0) {
		alert(list.join("\n"));
	} else {
		//alert(`${id} - 正常に受け付けました。`);
	}
}

function one_sycle(pr) {

	let hour = document.getElementById("hour").value;
	let minu = document.getElementById("minu").value;

	if (hour == "00" && minu == "00") {
		alert("5分未満の周期は設定できません。");
		return;
	}
	let id = document.getElementById("chaild").value;
	var result = window.confirm(`子機[${id}]の通信間隔を${parseInt(hour)}時間${parseInt(minu)}分周期へ変更します。`);
    if (!result) {
        return;
    }
    pr = 2;
	let res = change_cycle(id, hour, minu, pr);
	switch (res) {
		case "00":
			alert(`${id} - 正常に受け付けました。`);
			break;
		case "04":
			alert(`${id} - 制御実行待機中です。`);
			break;
		default :
			alert(`${id} - 異常エラーです。`);
	}
}

function all_state() {
	// ====================== COMMENTED BY SACREDDEVKING - BEGIN ===================
	// let lastlog = get_latest_data();
	// ====================== COMMENTED BY SACREDDEVKING - END ===================
	let lastlog = get_latest_data_sort_by_id();
	if (lastlog.length == 0){
		return;
	}

	let pcheck = exists_parmission();
	if(pcheck != 0) {
		alert("管理者に制限されている子機があるため一括設定できません。");
		return;
	}

	let state = document.getElementById("state").value;
	let flg = state ==1;

	var result = window.confirm(`全子機の水栓を${flg ? "開き" : "閉じ"}ます。`);
    if (!result) {
        return;
    }

	let list = new Array();
	lastlog.forEach(elem => {
		let id = elem.id;
		//data=$pr , $offon , $up , $down , $time , $ctrl
		data =1+","+","+","+","+","+state;
		let res = set_limit(elem.id, data);
	
		    switch (res) {
				case "00":
					list.push(`${id} - 正常に受け付けました。`);
					//alert(`${id} - 正常に受け付けました。`);
					break;
				case "01":
					list.push(`${id} - 制御実行待機中です。`);
					//alert(`${id} - 制御実行待機中です。`);
					break;
				case "":
					list.push(`${id} - ゲートウェイへの通信に失敗しました。`);
					break;
				default :
					list.push(`${id} - 不明なエラー。`);
					//alert(`${id} - 不明なエラー`);
			}
	});

	if (list.length > 0) {
		alert(list.join("\n"));
	} else {
		//alert(`${id} - 正常に受け付けました。`);
	}
}

function all_delete() {

	let slaves = get_slaves();
	if (slaves.length == 0) {
		return;
	}

	let ret = window.confirm('子機の蓄積データを全削除します。\nよろしいですか。');
    
    if ( !ret ) {
		return;
    }

	slaves.forEach(slaveid => {
		del_history(slaveid);
	});
	alert("削除しました。");

}

function one_delete() {

	let id = document.getElementById("chaild2").value;
	var result = window.confirm(`子機[${id}]の蓄積データを削除します。\nよろしいですか。`);
    if (!result) {
        return;
    }

	del_history(id);

	alert("削除しました。");
}

function all_histdata() {

	let raw = get_slavehist();

	if (raw == "") {
		alert("子機データ履歴がありません。");
		return;
	}
	raw = "子機名,日時,水位,水温,電池\n" + raw;

	let name = `全子機データ履歴.csv`;
	download_csv(name, raw);
}

function one_histdata() {
	let id = document.getElementById("chaild3").value;
	let raw = get_oneslavehist(id);

	if (raw == "") {
		alert("子機データ履歴がありません。");
		return;
	}
	raw = "子機名,日時,水位,水温,電池\n" + raw;

	let slavename = document.getElementById("chaild3").options[document.getElementById("chaild3").selectedIndex].text;
	let name = `${slavename}データ履歴.csv`;
	download_csv(name, raw);
}

function create_usertable2() {

	let users = get_childuser();
	let slaves = get_slaves();

	let date = new Date();

	let disp = users.map(val => {

		let info = val.split(",");
		let name = info[1];

		let item = `
		<tr style="width: 100%;">
			<th>▶</th>
			<th align="left" colspan="6" style="width: 100%;">${name}</th>
		</tr>				
		`;

		let slaverow = slaves.map(slave => {
			let sinfo = slave.split(",");
			let sid = sinfo[0];
			let sname = sinfo[1];
			let sgwid = sinfo[2];
			let ret = `
				<tr style="width: 100%;">
					<td></td>
					<td align="left" colspan="6" style="width: 100%;">${sname}</td>
				</tr>
			`;
			return ret;
		});

		return item + slaverow.join('');
	});

	document.getElementById("user-table").innerHTML = `<table style="width: 100%;">${disp.join('')}</table>`;
}

function create_usertable() {
	let slaves = get_slaves();
	if (slaves.length == 0) {
		return;
	}

	let date = new Date();
	let now = `${date.getFullYear()}-${padding_num('00', date.getMonth()+1)}-${padding_num('00', date.getDate())}`;
	let disp = slaves.map(val => {

		let info = val.split(",");

		let id = info[0];
		let name = info[1];

		let limit_raw = get_limit(id);

		let datevalue = "";
		let timevalue = "";
		let available2 = "";
		let available3 = "";
		let available4 = "";
		let period = "";
		let span = "";

		if (limit_raw != "") {
			let limit_items = limit_raw.split(",");

			let parmissionenable = get_parmissionenable(id);

			//個別時間
			if (parmissionenable == "1") {
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
			<th align="left" colspan="6" style="width: 100%;">子機名：${name}</th>
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

	document.getElementById("user-table").innerHTML = `<table style="width: 100%;">${disp.join('')}</table>`;
}

function all_set() {

	var result = window.confirm(`全子機にひとつ目の設定を適応します。\nよろしいですか。`);
    if (!result) {
        return;
    }

	let slaves = get_slaves();
	if (slaves.length <= 1) {
		return;
	}

	let root = slaves[0].split(",")[0];

	let switchval = document.getElementById(`swbulk${root}`).checked;
	let datedate = document.getElementById(`date4${root}`).value;
	let datetime = document.getElementById(`time4${root}`).value;

	let ocstate = document.getElementById(`state${root}`).value;
	let period = document.getElementById(`period${root}`).value;
	let span = document.getElementById(`span${root}`).value;

	for(let i = 1; i < slaves.length; i++) {
		let dest = slaves[i].split(",")[0];
		document.getElementById(`swbulk${dest}`).checked = switchval;
		document.getElementById(`date4${dest}`).value = datedate;
		document.getElementById(`time4${dest}`).value = datetime;
		document.getElementById(`state${dest}`).value = ocstate;
		document.getElementById(`period${dest}`).value = period;
		document.getElementById(`span${dest}`).value = span;
	}
}

function one_order_delete() {
	

	let id = document.getElementById("chaild4").value;
	var result = window.confirm(`子機[${id}]の命令データを削除します。\nよろしいですか。`);
    if (!result) {
        return;
    }

	del_order(id);

	alert("削除しました。");
}

function manage_save(userpermid) {
	if (userpermid == 2) {
		alert("操作権限がありません!");
		return;
	}

	let today = new Date();
	today.setHours(0);
	today.setMinutes(0);
	today.setSeconds(0);
	today.setMilliseconds(0);

	// グループ内子機の最新データ
	let slaves = get_slaves();
	if (slaves.length == 0) {
		return;
	}

	for (let i = 0; i < slaves.length; i++) {

		let info = slaves[i].split(",");

		let id = info[0];
		let name = info[1];

		let dateenable = document.getElementById(`swbulk${id}`).checked;

		if (dateenable) {
			let datedate = document.getElementById(`date4${id}`).value;
			let datetime = document.getElementById(`time4${id}`).value;

			if (datedate == "" || datetime == "") {
				alert(`${name}の日時が指定されていません。`);
				return;
			}

			// let tempdate = new Date(`${datedate} ${datetime}`);
			// let now = new Date();
			// if (now.getTime() > tempdate.getTime()) {
			// 	alert(`${name}の日時が過ぎています。`);
			// 	return;
			// }

			let period = document.getElementById(`period${id}`).value;
			if (period == "") {
				alert(`${name}の開閉時間が入力されていません。`);
				return;
			}
		}
	}

	//時間設定+時間逆設定
	slaves.forEach(elem => {

		let info = elem.split(",");

		let id = info[0];
		let name = info[1];

		let period = document.getElementById(`period${id}`).value;
		if (!period) {
			period = "0";
		}

		let active = document.getElementById(`swbulk${id}`).checked;
		let datestr = document.getElementById(`date4${id}`).value;
		let timestr = document.getElementById(`time4${id}`).value;
	
		let daytime = datestr + " " + timestr;

		let span = document.getElementById(`span${id}`).value;

		if (active) {
			let array = new Array();
			array.push(5);
			array.push(active ? "1" : "0"); //on/offtrueを1にfalseを0に変更
			array.push(""); //上限
			array.push(""); //下限
			array.push(daytime); // 期限 datetime
			array.push(document.getElementById(`state${id}`).value); // 開閉
			array.push(period); // 開閉期間
			array.push(span); // 期間
	
			// data=$pr , $offon , $up , $down , $time , $ctrl
			console.log(array);
			offdslavesettime(id);
			set_limit(id, array.join(","));
		}

		let paritems = new Array();
		paritems.push(id);
		paritems.push(daytime);
		let endtime = new Date(daytime);
		let sdate = endtime.getDate() + parseInt(span);
		endtime.setDate(sdate);
		let dhour = endtime.getHours() + parseInt(period);
		endtime.setHours(dhour);
		paritems.push(formatDate(endtime, 'yyyy-MM-dd HH:mm:ss'));
		paritems.push(span);
		paritems.push(document.getElementById(`state${id}`).value);
		paritems.push(active ? "1" : "0");

		set_slaveparmission(paritems.join(","));

	});
	alert("自動開閉を保存しました。");
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