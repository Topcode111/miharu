var Data = function (str) {
    // 00 2020-09-20,       サーバ日付
    // 01 15:57:41,         サーバ時刻
    // 02 d84a87fffefedf7f, GW_ID
    // 03 d84a87fffefee4ee, 子機ID
    // 04 2032-7-23,        送信日付
    // 05 23:84:84,         送信時刻
    // 06 0c,               送信設定
    // 07 140,              装置状態
    // 08 4.9,              バッテリ電圧
    // 09 65514,            RSSI
    // 10 7,                SNR
    // 11 50,               水位データ
    // 12 25.0,             水温データ
    // 13 2032-7-23,        前回送信日付
    // 14 23:36:84,         前回送信時刻
    // 15 45,               前回水位データ
    // 16.1,                前回水温データ
    // 17 2032-7-23,        制御実施日付
    // 18 22:51:21,         制御実施時刻
    // 19 02                制御情報
    // 20 100               GWナンバー
    // 21 107               子機ナンバー
    // 22 4332              水位電圧
    // 23 4332              前回水位電圧
    // 24 4332              基準電圧

    //00 子機ID
    //01 GWID
    //02 子機番号
    //03 日時
    //04 バッテリー電圧
    //05 水位
    //06 水温
    //07 開閉状態
    //08 基準電圧
    //09 前回水位
    //10 水位電圧
    //11 前回水位電圧
    //12 機器状態

    let obj = JSON.parse(str);
    let latestData = obj['latest_data'];
    let mode = obj['mode'];
    let limit = obj['limit'];
    let name = obj['name'];

    // let items = str.split(",");
    let items = latestData.split(",");

    // 00子機ID
    this.id = items[0];
    // 01GW_ID
    this.gwid = items[1]
    // 02子機ナンバー
    this.num = parseInt(items[2]);

    // 01日付 Date[yyyy/mm/dd]
    this.date = items[3].substr( 0, 10 );
    // 02時間 Time[hh:mm:ss]
    this.time = items[3].substr( -8 );

    // 05センサー電源電圧
    this.voltage = parseFloat(items[4]).toFixed(1);
    // 06水位
    //this.level = items[11];
    this.level = items[5];
    // 07水温
    this.temperature = items[6]
    // 08バルブ状態 “a” or “b”
    this.state = parseInt(items[7]);
    // 13基準水位電圧
    this.cwvolt = items[8];
    // 11水位電圧
    this.wvolt = items[10];
    // 12前回水位電圧
    this.pwvolt = items[11];
    // 13
    this.mode = mode;
    // 14
    this.limit = limit;
    // 15
    this.name = name;

    let info = items[12];
    this.voltstate = info.slice(info.length - 2, info.length - 1);

   
}

Data.prototype.getDateMMDD = function () {
    return this.date.substr(5);
};

Data.prototype.getTimeHHMM = function () {
    return this.time.substr(0, 5);
};

Data.prototype.isOpen = function () {
    return this.state == 1;
};

Data.prototype.isClose = function () {
    return this.state == 2;
};

Data.prototype.getDateTime = function () {
    let day = this.date.replace(/-/g,"/");
    return new Date(day + " " + this.time);
};

Data.prototype.getLevel = function () {

    return (this.level == "--") ? "--" : parseFloat(this.level).toFixed(1);
};

Data.prototype.getLevel2 = function () {

    if (this.wvolt == "--"){
        return this.wvolt;
    }

    if (this.cwvolt == ""){
        return "??";
    }

    let diff = this.wvolt - parseInt(this.cwvolt);

    // if (diff < 0) {
    //     return "???";
    // }

    let res = 0;
    if (diff <= 0) {
        res = 0;
    } else if (diff <= 4) {
        //res = 3;
        res = 0;
    } else if (diff <= 14) {
        res = 3.5;
    } else if (diff <= 24) {
        res = 4;
    } else if (diff <= 35) {
        res = 4.5;
    } else if (diff <= 45) {
        res = 5;
    } else if (diff <= 56) {
        res = 5.5;
    } else if (diff <= 66) {
        res = 6;
    } else if (diff <= 77) {
        res = 6.5;
    } else if (diff <= 89) {
        res = 7;
    } else if (diff <= 99) {
        res = 7.5;
    } else if (diff <= 110) {
        res = 8;
    } else if (diff <= 121) {
        res = 8.5;
    } else if (diff <= 132) {
        res = 9;
    } else if (diff <= 143) {
        res = 9.5;
    } else if (diff <= 154) {
        res = 10;
    } else if (diff <= 165) {
        res = 10.5;
    } else if (diff <= 176) {
        res = 11;
    } else if (diff <= 188) {
        res = 11.5;
    } else if (diff <= 199) {
        res = 12;
    } else if (diff <= 210) {
        res = 12.5;
    } else if (diff <= 223) {
        res = 13;
    } else if (diff <= 234) {
        res = 13.5;
    } else if (diff <= 245) {
        res = 14;
    } else if (diff <= 256) {
        res = 14.5;
    } else if (diff <= 269) {
        res = 15;
    } else if (diff <= 280) {
        res = 15.5;
    } else if (diff <= 293) {
        res = 16;
    } else if (diff <= 304) {
        res = 16.5;
    } else if (diff <= 316) {
        res = 17;
    } else if (diff <= 328) {
        res = 17.5;
    } else if (diff <= 340) {
        res = 18;
    } else if (diff <= 351) {
        res = 18.5;
    } else if (diff <= 364) {
        res = 19;
    } else if (diff <= 376) {
        res = 19.5;
    } else if (diff <= 389) {
        res = 20;
    } else if (diff <= 401) {
        res = 20.5;
    } else if (diff <= 414) {
        res = 21;
    } else {
        res = 21.5;
    }

    return res;
};

Data.prototype.getLevel_unit = function () {
    return this.getLevel2() + "㎝";
};

Data.prototype.getTemperature = function () {
    if (this.temperature == "--") {
        return this.temperature;
    }
    let temp = parseFloat(this.temperature);
    if (temp < -55 || temp > 125) {
        return "--";
    }
    return temp.toFixed(1);
    
};
Data.prototype.getvoltage = function () {
    if (this.voltage == "--") {
        return this.voltage;
    }
    let vvolt = parseFloat(this.voltage);
    if (vvolt < -0 || vvolt > 3.5) {
        return "--";
    }
    return vvolt.toFixed(1);
    
};

Data.prototype.getTemp_unit = function () {
    return this.getTemperature() + "℃";
};