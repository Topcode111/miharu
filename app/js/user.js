function transaction(datas) {
	let ret = "";
	try {
		$.ajax({
			url: "./php/user.php",
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

function get_group_name() {
	let array = { func: "name" };
	return transaction(array);
}