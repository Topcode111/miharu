function init994(uuidd) {
	if ((uuidd ==  0||uuidd =="") ) {
		alert("ログインしなおしてください。");
		location.href = "../logout.php";
		//window.close();
		//return;
	}

	//document.getElementById("groupname0").innerHTML = get_group_name();
 }
function update() {
	initTable(0);
	if (page_index != "") {
		tabChange(page_index);
	}
	alert("画面を更新しました。");
}

function button_gatw(gateno) {
location.href = '../manage/manage.php?gwlistno=' +  encodeURIComponent(gateno);
}
  
//function button_gatw1() {
//	let path = "./gatelist";
//	screen_transition(path);
//}

function button_topmenu() {
	let path = "../top.php";
	screen_transition(path);
}

//function button_setting() {
//	let path = "./setting";
//	screen_transition(path);
//}
//function button_password() {
//	let path = "./setpassword";
//	screen_transition(path);
//}



//function button_manage() {

	//let myPassWord=prompt("パスワードを入力してください","");
	//if ( myPassWord == "" ){
	//	return;
	//} else if ( myPassWord != "miharu0621" ) {
	//	alert( "パスワードが違います!" );
	//	return;
	//}

//	let path = "./manage";
//	screen_transition(path);
//}

//function button_timer() {
//	let path = "./timer";
//	screen_transition(path);
//}