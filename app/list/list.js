function init997(uuidd) {
	if ((uuidd ==  0||uuidd =="") ) {
		alert("ログインしなおしてください。");
		location.href = "../logout.php";
		//window.close();
		//return;
	}
	initTable(0);

	//document.getElementById("groupname0").innerHTML = get_group_name();
 }

// function update(sgatewayid) {
// 	initTable2(sgatewayid);
// 	if (page_index != "") {
// 		tabChange(page_index);
// 	}
// 	alert("画面を更新しました。");
// }

function update() {

	let ret = check_login();
	if (ret ==  0 || ret =="") {
		alert("ログインしなおしてください。");
		location.href = "../logout.php";
		return;
	}

	initTable(0);
	if (page_index != "") {
		tabChange(page_index);
	}
	alert("画面を更新しました。");
}

function initTable(curTabIdx) {

	let lastlog = get_latest_data_sort_by_id();
	if (lastlog.length == 0) {
		return;
	} 
    satime = 4;
	let smap = lastlog.sort((a, b) => {
		return a.id - b.id;
	});

	let disp = smap.map(val => {
		let id = val.id;
		let No = val.num;
		let state = "";
		let statestr = "異";
		let statenow = "異常";
		let btnstr = "無効";
		let orderstr = "なし";

		// let gmode = get_mode(id);
		let gmode = val.mode;

		if (gmode != "") {
			Uplink = gmode.split(",");

			if (Uplink[13] == 1) {
				state = "open";
				statestr = "開";
				statenow = "開";
				btnstr = "閉める";
			}
			if (Uplink[13] == 2) {
				state = "close";
				statestr = "閉";
				statenow = "閉";
				btnstr = "開ける";
			}
	    }
		let blink = "";

		// let ordera = get_limit(id);
		let ordera = val.limit;

		if (ordera != "") {
			let order  =  ordera.split(",");

			if (order[3] == 1) {
				blink = `class="blink"`;
				if (order[4]==1) {
					orderstr = "開";
					btnstr = "閉める";
				} else {
					orderstr = "閉";
					btnstr = "開ける";
				}
			} else if (order[14] == 1) {
				// Auto Control
				blink = `class="blink"`;
				// TODO - Show the correct status. But I can't see what it is here.
			}  else {
				blink = "";
			}
		}
		let wlevel = val.getLevel();
		let wtemp = val.getTemperature();
		let vvolt = val.getvoltage();

		let date = formatDate(val.getDateTime(), 'yyyy-MM-dd');
		let datetime = formatDate(val.getDateTime(), 'yyyy-MM-dd HH:mm:ss');
		let today = new Date();
		let uptime = new Date(datetime);
        let satime = (today.getTime() - uptime.getTime())/ (1000 * 60 * 60);
		
		if (satime<3){tabCr="#b8bcff"}else{tabCr= "#C0C0C0"};

		let item = `
		<tr><th colspan="7" bgcolor=${tabCr}>${val.name}</th></tr>
		<tr bgcolor=${tabCr} id="taget${id}" onclick="opendetail('${id}')"> 
			<th height="100" align="left">${No}</th>
			<th align="center" colspan="3">${val.getDateMMDD()} ${val.getTimeHHMM()}</th>
			<th colspan="2" align="right">詳細へ>></th>
		</tr>	
		<tr>
			<td rowspan="3" align="left">
				<button class="state ${state}">${statestr}</button>
			</td>
			<td rowspan="3" align="center" style="${createWLevel(wlevel)}">水位<br />${wlevel}㎝</td>
			<td rowspan="3" align="center" style="${createWTemp(wtemp)}">水温<br />${wtemp}℃</td>
			<td rowspan="3" align="center" style="${createBTlevel(vvolt, val.voltstate)}">電池<br />${vvolt}V</td>
			<td style="text-align: right;">現在:</td>
			<td class="state" style="">${statenow}</td>
		</tr>
		<tr onclick="ctrl_date('${id}','${date}')">
			<td style="text-align: right;"  id="nows${id}" ${blink}>動作中:</td>
			<td class="state" id="now${id}">${orderstr}</td>
		</tr>
		<tr>
			<td style="text-align: right;">開閉:</td>
			<td class="state">
				<button id="btn${id}" value="${id}" onclick="statebtn(this)" style="width:100%;height:100%;">${btnstr}</button>
			</td>
		</tr>
		`;

		return item;
	});

	let tabbtn = new Array();
	let tabpage = new Array();
	let tabindex = 0;
	let tabcount = 0;
	let page = '';
	for (let i = 0; i < disp.length; i++) {
		if (tabcount === 0) {
			if (tabindex === curTabIdx) {
				tabbtn.push(`<button class="tabbtn" id="tabpage${tabindex}" style="background-color:orange;" onclick="tabChange('tabpage${tabindex}')">${(i + 1)}～</button>`);
				page += `<div id="tabpage${tabindex}"><table>`;
			} else {
				tabbtn.push(`<button class="tabbtn" id="tabpage${tabindex}" style="background-color:white;" onclick="tabChange('tabpage${tabindex}')">${(i+1)}～</button>`);
				page += `<div id="tabpage${tabindex}" class="nodisplay"><table>`;
			}
		}
		page += disp[i];
		
		if (tabcount === 9) {
			page += `</table></div>`;

			tabpage.push(page);  
			page = '';

			tabcount = 0;
			tabindex++;
		} else {
			tabcount++;
		}
	}

	if (page.length !== 0) {
		page += `</table></div>`;
		tabpage.push(page);
	}

	document.getElementById("tabcontrol").innerHTML = tabbtn.join('');
	document.getElementById("tabbody").innerHTML = tabpage.join('');
 
	let clist = new Array();
	lastlog.forEach(elem => {
		let id = elem.id;
		// let limit_raw = get_limit(id);
		let limit_raw = elem.limit;
		let alartflag = "";
		if (limit_raw != "") {
			let limit_items = limit_raw.split(",");
			
            alartflag=limit_items[40];
			
			if(alartflag !== "0"){
				clist.push(get_name(id)+`[${id}] - 開閉エラーです。`);
				
			}
    	}
	});

	if (clist.length > 0) {
		alert(clist.join("\n"));
	}
}

