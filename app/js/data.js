function transaction(datas) {
	let ret = "";
	try {
		$.ajax({
			url: "../php/data.php",
			data: datas,
			type: "POST",
			async: false
		})
		.then(
			function (msg) {
				ret = msg;
			}
		);
	} catch(e) {
	}
	return ret;
}

function check_connect() {
	let ret = "";
	try {
		$.ajax({
			url: "../php/connect.php",
			data: { func: "ck" },
			type: "POST",
			async: false
		})
		.then(
			function (msg) {
				ret = msg;
			}
		);
	} catch(e) {
	}
	return ret == "1";
}


function create_data( str ) {
	let datas = new Array();
	if (str == "") {
		return datas;
	}
	let rows = str.split("\n");
	rows.forEach(row => {
		datas.push(create_datum(row));
	});
	return datas;
}

function create_datum( str ) {
	return new Data(str);
}

function send_volt(id, volt) {
	let array = { func: "update_volt", id: id, volt: volt };
	return transaction(array) != "";
}

function get_latest_data() {
	let array = { func: "latest" };
	return create_data(transaction(array));
}

function  get_latest_data2(gwid) {
	let array = { func: "latest2", gwid: gwid };
	return create_data(transaction(array));
}

function get_latest_one(id) {
	let array = { func: "latest_one", id: id };
	let data = transaction(array);
	//return data == "" ? "" : create_datum(data);
    return data;
}

function change_cycle( id, hour, minu,pr ) {
	let array = { func: "cycle", id: id, hour: hour, minu: minu ,pr: pr };
	return transaction(array);
}

function get_mode( id ) {
	let array = { func: "get_mode", id: id };
	return transaction(array);
}
function get_name( id ) {
	let array = { func: "get_name", id: id };
	return transaction(array);
}

function check_login() {
	let array = { func: "clogin" };
	return transaction(array);
}

// function get_wvolt( id ) {
// 	let array = { func: "get_wvolt", id: id };
// 	return transaction(array);
// }

// function get_nvolt( id ) {
// 	let array = { func: "get_nvolt", id: id };
// 	return transaction(array);
// }

function get_date_dates(id) {
	let array = { func: "get_dates", id: id};
	//transaction(array).split('\n');
	return transaction(array);
}

function get_day_data( id, date) {
	let array = { func: "get_date_data", id: id, date: date };
	return create_data(transaction(array));
}

function get_week_average( id, date) {
	let array = { func: "weekave", id: id, date: date };
	return transaction(array).split("\n");
}

function get_limit( id ) {
	let array = { func: "get_limit", id: id };
	return transaction(array);
}

function set_limit( id, data ) {
	let array = { func: "set_limit", id: id, data: data };
	return transaction(array);
}

function get_setting() {
	let array = { func: "get_setting"};
	return transaction(array);
}

//function set_setting( data ) {
//	let array = { func: "set_setting", value: data };
//	return transaction(array);
//}

//function get_alertlevel() {
//	let array = { func: "get_alertlevel"};
//	return transaction(array);
//}

function set_alertlevel( data ) {
	let array = { func: "set_alertlevel", value: data };
	return transaction(array);
}

function get_alert_maillist() {
	let array = { func: "get_alert_maillist"};
	return transaction(array);
}

function set_alert_maillist( data ) {
	let array = { func: "set_alert_maillist", value: data };
	return transaction(array);
}

function get_hist_a(id, val) {
	let array = { func: "get_hist_a", id: id, value: val};
	return transaction(array);
}

function latest_ctrl( id, date ) {
	let array = { func: "latest_ctrl", id: id, value: date};
	return transaction(array);
}

function get_batch() {
	let array = { func: "get_batch" };
	return transaction(array);
}

function set_batch(data) {
	let array = { func: "set_batch", value: data };
	return transaction(array);
}

function get_childuser() {
	let array = { func: "get_children" };
	return transaction(array).split("\n");
}

function get_slaves() {
	let array = { func: "get_slaves" };
	return transaction(array).split("\n");
}

function get_parmissionenable(data) {
	let array = { func: "get_penable", value: data};
	return transaction(array).split("\n");
}

function offdslavesettime(data) {
	let array = { func: "timeoff", value: data};
	return transaction(array);
}

function set_slaveparmission(data) {
	let array = { func: "set_slaveparmission", value: data };
	return transaction(array);
}

function check_parmission(sid) {
	let array = { func: "checkparm", id: sid };
	return transaction(array);
}

function exists_parmission() {
	let array = { func: "existsparm" };
	return transaction(array);
}

function del_history(data) {
	let array = { func: "delhist", value: data };
	return transaction(array);
}

function get_slavehist() {
	let array = { func: "slavehist" };
	return transaction(array);
}

function get_oneslavehist(data) {
	let array = { func: "oneslavehist", value: data };
	return transaction(array);
}

function del_order(data) {
	let array = { func: "delorder", value: data };
	return transaction(array);
}