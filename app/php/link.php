<?php

// Uplink用 kindID
define("RECEIVED_NOTIFICATION", "01");      // 受信通知
define("RECEPTION_RESULT", "02");           // 受付結果
define("SENT_RESULT", "03");                // 送信結果
define("ACK_NOTIFICATION", "04");           // ACK通知
// Downlink用 kindID
define("RECEIVED_RESPONCE", "81");          // 受信応答
define("REQUEST_SEND", "82");               // 送信要求
define("SENT_RESULT_RESPONCE", "83");       // 送信結果応答
define("ACK_NOTIFICATION_RESPONCE", "84");  // ACK通知応答

// 汎用応答処理
function common_response($req, $kind, $data) {

    $head1 = $req["head1"];
    $head2 = $req["head2"];

    $array = array(
        "head1" => array(
            "kind" => $kind,
            "appeui"=> $head1["appeui"],
            "gwid"=> $head1["gwid"],
            "seqno"=> $head1["seqno"]
        ),
        "head2" => array(
            "time"=> date(DATE_W3C),
            "deveui"=> $head2["deveui"],
            "devaddr"=> $head2["devaddr"],
            "fport"=> $head2["fport"],     
            "pkttoken"=> $head2["pkttoken"],
            "fmtver"=> $head2["fmtver"],
            "lendt"=> 1
        ),
        "data"=> $data
    );
    header('Content-Type: application/json');
    echo json_encode($array);
    // $rr = "  ok" . "   ". $head2["deveui"]."  ". $kind;
    $rr = "OK " . $head2["deveui"]." {$kind}";
    return $rr; 
}

?>