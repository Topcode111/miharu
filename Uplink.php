<?php

//uplinkデータをデータベース収納仕様に変更中
require_once(dirname(__FILE__).'/app/php/link.php');
require_once(dirname(__FILE__).'/app/php/loralib.php');
require_once(dirname(__FILE__).'/app/php/db.php');

require_once(dirname(__FILE__).'/app/php/lib.php');

uplink_log("▼▼▼Uplink.php▼▼▼");

$raw = file_get_contents('php://input');

$req = json_decode($raw, true);
uplink_log("\t受信データ:".json_encode($req));

switch ($req["head1"]["kind"]) {
    //uplink
    case RECEIVED_NOTIFICATION :
        // 受信通知
        uplink_log("\t受信通知");

        $result = received_notification($req);
        uplink_log("\t受信通知:{$result}");

        // 受信応答
        $rr = common_response($req, RECEIVED_RESPONCE, $result);
        uplink_log("\t受信応答:{$rr}");

        http_response_code();

        // $id = $req["head2"]["deveui"];
        // uplink_log("ID[{$id}]\n");

        break;
    //downlink
    case SENT_RESULT :
        // 送信結果
        uplink_log("\t送信結果");

        // 送信結果応答
        $rr = common_response($req, SENT_RESULT_RESPONCE, "00");
        uplink_log("\t送信結果:{$rr}");

        http_response_code();

        break;
    //downlink    
    case ACK_NOTIFICATION :
        // ACK通知
        uplink_log("\tACK通知");

        $rr = common_response($req, ACK_NOTIFICATION_RESPONCE, "00");
        uplink_log("\tACK通知:{$rr}");

        http_response_code();
        
        break;

    default :
        uplink_log("\tその他の通信");

}
uplink_log("▲▲▲Uplink.php▲▲▲\n");

//アップリンク書き込みとデータチェック
function received_notification($req) {

    define("OK", "00");
    define("ERROR_LENGTH", "01");
    define("ERROR_OTHER", "FF");

    $result = OK;
    
    $path = "";
    $csv = "";
    try {
        $items = str_split($req["data"], 2 );

        $id = $req["head2"]["deveui"];
        $gwid = $req["head1"]["gwid"];

        //子機idからゲート情報取得　
        $gwinfo = dbmgr::readdbgw($id);
        //グループid取得
        $uid = $gwinfo[0]['GW_GROUP_ID'];

        $cvolt = get_criterion_voltage($id, $items[13].$items[14], $items[23].$items[24], $uid);
        $date = date("Y-m-d");

        //$array = array();
        // 01 サーバー日付
        //$array[] = $date;
        // 02 サーバー時刻
        //$array[] = date("H:i:s");
        // 03 GW_ID
        //$array[] = $gwid;

        // 0 子機ID
        $array[] = $id;
        // 05 端末送信日付
        //$array[] = "20{$items[1]}-{$items[2]}-{$items[3]}";
        // 06 端末送信時刻
        //$array[] = "{$items[4]}:{$items[5]}:{$items[6]}";

        // 01 送信日付時刻
        $array[] = "20{$items[1]}-{$items[2]}-{$items[3]} {$items[4]}:{$items[5]}:{$items[6]}";
        uplink_log("\t今回日付20{$items[1]}-{$items[2]}-{$items[3]} {$items[4]}:{$items[5]}:{$items[6]}");
        // 07 送信設定
        $array[] = $items[7];
        // 08 装置状態
        $array[] = sprintf('%08d', decbin(hexdec($items[8])));
        // 09 ﾊﾞｯﾃﾘ電圧
        // 04 バッテリ電圧
        $array[] = sprintf('%.1f', hexdec($items[9]) / 10);
        // 10 RSSI
        // 05 RSSI
        $array[] = $items[10].$items[11];
        // 11 SNR
        // 06 SNR
        $array[] = $items[12];
        // 12 水位データ
        // 07　水位データ
        $level = get_wlevel( $items[13].$items[14], $cvolt);
        $array[] = $level;
        // 08 水温データ
        $wtemp = get_water_temp($items[15].$items[16]);
        $array[] = $wtemp;
        // 09 前回端末送信日付時刻
        $array[] = "20{$items[17]}-{$items[18]}-{$items[19]} {$items[20]}:{$items[21]}:{$items[22]}";
        uplink_log("\t前回日付20{$items[17]}-{$items[18]}-{$items[19]} {$items[20]}:{$items[21]}:{$items[22]}");
        // 10 前回水位データ
        $array[] = get_wlevel( $items[23].$items[24], $cvolt);
        // 11 前回水温データ
        $array[] = get_water_temp($items[25].$items[26]);
        // 12 制御実施時刻
        $array[] = "20{$items[27]}-{$items[28]}-{$items[29]} {$items[30]}:{$items[31]}:{$items[32]}";
        // 13 装置情報
        $state = $items[33];
        $array[] = sprintf('%01d',$state);
        // 14 水位電圧
        $array[] = get_water_volt($items[13].$items[14]);
        // 15 前回水位電圧
        $array[] = get_water_volt($items[23].$items[24]);
        // 基準電圧
        //$array[] = $cvolt;
        
        //データサーバへの書き込み
        if ($items[2] != "00" && $items[18] != "00") {
            dbmgr::writeuplinkdate($array);
        }

        //アラートチェック
        if (($wtemp != "--") || ($level != "--")) {
            all_alert($uid, $wtemp, $level, $id); 
            //温度アラート
            //temp_alert($uid, $wtemp, $id);
        }
    } catch( Exception $e ) {
        error_output($e->getMessage());
        uplink_log($e->getMessage());
        if ($result == OK) $result = ERROR_OTHER;
    }

    return $result;
}