// function initTable2(sgatewayid) {
// 	let lastlog = get_latest_data2(sgatewayid);
// 	if (lastlog.length == 0){
// 		return;
// 	} 

//     satime = 4;
// 	let smap = lastlog.sort((a, b) => {
// 		return a.id - b.id;
// 	});

// 	let disp = smap.map(val => {
// 		let id = val.id;
// 		let No = val.num;
// 		let state = "";
// 		let statestr = "異";
// 		let statenow = "異常";
// 		let btnstr = "無効";
// 		let orderstr = "なし";
// 		let gmode = get_mode(id);
// 		if (gmode != "") {
// 			Uplink = gmode.split(",");

// 			if (Uplink[13] == 1) {
// 				state = "open";
// 				statestr = "開";
// 				statenow = "開";
// 				btnstr = "閉める";
// 			}

// 			if (Uplink[13] == 2) {
// 				state = "close";
// 				statestr = "閉";
// 				statenow = "閉";
// 				btnstr = "開ける";
// 			}
// 	    }
// 		let blink = "";
// 		let ordera = get_limit(id);
// 		if (ordera != "") {
// 			let order = ordera.split(",");

// 			if (order[3] == 1) {
// 				blink = `class="blink"`;
// 				if (order[4] == 1) {
// 					orderstr = "開";
// 					btnstr = "閉める";
// 				} else {
// 					orderstr = "閉";
// 					btnstr = "開ける";
// 				}
// 			} else {
// 				blink = "";
// 			}
// 		}

// 		let wlevel = val.getLevel();
// 		let wtemp = val.getTemperature();
// 		let vvolt = val.getvoltage();

// 		let date = formatDate(val.getDateTime(), 'yyyy-MM-dd');
// 		let datetime = formatDate(val.getDateTime(), 'yyyy-MM-dd HH:mm:ss');
// 		let today = new Date();
// 		let uptime = new Date(datetime);
//         let satime = (today.getTime() - uptime.getTime()) / (1000 * 60 * 60);

// 		if (satime < 3) {
// 			tabCr = "#b8bcff";
// 		} else {
// 			tabCr = "#C0C0C0";
// 		}

