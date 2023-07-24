<?php
if(!isset($_SESSION)){
    session_start();
}

define("DOWNLINK_FORMAT", "0b%02d%02d%02d%02d%02d%02d%02d%02d%02d");
define("UPLINK_FORMAT", "05%02d%02d%02d%02d%02d%02d%02x%02x%02x");

function create_downlink_data($ctrl, $hour, $minu) {
    return sprintf(DOWNLINK_FORMAT, date("y"), date("m"), date("d"), date("h"), date("i"), date("s"), $ctrl, $hour, $minu);
}

function send_downlink($seq,$data,$id,$gwinfo) {

    $gwid=$gwinfo[0]['GW_ID'];

    $array = array(
        "head1" => array(
            "kind" => "82",
            "appeui"=> "d84a870000000001",
            "gwid"=> $gwid,
            "seqno"=> $seq
        ),
        "head2" => array(
            "time"=> date(DATE_W3C),
            "deveui"=> $id,
            "fport"=> 29,
            "fcntdn"=> 0,
            "mtype"=> 3,
            "major"=> 3,
            "pkttoken"=> rand(0, 65535),
            "ctrlbit"=> 0,
            "fmtver"=> 1,
            "lendt"=> 10
        ),
        "data"=> $data
    );

    $res = transaction( json_encode( $array ),$gwinfo );
    //return $res;
    if ($res == "") {
        return $res;
    }
    $res_array = json_decode($res, true);
    $res1 = "82  {$res}  {$data}";
    return $res1;
}


function transaction($json, $gwinfo) {

    cron_log("▼▼▼lodalib.php transaction▼▼▼");

    $gwip=$gwinfo[0]['GW_IP_AD'];
    $gwport=$gwinfo[0]['GW_DOWN_PORT'];

    $option = [];
    $option['http'] = [];
    $option['http']['ignore_errors'] = true;
    $option['http']['method'] = 'POST';
    $option['http']['header'] = "Content-Type: application/json\r\n" .
                                 "Accept: application/json\r\n";
    $option['http']['content'] = $json;                          
    $option['ssl'] = [];
    $option['ssl']['verify_peer'] = false;
    $option['ssl']['verify_peer_name'] = false;
    $context  = stream_context_create($option);
    $filename="https://{$gwip}:{$gwport}/Downlink";
    //return file_get_contents($filename, false, $context);
    $file=file_get_contents($filename, false, $context);
    //var_dump($http_response_header);
    //echo $file;
    $res = http_response_code()." {$filename}";

    cron_log("▲▲▲lodalib.php transaction▲▲▲\n");

    return $res;
}

?>