//uid グループ　　wtemp 温度　id 子機id
function all_alert($uid, $wtemp, $level, $id) {
        //設定ファイル取得
        $alertinfo = dbmgr::readdbgroupalertlist($uid); 
        $h1_items = $alertinfo[0]['GROUP_SET_HIGH_TEMP_LIM_AL'];
        $l1_items = $alertinfo[0]['GROUP_SET_LOW_TEMP_LIM_AL'];
        $h2_items = $alertinfo[0]['GROUP_SET_UP_LIM_WATER_AL'];
        $l2_items = $alertinfo[0]['GROUP_SET_LO_LIM_WATER_AL'];
        $h12_items = $alertinfo[0]['GROUP_SET_HIGH_TEMP_LIM'];
        $l12_items = $alertinfo[0]['GROUP_SET_LOW_TEMP_LIM'];
        $h22_items = $alertinfo[0]['GROUP_SET_UP_LIM_WATER'];
        $l22_items = $alertinfo[0]['GROUP_SET_LO_LIM_WATER' ];

        //子機ナンバー取得
        //子機idから子機情報取得　
        $slaveinfo = dbmgr::readdbslaveinfo($id);
        //子機ナンバー取得
        $name = $slaveinfo[0]['SLAVE_NUMBER'];

        //温度チェック
        if ($h1_items == "1" and $h12_items != "" and (double)$wtemp >= (double)$h12_items) {
            $body = "子機[{$name}]が設定基準値[{$h12_items}℃]を超え、[{$wtemp}℃]になりました。".
            "\n\nこのメールは送信専用です。返信しないようお願いいたします。";
            send_alert_mail($uid, $body);   
        }
        if ($l1_items == "1" and $l12_items != "" and (double)$wtemp <= (double)$l12_items) {
            $body = "子機[{$name}]が設定基準値[{$l12_items}℃]を下回り、[{$wtemp}℃]になりました。".
            "\n\nこのメールは送信専用です。返信しないようお願いいたします。";
            send_alert_mail($uid, $body);   
        }
        //水位チェック
        if ($h2_items == "1" and $h22_items != "" and (double)$level >= (double)$h22_items) {
            $body = "子機[{$name}]が設定基準値[{$h22_items}cm]を超え、[{$level}cm]になりました。".
            "\n\nこのメールは送信専用です。返信しないようお願いいたします。";
            send_alert_mail($uid, $body); 
        }
        if ($l2_items == "1" and $l22_items != "" and (double)$level <= (double)$l22_items) {
            $body = "子機[{$name}]が設定基準値[{$l22_items}cm]を下回り、[{$level}cm]になりました。".
            "\n\nこのメールは送信専用です。返信しないようお願いいたします。";
            send_alert_mail($uid, $body);
        }

}

function send_alert_mail($uid, $body) {

    //メールアドレス取得
    $maillist = dbmgr::readdbgroupmaillist($uid);

    mb_language("Japanese"); 
    mb_internal_encoding("UTF-8");
    $email = "noreply@alert.miharu-cloud.net";
    //$email = "miyakuri242@gmail.com";
    $subject = "MIHARU アラート";
    $header = "From: {$email}\nReply-To: {$email}\n";
    $header = "From: {$email}";
    if (count($maillist) !== 0 ) {
        $arraylist = array_column($maillist, "GROUP_MAIL_AD");
        foreach ($arraylist as $dates) {
            $send_res = mb_send_mail(trim($dates), $subject, $body, $header);
            uplink_log("\t{$send_res}:".trim($dates));
        }
    }
}


