//CSVファイルを読み込む関数getCSV()の定義
let param = [];
function init995(uuidd) {
	if ((uuidd ==  0||uuidd =="") ) {
		alert("ログインしなおしてください。");
		location.href = "../logout.php";
		//window.close();
		//return;
	}


	let setting = get_setting();
	if (setting != "") {
		let items = setting.split(",");
		//let h_items = items[0].split(",");
		//let l_items = items[1].split(",");
        //trueを1に変更
		document.getElementById(`sw-max-temp`).checked = items[0] == "1";
		document.getElementById(`val-max-temp`).value = items[1];
		//trueを1に変更
		document.getElementById(`sw-min-temp`).checked = items[2] == "1";
		document.getElementById(`val-min-temp`).value = items[3];

		//trueを1に変更
		document.getElementById(`sw-max-level`).checked = items[4] == "1";
		document.getElementById(`val-max-level`).value = items[5];
		//trueを1に変更
		document.getElementById(`sw-min-level`).checked = items[6] == "1";
		document.getElementById(`val-min-level`).value = items[7];
	}

	let mails = get_alert_maillist();
	let maillist = new Array();
	//maillist.push("");
	if (mails != "") {
		maillist = mails.split(",");
	}

	let table_str = document.getElementById(`mail-list`).innerHTML;
	for (let i = 0; i < maillist.length; i++) {
		table_str += `<tr><td><input type="mail" id="mail${i}" value="${maillist[i]}"></td></tr>`;
	}
	document.getElementById(`mail-list`).innerHTML = table_str;
}

function setting_save() {
	for(let i = 0; true; i++) {
		let obj = document.getElementById(`mail${i}`);
		if (obj) {
			let mail = obj.value;
			if (mail != "" && !mail_check( mail )) {
				alert(`メールアドレス[${mail}]が不正です。`);
				return;
			}
		} else {
			break;
		}
	}

	//水位警報と温度警報のセッティングを同時じ送信に変更
	//trueを1に変更、falseを0に変更
	let max_state1 = document.getElementById(`sw-max-temp`).checked ? "1" : "0";
	let max_temp1 = document.getElementById(`val-max-temp`).value;
	//trueを1に変更、falseを0に変更
	let min_state1 = document.getElementById(`sw-min-temp`).checked ? "1" : "0";
	let min_temp1 = document.getElementById(`val-min-temp`).value;
	//let data = `${max_state},${max_temp}\n${min_state},${min_temp}`;

	//set_setting( data );
	//trueを1に変更、falseを0に変更
	let max_state2 = document.getElementById(`sw-max-level`).checked ? "1" : "0";
	let max_temp2 = document.getElementById(`val-max-level`).value;
	//trueを1に変更、falseを0に変更
	let min_state2 = document.getElementById(`sw-min-level`).checked ? "1" : "0";
	let min_temp2 = document.getElementById(`val-min-level`).value;
	let data =  `${max_state2},${max_temp2},${min_state2},${min_temp2},${max_state1},${max_temp1},${min_state1},${min_temp1}`;

	set_alertlevel( data );

	//水位警報と温度警報のセッティングを同時じ送信に変更
	let mailarr = new Array();
	for(let i = 0; true; i++) {
		let obj = document.getElementById(`mail${i}`);
		if (obj) {
			if (obj.value != "") {
				mailarr.push(obj.value);
			}
		} else {
			break;
		}
	}
	mailaddata = mailarr.join(",");

	//改行区切りをカンマ区切りに変更
	//set_alert_maillist(mailarr.join("\n"));
	//set_alert_maillist(mailarr.join());
	set_alert_maillist(mailaddata);

	alert("設定を保存しました。");

}

function add_mail() {

	for(let i = 0; true; i++) {
		if (!document.getElementById(`mail${i}`)) {
			let table_str = document.getElementById(`mail-list`).innerHTML;
			table_str += `<tr><td><input type="mail" id="mail${i}"></td></tr>`;
			document.getElementById(`mail-list`).innerHTML = table_str;
			break;
		}
	}
}

function page_back(userpermid) {
	
	if (userpermid == 2 ||userpermid == 3) {
		// 子機一覧を表示
		screen_transition("../top.php");
		return;
		//let path = "./list/list.php";
	} else {
		//ゲート一覧を表示
		screen_transition("../settinggatelist/settinggatelist.php");
		return;
	}
}


function num_level(obj) {

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

let sing = false;
function num_temp(obj, event) {
	// if (event.data == "-" || event.data == "+") {
	// 	if (sing) {
	// 		obj.value = "";
	// 		return;
	// 	} else {
	// 		sing = true;
	// 	}
	// }

	// if (obj.value.length >= 1) {
	// 	if (event.data == "-" || event.data == "+") {
	// 		obj.value = "";
	// 		sing = false;
	// 		return;
	// 	}
	// }

	if (obj.value == "") {
		return;
	}

	let input = Number(obj.value);
	if (isNaN(input)) {
		obj.value = "";
		return;
	}

	if (input > 50) {
		obj.value = 50;
		return;
	}

	if (input < -20) {
		obj.value = -20;
		return;
	}
	obj.value = input;
}