// 		let item = `
// 		<tr id="taget${id}" onclick="opendetail('${id}')"> 
// 			<!--th>${get_name(id)}</th-->
// 			<th bgcolor=${tabCr}   height="100" align="left">${No}</th>
// 			<th bgcolor=${tabCr}    align="center" colspan="3">${val.getDateMMDD()} ${val.getTimeHHMM()}</th>
// 			<th bgcolor=${tabCr} ></th>
// 			<th bgcolor=${tabCr}    align="right">詳細へ>></th>
// 		</tr>	
// 		<tr>
// 			<td rowspan="3" align="left">
// 				<button class="state ${state}">${statestr}</button>
// 			</td>
// 			<td rowspan="3" align="center" style="${createWLevel(wlevel)}">水位<br />${wlevel}㎝</td>
// 			<td rowspan="3" align="center" style="${createWTemp(wtemp)}">水温<br />${wtemp}℃</td>
// 			<td rowspan="3" align="center" style="${createBTlevel(vvolt, val.voltstate)}">電池<br />${vvolt}V</td>
// 			<td style="text-align: right;">現在:</td>
// 			<td class="state" style="">${statenow}</td>
// 		</tr>
// 		<tr onclick="ctrl_date('${id}','${date}')">
// 			<td style="text-align: right;" ${blink}>動作中:</td>
// 			<td class="state" id="now${id}">${orderstr}</td>
// 		</tr>
// 		<tr>
// 			<td style="text-align: right;">開閉:</td>
// 			<td class="state">
// 				<button id="btn${id}" value="${id}" onclick="statebtn(this)" style="width:100%;height:100%;">${btnstr}</button>
// 			</td>
// 		</tr>
// 		`;

// 		return item;
// 	});

// 	let tabbtn = new Array();
// 	let tabpage = new Array();
// 	let tabindex = 0;
// 	let tabcount = 0;
// 	let page = '';
// 	for (let i = 0; i < disp.length; i++) {
// 		if (tabcount === 0) {
// 			//tabbtn.push(`<button class="tabbtn" id="tabpage${tabindex}" onclick="tabChange('tabpage${tabindex}')">${(i+1)}～</button>`);
// 			if (tabindex === 0) {
// 				tabbtn.push(`<button class="tabbtn" id="tabpage${tabindex}" style="background-color:orange;" onclick="tabChange('tabpage${tabindex}')">${(i+1)}～</button>`);
// 				page += `<div id="tabpage${tabindex}"><table>`;
// 			} else {
// 				tabbtn.push(`<button class="tabbtn" id="tabpage${tabindex}" style="background-color:white;" onclick="tabChange('tabpage${tabindex}')">${(i+1)}～</button>`);
// 				page += `<div id="tabpage${tabindex}" class="nodisplay"><table>`;
// 			}
// 		}
// 		page += disp[i];
		
// 		if (tabcount === 9) {
// 			page += `</table></div>`;

// 			tabpage.push(page);  
// 			page = '';

// 			tabcount = 0;
// 			tabindex++;
// 		} else {
// 			tabcount++;
// 		}
// 	}

// 	if (page.length !== 0) {
// 		page += `</table></div>`;
// 		tabpage.push(page);
// 	}

//     //tabbtn0.style.backgroundColor = "orange";
// 	document.getElementById("tabcontrol").innerHTML = tabbtn.join('');
// 	document.getElementById("tabbody").innerHTML = tabpage.join('');
// 	let clist = new Array();
// 	lastlog.forEach(elem => {
// 		let id = elem.id;
// 		let limit_raw = get_limit(id);
// 		let alartflag = "";
// 		if (limit_raw != "") {
// 			let limit_items = limit_raw.split(",");
			
//             alartflag = limit_items[40];
			
// 			if(alartflag !== "0"){
// 				clist.push(`${id} - 開閉エラーです。`);
// 			}
//     	}
// 	});
// 	if (clist.length > 0) {
// 		alert(clist.join("\n"));
// 	}	
// }

function createWTemp(val) {
	if (val == "--") {
		return "";
	}
	return create_bgcolor(val, 40, "#f5b1aa");
}

