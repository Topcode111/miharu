<?php

//ダウンリンクのGWからの各種結果への反応
require_once(dirname(__FILE__).'/app/php/link.php');
require_once(dirname(__FILE__).'/app/php/lib.php');

downlink_log("\n[".date("Y/m/d H:i:s")."]Downlink.php▼▼▼\n");

$request = json_decode(file_get_contents('php://input'), true);
downlink_log("\t受信データ:".json_encode($request)."\n");

switch ($request["head1"]["kind"]) {
    case SENT_RESULT :
        // 送信結果
        downlink_log("\t送信結果\n");

        $result = soshin_kekka($request);
        downlink_log("\t送信結果:{$result}\n");

        // 送信結果応答
        $response = common_response($request, SENT_RESULT_RESPONCE, $result);
        downlink_log("\t送信結果応答:{$response}\n");

        http_response_code();
        break;

    case ACK_NOTIFICATION :
        // ACK通知
        downlink_log("\tACK通知\n");

        $response = common_response($request, ACK_NOTIFICATION_RESPONCE, "00");
        downlink_log("\tACK通知応答:{$response}\n");

        http_response_code();
        break;

    default :
        downlink_log("\tその他の通信\n");
}

downlink_log("[".date("Y/m/d H:i:s")."]▲▲▲Downlink.php▲▲▲\n");

function soshin_kekka( $req ) {

    define("NONE", "00");
    define("TOO_LATE", "01");
    define("TOO_EARLY", "02");
    define("COLLISION_PACKET", "02");
    define("COLLISION_BEACON", "04");
    define("TX_FREQ", "05");
    define("TX_POWER", "06");
    define("GPS_UNLOCKED", "07");

    $result = $req["data"];

    // 異常値が来た場合ごとの処理 未実装
    switch ($result) {
        case TOO_LATE:
            break;
        case TOO_EARLY:
            break;
        case COLLISION_PACKET:
            break;
        case COLLISION_BEACON:
            break;
        case TX_FREQ:
            break;
        case TX_POWER:
            break;
        case GPS_UNLOCKED:
            break;
    }

    return NONE;
}

?>