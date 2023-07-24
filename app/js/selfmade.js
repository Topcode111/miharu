// 画面を遷移する
function screen_transition(url) {
	window.location.href = url;
}

// 画面を新しいタブで開く
function screen_open(url){
	window.open(url);
}

function back() {
	window.close();
	history.back();
}

// メールアドレスチェック
function mail_check( mail ) {
    var mail_regex1 = new RegExp( '(?:[-!#-\'*+/-9=?A-Z^-~]+\.?(?:\.[-!#-\'*+/-9=?A-Z^-~]+)*|"(?:[!#-\[\]-~]|\\\\[\x09 -~])*")@[-!#-\'*+/-9=?A-Z^-~]+(?:\.[-!#-\'*+/-9=?A-Z^-~]+)*' );
    var mail_regex2 = new RegExp( '^[^\@]+\@[^\@]+$' );
    if( mail.match( mail_regex1 ) && mail.match( mail_regex2 ) ) {
        // 全角チェック
        if( mail.match( /[^a-zA-Z0-9\!\"\#\$\%\&\'\(\)\=\~\|\-\^\\\@\[\;\:\]\,\.\/\\\<\>\?\_\`\{\+\*\} ]/ ) ) { return false; }
        // 末尾TLDチェック（〜.co,jpなどの末尾ミスチェック用）
        if( !mail.match( /\.[a-z]+$/ ) ) { return false; }
        return true;
    } else {
        return false;
    }
}

// 引数のDate型データが今日かを判定する
function checkDateToday(date) {
	let now = new Date();

	return (
		now.getDate() == date.getDate() &&
		now.getMonth() == date.getMonth() &&
		now.getFullYear() == date.getFullYear()
	);
}

// 引数のDate型データをYYYY-MM-DDの文字列へ変換する
function dateToHyphenStr(date) {
	let year = date.getFullYear();
	let month = (date.getMonth()+1).toString().padStart(2, '0');
	let day = date.getDate().toString().padStart(2, '0');
	
	return year + "-" + month + "-" + day;
}

function get_url_param() {
	let ret = [];
	// URLのパラメータを取得
	let urlParam = location.search.substring(1);
	// URLにパラメータが存在する場合
	if (urlParam) {
		// 「&」が含まれている場合は「&」で分割
		let param = urlParam.split('&');
		// パラメータを格納する用の配列を用意
		// 用意した配列にパラメータを格納
		param.forEach(elem => {
			let paramItem = elem.split('=');
			ret[paramItem[0]] = paramItem[1];
		});
	}
	return ret;
}

function sort_string(a, b){
	let maxcount = a.length < b.length ? a.length : b.length;
	for(let i = 0; i < maxcount; i++) {
		let diff = a.charCodeAt(i) - b.charCodeAt(i);
		if(diff !== 0) {
			return diff;
		}
	}
	return 0;
};

function download_csv(name, data) {

    let bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
    let blob = new Blob([ bom, data ], { 'type' : 'text/csv' });

    let downloadLink = document.createElement('a');
    downloadLink.download = name;
    downloadLink.href = URL.createObjectURL(blob);
    downloadLink.dataset.downloadurl = ['text/plain', downloadLink.download, downloadLink.href].join(':');
    downloadLink.click();
}

function padding_num(top, num) {
	return (top + num).slice(-1*top.length);
}

// date: 日付オブジェクト
// format: 書式フォーマット
function formatDate (date, format) {
	format = format.replace(/yyyy/g, date.getFullYear());
	format = format.replace(/MM/g, ('0' + (date.getMonth() + 1)).slice(-2));
	format = format.replace(/dd/g, ('0' + date.getDate()).slice(-2));
	format = format.replace(/HH/g, ('0' + date.getHours()).slice(-2));
	format = format.replace(/mm/g, ('0' + date.getMinutes()).slice(-2));
	format = format.replace(/ss/g, ('0' + date.getSeconds()).slice(-2));
	format = format.replace(/SSS/g, ('00' + date.getMilliseconds()).slice(-3));
	return format;
};

function length_limit(obj) {

	if(obj.value.length > obj.maxLength) {
		obj.value = obj.value.slice(0, obj.maxLength);
	}
}