function createWLevel(val) {
	if (val == "--") {
		return "";
	}
	return create_bgcolor(val, 21.5, "aquamarine");
}

function createBTlevel(val, state) {

	let volt = val;
	let color = "#00DD00";
	if (val >= 2.8) {
		color = "#00DD00";
	} else if (val >= 2.7) {
		color = "yellow";
	} else {
		color = "red";
	}

	if (val == "--") {
		return "";
	} else {
		volt = val - 2.5;
	}

	return create_bgcolor(volt, 0.5, color);
}

function create_bgcolor(val, maxval, color) {
	let par = 100;
	if (val > 0 && val < maxval) {
		par = 100 - Math.round(val * 100 / maxval);
	} else if (val >= maxval) {
		par = 0;
	}
	return `background: linear-gradient(180deg, white ${par}%, ${color} ${par}%);`;
}

// タブボタン押下時処理
let page_index = "";
function tabChange(targetid) {
	page_index = targetid;
	let pages1 = document.getElementById('tabbody').getElementsByTagName('div');

	Array.prototype.forEach.call(pages1, function (page) {
		if (page.id == targetid) {
			page.className = '';
		} else {
			page.className = "nodisplay";
		}
	});

	let pages2 = document.querySelectorAll(".tabbtn");
	Array.prototype.forEach.call(pages2, function (page) {
		if (page.id == targetid) {
			page.style = "background-color:orange;";
		} else {
			page.style = "background-color:white;";
		}
	});
}

// 開閉ボタン押下時処理
function statebtn(obj) {
	
	if (!check_connect()) {
		alert("画面が古くなっています。ログインしなおしてください。");
		return;
	}

	let checkparmission = check_parmission(obj.value);
	if (checkparmission == "1") {
		alert("この子機は管理者に制限されています。");
		return;
	}


	if (obj.innerHTML != "開ける" && obj.innerHTML != "閉める") {
		return;
	}

	let text = obj.innerHTML == "開ける" ? "開けてよいですか" : "閉めてよいですか";
	var result = window.confirm(text);
    if (!result) {
        return;
    }
    blink = `class="blink"`;
	let state = obj.innerHTML == "開ける";
	let ctrl = state ? true : false;
	let now = state ? "開" : "閉";
	let btn = state ? "閉める" : "開ける";
	let ctrl1 = 0;

	if (ctrl) {
		ctrl1 = 1;
	} else {
		ctrl1 = 0;
	}

	// Get current tab index.
	let tabId = document.querySelector(`#btn${obj.value}`).parentNode.parentNode.parentNode.parentNode.parentNode.getAttribute("id");
	let tabIdx = parseInt(tabId.replace("tabpage", ""));

	data1 = 3 + "," + "," + "," + "," + "," + ctrl1;
	//data1=$pr , $offon , $up , $down , $time , $ctrl
    let res = set_limit( obj.value, data1);
    switch (res) {
        case "00":
            alert("正常に受け付けました。");
			document.getElementById(`now${obj.value}`).innerHTML = now;
			document.getElementById(`btn${obj.value}`).innerHTML = btn;
			initTable(tabIdx);
			break;

        case "04":
            alert("制御実行待機中です。");
            break;

		case "88":
			alert("操作権限がありません。");
			break;			

		case "99":
			alert("異常動作です。");
			break;

        default :
            alert("[code:" + res + "] 不明なエラー");
    }
}

// ヘッダー押下時
function opendetail(id) {
	let f_name = "../details/details.php?id=" + id;
    //let f_name = "../details?id=" + id;
	screen_open(f_name);
}

function ctrl_date(id, date) {
	let res = latest_ctrl(id, date);
	if (res != "") {
		let date =  new Date(res.replace(/-/g,"/"));
		alert(`命令送信日時：\n[${formatDate(date, 'yyyy/MM/dd HH:mm')}]`);
	}
}

function button_listback(userpermid) {
	
	if (userpermid == 2 ||userpermid == 3) {
		// 子機一覧を表示
		screen_transition( '../top.php');
	} else {
		//ゲート一覧を表示
		screen_transition("../gatelist/gatelist.php");
	}    
} 