function get_water_volt($value) {
    if ($value == "ffff") {
        return "99.9";
    }
    return hexdec($value);
}

function get_water_temp($value) {
    if ($value == "07ff") {
        return "99.9";
    }
    if ($value == "--") {
        return "99.9";
    }
    $tempval = hex_to_decimal($value);
    //return sprintf('%.1f', hexdec($value)*0.0625);
    return sprintf('%.1f', $tempval*0.0625);
}

//16進数を符号付き10進数に変換する
function hex_to_decimal($val)
{
    $hex = $val;
    //0x表記の場合
    if (strpos($val,"0x") === 0) {
        //「0x」の箇所を削除する
        $hex = substr($val, 2);
    }

    //先頭文字の1ビット目が1→マイナス
    if (hexdec(substr($hex, 0, 1)) == 15) {
        //2の補数で絶対値を取得
        $hex = (hexdec($hex) ^ (16 ** strlen($hex) - 1)) + 1;
        //マイナスに変換
        $dec = 0 - $hex;
    } else {
        //先頭文字の1ビット目が0→プラス
        $dec = hexdec($hex);
    }
    return $dec;
}

//基準電圧取得
function get_criterion_voltage($id, $volt, $prevvolt, $uid) {

    $res = "";

    //基準電圧を取得
    //基準電圧がないか前回データがなければ今回データを基準電圧に設定しデータサーバーに書き込み
    $result = dbmgr::readSlaveStandard($id);
    if (count($result) <= 0) {
        dbmgr::writeSlaveStandard($id, hexdec($volt));
        $res = hexdec($volt);
    } else {
        $res = $result[0]["SLAVE_SET_STADARD"];
    }
    return $res;
}

function get_wlevel( $volt, $cvolt ) {
    $res = 99.9;
    if ($volt == "ffff" or $cvolt == "") {
        return $res;
    }

    $wvolt = hexdec($volt);

    $wlevel = $wvolt - (int)$cvolt;
    if ($wlevel <= 0) {
        $res = 0;
    } else if ($wlevel <= 4) {
        $res = 0;
    } else if ($wlevel <= 14) {
        $res = 3.5;
    } else if ($wlevel <= 24) {
        $res = 4;
    } else if ($wlevel <= 35) {
        $res = 4.5;
    } else if ($wlevel <= 45) {
        $res = 5;
    } else if ($wlevel <= 56) {
        $res = 5.5;
    } else if ($wlevel <= 66) {
        $res = 6;
    } else if ($wlevel <= 77) {
        $res = 6.5;
    } else if ($wlevel <= 89) {
        $res = 7;
    } else if ($wlevel <= 99) {
        $res = 7.5;
    } else if ($wlevel <= 110) {
        $res = 8;
    } else if ($wlevel <= 121) {
        $res = 8.5;
    } else if ($wlevel <= 132) {
        $res = 9;
    } else if ($wlevel <= 143) {
        $res = 9.5;
    } else if ($wlevel <= 154) {
        $res = 10;
    } else if ($wlevel <= 165) {
        $res = 10.5;
    } else if ($wlevel <= 176) {
        $res = 11;
    } else if ($wlevel <= 188) {
        $res = 11.5;
    } else if ($wlevel <= 199) {
        $res = 12;
    } else if ($wlevel <= 210) {
        $res = 12.5;
    } else if ($wlevel <= 223) {
        $res = 13;
    } else if ($wlevel <= 234) {
        $res = 13.5;
    } else if ($wlevel <= 245) {
        $res = 14;
    } else if ($wlevel <= 256) {
        $res = 14.5;
    } else if ($wlevel <= 269) {
        $res = 15;
    } else if ($wlevel <= 280) {
        $res = 15.5;
    } else if ($wlevel <= 293) {
        $res = 16;
    } else if ($wlevel <= 304) {
        $res = 16.5;
    } else if ($wlevel <= 316) {
        $res = 17;
    } else if ($wlevel <= 328) {
        $res = 17.5;
    } else if ($wlevel <= 340) {
        $res = 18;
    } else if ($wlevel <= 351) {
        $res = 18.5;
    } else if ($wlevel <= 364) {
        $res = 19;
    } else if ($wlevel <= 376) {
        $res = 19.5;
    } else if ($wlevel <= 389) {
        $res = 20;
    } else if ($wlevel <= 401) {
        $res = 20.5;
    } else if ($wlevel <= 414) {
        $res = 21;
    } else {
        $res = 21.5;
    }
    return $res;
}

?>