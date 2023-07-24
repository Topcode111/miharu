<?php
require_once(dirname(__FILE__).'/app/php/db.php');
require_once(dirname(__FILE__).'/app/php/lib.php');

switch ($_POST["func"]) {
    case "apisdata":
        $id = $_POST["id"];
        echo get_slavedata($id);
        break;

    case "apisdatas":
        $data = $_POST["val"];
        $ids = preg_split('/,/', $data);
        echo get_slavesdata($ids);
        break;

    case "apisctrl":
        $id = $_POST["id"];
        $data = $_POST["val"];
        api_log($id);
        api_log($data);
        $res = set_ocstate($id, $data);
        echo $res;
        break;
    case "apiparm":
        $id = $_POST["id"];
        echo check_webparmission($id);
        break;
}

//子機一台の最新データ取得
function get_slavedata($id) {
    $order = "";
    $updatalist = dbmgr::read_slave_data($id);
    if (count($updatalist) > 0 ) {
        $arraylist = $updatalist[0];
        $order = implode(",", $arraylist);
    }
    return $order;
}

function get_slavesdata($ids) {
    $array = array();

    foreach($ids as $id) {
        if ($id != "") {
            $array[] = get_slavedata($id);
        } else {
            $array[] = "";
        }
     }
    
    return join("\n", $array);
}

function set_ocstate($id, $data) {
    //すぐに命令を出しているのをデータベースにセットするだけに変更
    $dataarry = explode(",", $data);
    $pr =     $dataarry[0];
    //設定onoff
    $onoff =  $dataarry[1];
    $up =     $dataarry[2];
    $down =   $dataarry[3];
    $time =   $dataarry[4];
    //開閉　0：閉める、1:開ける
    $ctrl =   $dataarry[5];
    $period = $dataarry[6];
    $span =  $dataarry[7];

    $lastset = dbmgr::readdbslavesetlog($id);
    if (! empty($lastset)) {
        if ($pr > 3) {
            //優先順位３より低いものは入力される毎バージョンアップ
            switch ($pr) {
                case 4:
                    dbmgr::upvdbslavesetlog4($id,$pr,$onoff,$time,$ctrl, $period, $span);
                    $res = '00';
                    return $res;
                case 5:
                    dbmgr::upvdbslavesetlog5($id,$pr,$onoff,$time,$ctrl, $period, $span);
                    $res = '00';
                    return $res;
                case 6: 
                    dbmgr::upvdbslavesetlog6($id,$onoff,$pr,$up,$down,$time);
                    $res = '00';
                    return $res;
                case 7: 
                    dbmgr::upvdbslavesetlog7($id,$onoff,$pr,$up,$down,$time);
                    $res = '00';
                    return $res;
            }

        } else {
            //優先順位３より高い物 
            //時間設定をすべてOFFにする。
            dbmgr::upenddbslavesetlogtimeoff($id);
            if ($lastset[0]['SLAVE_SET_END'] == 0) {
                //時間バージョンアップ-完了を1、正常通知  
                dbmgr::upenddbslavesetlog($id, $ctrl, $pr);
                $result = "00"; 
            } else {
                if ($lastset[0]['SLAVE_SET_ORDER_NO'] > 2) {
                    //バージョンアップなし、異常通知 
                    $result = "99";
                } elseif ($lastset[0]['SLAVE_SET_ORDER_NO'] > 0) {
                    //バージョンアップなし、待機中通知
                    $result = "04";
                } elseif ($lastset[0]['SLAVE_SET_ORDER_NO'] == 0) {
                    //現在の優先順位と新命令の優先順位を比較
                    $lpr = $lastset[0]['SLAVE_SET_PR'];
                    if ($pr <= $lpr) {
                        //時間バージョンアップ-優先順位を変更し、正常通知
                        dbmgr::upprddbslavesetlog($id, $pr, $ctrl);
                        $result = "00";
                    } elseif ($pr > $lpr) {
                        //バージョンアップなし、待機中通知
                        $result = "04";
                    }
                 }
            }
            return $result;
        }
    } else {

        switch ($pr) {
            case 1:
                dbmgr::writedbslavesetlog1($id, $pr, $ctrl);
                $res = '00';
                return $res;
            case 2:
                dbmgr::writedbslavesetlog1($id, $pr, $ctrl);
                $res = '00';
                return $res;
            case 3:
                dbmgr::writedbslavesetlog1($id, $pr, $ctrl);
                $res = '00';
                return $res;
            case 4:
                dbmgr::writedbslavesetlog4($id, $pr, $onoff, $time, $ctrl, $period, $span);
                $res = '00';
                return $res;
            case 5: 
                dbmgr::writedbslavesetlog5($id, $pr, $onoff, $time, $ctrl, $period, $span);
                $res = '00';
                return $res;
            case 6:
                dbmgr::writedbslavesetlog6($id, $pr, $onoff, $up, $down, $time);
                $res = '00';
                return $res;
            case 7:
                dbmgr::writedbslavesetlog7($id, $pr, $onoff, $up, $down, $time);
                $res = '00';
                return $res;
        }
    }
}

function check_webparmission($sid) {

    $data = dbmgr::checkparmission($sid);
    if (count($data) > 0) {
        $ret = $data[0];
    } else {
        return $ret;
    }

    return implode(",",$ret);
}


?>