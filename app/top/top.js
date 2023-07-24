function init999(uuidd) {

	if ((uuidd ==  0||uuidd =="") ) {
		alert("ログインしなおしてください。");
		location.href = "./logout.php";
		//window.close();
		//return;
	}

	//document.getElementById("groupname0").innerHTML = get_group_name();
 }

//function set_visibility(userpermid) {
//	if (userpermid == '1' ||userpermid == '3' ){
//	  // btn_1を非表示
//	  document.getElementById('btnmanage').style.visibility = 'visible';
//	}else{
//	  // btn_1を表示
//	  document.getElementById('btnmanage').style.visibility = 'hidden';
//	}
//  }
function button_logout() {
	let path = "./logout.php";
	screen_transition(path);
} 
  
function button_gatelist(userpermid) {
	if (userpermid == 2 ||userpermid == 3 ){
		// 子機一覧を表示
		screen_transition( './list/list.php?gwlistno=' +  encodeURIComponent(0))
		//let path = "./list/list.php";
	  }else{
		//ゲート一覧を表示
		screen_transition("./gatelist/gatelist.php");
		//let path = "./gatelist/gatelist.php";
	  }    
	//screen_transition(path);
}

function button_limitgate(userpermid) {
	//let path = "./limit/limit.php";
	if (userpermid == 2 ||userpermid == 3 ){
		// 子機一覧を表示
		screen_transition( './limit/limit.php?gwlistno=' +  encodeURIComponent(0))
		//let path = "./list/list.php";
	  }else{
		//ゲート一覧を表示
		screen_transition("./limitgatelist/limitgatelist.php");
		//let path = "./gatelist/gatelist.php";
	  }    	
	//screen_transition(path);
}

function button_settinggate(userpermid) {
	//let path = "./setting";
	if (userpermid == 2 ||userpermid == 3 ){
		// 子機一覧を表示
		screen_transition( './setting/setting.php?gwlistno=' +  encodeURIComponent(0))
		//let path = "./list/list.php";
	  }else{
		//ゲート一覧を表示
		screen_transition("./settinggatelist/settinggatelist.php");
		//let path = "./gatelist/gatelist.php";
	  }    

	//screen_transition(path);
}
//function button_password() {
//	let path = "./changepassword/pass_change.php";
//	screen_transition(path);
//}
function button_password() {
	let path = "./changepassword/pass_change.php";
	screen_transition(path);
}


function button_managegate(userpermid) {

	//let myPassWord=prompt("パスワードを入力してください","");
	//if ( myPassWord == "" ){
	//	return;
	//} else if ( myPassWord != "miharu0621" ) {
	//	alert( "パスワードが違います!" );
	//	return;
	//}
	//let path = "./setting";
	if (userpermid == 2 ||userpermid == 3 ){
		// 子機一覧を表示
		screen_transition( './manage/manage.php?gwlistno=' +  encodeURIComponent(0))
		//let path = "./list/list.php";
	  }else{
		//ゲート一覧を表示
		screen_transition("./managegatelist/managegatelist.php");
		//let path = "./gatelist/gatelist.php";
	  }    

	//screen_transition(path);	

	//let path = "./manage";
	//screen_transition(path);
}

function button_timer() {
	let path = "./timer";
	screen_transition(path);
}