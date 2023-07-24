<?php
require_once(dirname(__FILE__).'/lib.php');
//ini_set('display_errors',1);
if (!isset($_SESSION)) {
    session_start();
}

$uid = $_SESSION['userid'];
$usergroupid = $_SESSION['usergroupid'];
$sgroupid =$_SESSION['sgroupid'];
$gwid = $_SESSION['gwid'];
$gwip = $_SESSION['gwip'];
//$gwport = $_SESSION['gwport'];

require_once(dirname(__FILE__).'/../php/db.php');
//$id="d84a87fffefe03ca";
//get_ctrl_log_latest($uid, $id, $date);
//$sgroupid="005";
//set_alert_maillist($uid, $value,$sgroupid);

switch ($_POST["func"]) {
    case "latest_one" :
        $id = $_POST["id"];
        echo get_latest_one($id);
        break;
    case "latest" :
        //$uid = $_COOKIE["userID"];
        $gwid = $_SESSION['gwid'];
        //$gwid = "d84a87fffefee348";
        echo get_latest_data($gwid);
        break;
    case "latest_sort_by_id":
        $gwid = $_SESSION['gwid'];
        echo get_latest_data_sort_by_id($gwid);
        break;
    case "latest2":
        //$uid = $_COOKIE["userID"];
        $gwid = $_POST['gwid'];
        //$gwid = "d84a87fffefee348";
        echo get_latest_data($gwid);
        break;     
    case "ctrl" :
        $id = $_POST["id"];
        echo change_state($id);
        break;
    case "cycle" :
        //周期変更
        $id = $_POST["id"];
        $pr = $_POST["pr"];

        echo change_cycle($id,$pr);
        break;
    case "get_mode" :
        $id = $_POST["id"];
        echo  getmode($id);
        break;
    case "get_name" :
        $id = $_POST["id"];
        echo get_name($id);
        break;
    case "update_volt" :
        $id = $_POST["id"];
        $wvolt = $_POST["volt"];
        echo update_volt($id, $wvolt);
        break;
    case "get_dates" :
        //$id="d84a87fffefe03ca";
        $id = $_POST["id"];
        echo get_data_dates($id);
        break;
    case "get_date_data" :
        $id = $_POST['id'];

        $date = $_POST["date"];

        echo get_day_data($id, $date);
       break;
    case "weekave" :
    
        echo get_week_average();
        break;
    case "get_limit" :
    
        $uid = $_SESSION['userid'];
        $id = $_POST['id'];
        echo get_limit($uid, $id);
        break;
    case "set_limit" :

        $permid = $_SESSION['permid'];
        if($permid == 2) {
            echo "88";
            break;  
        }

        $uid = $_SESSION['userid'];
        $id = $_POST['id'];
        $data = $_POST["data"];
    
        echo set_limit($uid, $id, $data);
        break;
    
    //警報
    case "get_setting" :
        //$uid = $_COOKIE["userID"];
        $uid = $_SESSION['userid'];
        echo get_setting($sgroupid);
        break;

    case "set_alertlevel" :
        //$uid = $_COOKIE["userID"];
        //$usergroupid= $_SESSION['usergroupid'];
        $uid = $_SESSION['userid'];
        //value=true,10
        $value = $_POST['value'];
        echo set_alertlevel($uid, $value, $sgroupid);
        break;

    case "set_alert_maillist" :
        //$uid = $_COOKIE["userID"];
        $uid = $_SESSION['userid'];
        $value = $_POST['value'];
        $usergroupid = $_SESSION['usergroupid'];
        echo set_alert_maillist($uid, $value, $sgroupid);
        break;
    case "get_alert_maillist" :
        //$uid = $_COOKIE["userID"];
        $usergroupid = $_SESSION['usergroupid'];
        echo get_alert_maillist($sgroupid);
        break;
    case "get_hist_a" :
        //$uid = $_COOKIE["userID"];
        $uid = $_SESSION['userid'];
        $id = $_POST['id'];
        $value = $_POST['value'];
        echo get_hist_a($uid, $id, $value);
        break;
    case "latest_ctrl" :
        //$uid = $_COOKIE["userID"];
        $uid = $_SESSION['userid'];
        $id = $_POST['id'];
        $date = $_POST['value'];
        echo get_ctrl_log_latest($id);
        break;
    case "get_batch" :
        $id = $_SESSION['usergroupid'];
        echo get_batch($id);
        break;
    case "set_batch" :
        $id = $_SESSION['usergroupid'];
        $uid = $_SESSION['userid'];
        $value = $_POST['value'];
        echo set_batch($id, $uid, $value);
        break;

    case "get_children":
        echo get_childuser($sgroupid);
        break;
    case "get_slaves":
        $gwid = $_SESSION['gwid'];
        echo get_slaves($gwid);
        break;
    case "get_slaves":
        $gwid = $_SESSION['gwid'];
        echo get_parmissioninfo($sid);
        break;
    case "get_penable":
        $value = $_POST['value'];
        echo get_parmissionenable($value);
        break;
    case "timeoff":
        $value = $_POST['value'];
        echo off_slavesetlogtime($value);
        break;

    case "set_slaveparmission" :
        $uid = $_SESSION['userid'];
        $gid = $_SESSION['sgroupid'];
        $value = $_POST['value'];
        
        echo set_slaveparmission($uid, $gid, $value);
        break;

    case "checkparm" :
        $permid = $_SESSION['permid'];
        $sid = $_POST['id'];
        echo check_parmission($permid, $sid);
        break;

    case "existsparm" :
        $permid = $_SESSION['permid'];
        $gid = $_SESSION['sgroupid'];
        $sid = $_POST['id'];
        echo exists_parmission($permid, $gid, $sid);
        break;

    case "clogin" :
        // $uid = $_SESSION['userid'];
        // echo $uid;

        $uuidd = 0;
		if (!isset($_SESSION)) {
			session_start();
		}
		$auth = $_SESSION['auth'] ;
		$userid = $_SESSION['userid'];
		$username = $_SESSION['username'];
		$permid = $_SESSION['permid'] ;
		$usergroupid = $_SESSION['usergroupid'] ;
		$secrndnum = $_SESSION['secrndnum'];
		$secrndnumcarry = dbmgr::readcsrf($userid);
		$nowdatetime = date("Y/m/d H:i:s");
		$keikatime = strtotime($nowdatetime) - strtotime($secrndnumcarry["0"]["CSRF_DATE"]);
		$secrndnumc = $secrndnumcarry["0"]["CSRF_CD"];
		if (($secrndnum = $secrndnumc) && ($auth == 1) && ($keikatime < 4000)) {
			$uuidd = 1;
		}
        echo $uuidd;
        break;

    case "delhist" :
        $value = $_POST['value'];
        dbmgr::deleteslaveupdata($value);
        dbmgr::deleteslavedowndata($value);
        dbmgr::deleteslavestandardvlog($value);
        dbmgr::deletelastuplinkdate($value);
        dbmgr::deleteslaveoclog($value);
        dbmgr::deleteslaveparmission($value);
        dbmgr::deleteslavepisetlog($value);
        dbmgr::deleteslavesetlog($value);
        echo "00";
        break;

    case "delorder" :
        $value = $_POST['value'];
        dbmgr::deleteslavesetlog($value);
        echo "00";
        break;

    case "slavehist":
        $gwid = $_SESSION['gwid'];
        echo get_slavehist($gwid);
        break;
    case "oneslavehist":
        $value = $_POST['value'];
        echo get_oneslavehist($value);
        break;

}

function get_hist_a($uid, $id, $period)
{
    $days = explode(",", $period);
    $date1 = $days[0];
    $date2 = $days[1];
    $date3 = new DateTime($date2);
    $date3->modify('+1 day');
    $date4=$date3->format('Y-m-d');

    $slaveoclist = dbmgr::readdbslaveoc($id, $date1, $date4);
    if (count($slaveoclist) !== 0 ) {
        $arraylist=array_column($slaveoclist, 'SLAVE_OC_AT');
        for($i = 0; $i < count($arraylist); $i++) {
          $array[] = implode(",",$slaveoclist[$i]);
        }
    }
    return join("\n", $array);
}


function get_limit($uid, $id) {
    $updatalist = dbmgr::readdbslavesetlog($id);

    $order = "";
    if (count($updatalist) !== 0 ) {
        $arraylist=$updatalist[0];
        $order = implode(",",$arraylist);
    }
    return $order;
}

//警報
function get_setting($sgroupid) {

    $data = "";

    $alertlist = dbmgr::readdbgroupalertlist($sgroupid);
    if (!count($alertlist) == 0 ) {
        $data = $alertlist[0]["GROUP_SET_HIGH_TEMP_LIM_AL"].",".$alertlist[0]["GROUP_SET_HIGH_TEMP_LIM"].",".
                $alertlist[0]["GROUP_SET_LOW_TEMP_LIM_AL"].",".$alertlist[0]["GROUP_SET_LOW_TEMP_LIM"].",".
                $alertlist[0]["GROUP_SET_UP_LIM_WATER_AL"].",".$alertlist[0]["GROUP_SET_UP_LIM_WATER"].",".
                $alertlist[0]["GROUP_SET_LO_LIM_WATER_AL"].",".$alertlist[0]["GROUP_SET_LO_LIM_WATER"];
    }

    return $data;
}

//水位と水温を一括セッティング
//データベース化済
function set_alertlevel($uid, $value, $sgroupid) {
    $id = $sgroupid; 
    $setat = date("Y/m/d H:i:s");
    $values = preg_split('/,/', $value);
    // dbmgr::writedbgroupmaillist($id,$uid,$value,$setat);
    dbmgr::write_db_group_alert_level($id, $uid, $values, $setat);
    return "00";
}

//アラート用メールアドレスを設定
function set_alert_maillist($uid, $value,$sgroupid) {
    //$valueはカンマ区切りで来る予定

    $setat = date("Y/m/d H:i:s");
    $values = preg_split('/,/', $value);
    $id = $sgroupid;
    //グループメイルリストをいったん消す
    dbmgr::deletedbgroupmaillist($id);

    //グループメイルリストを作成する
    foreach($values as $str) {
        if ($str != "") {
            dbmgr::writedbgroupmaillist($id,$uid,$str,$setat);
        }
    }
    return "00";
}

//db化済
function get_alert_maillist($sgroupid) {

    $maillist = dbmgr::readdbgroupmaillist($sgroupid);
    if (count($maillist) !== 0 ) {
        $arraylist = "";
        $data = "";
        $arraylist = array_column($maillist, "GROUP_MAIL_AD");
        foreach($arraylist as $dates) {
           $data = $data .",". $dates;
        }
        $data = substr($data , 1 , strlen($data)-1 );
        //$data = trim($maillist[][GROUP_MAIL_AD]);
       //$data = $maillist;
    }
    return $data;
}

//子機一台の最新データ
//db化済
function get_latest_one($id) {
    $order = "";
    $updatalist = dbmgr::readlastuplinkdata($id);
    if (count($updatalist) !== 0 ) {
        $arraylist = $updatalist[0];
        $order = implode(",", $arraylist);
    }
    return $order;
}
//ゲート内子機の最新データたぶん追加必要

//並び換え等が必要かも
//db化済
function get_latest_data($gwid) {
    $array = array();

    //ゲート内子機の最新アップリンクデータ一時刻覧
    //$updatalist = dbmgr::readdbgwslaveuplastlink($gwid);
    //ゲート内子機情報取得
    $slavelist = dbmgr::readdbgwslave($gwid);
    // $slavelist = dbmgr::readdbgwslaveSortbyId($gwid);
    if (count($slavelist) !== 0) {
        $arraylist = array_column($slavelist, 'SLAVE_ID');
        for($i = 0; $i < count($arraylist); $i++) {
          $id = $slavelist[$i]['SLAVE_ID'];
          //$lasttaime= $updatalist[$i][max(SLAVEU_DATE)] ; 
          //GW内子機IDと最新アップリンク時刻よりアップリンクリスト取得
          $uplinklist = dbmgr::readlastuplinkdatalist3($id);
          $array[] = implode(",", $uplinklist[0]);

        }
    }
    return join("\n", $array);
}

//並び換え等が必要かも
//db化済
// SacredDevKing
function get_latest_data_sort_by_id($gwid)
{
    $array = array();

    //ゲート内子機の最新アップリンクデータ一時刻覧
    //$updatalist = dbmgr::readdbgwslaveuplastlink($gwid);
    //ゲート内子機情報取得
     $slavelist = dbmgr::readdbgwslave($gwid);
    // $slavelist = dbmgr::readdbgwslaveSortbyId($gwid);
    if (count($slavelist) !== 0) {
        $arraylist = array_column($slavelist, 'SLAVE_ID');
        for ($i = 0; $i < count($arraylist); $i++) {
            $id = $slavelist[$i]['SLAVE_ID'];
            //$lasttaime= $updatalist[$i][max(SLAVEU_DATE)] ; 
            //GW内子機IDと最新アップリンク時刻よりアップリンクリスト取得

            // Get mode
            $uplinklist = dbmgr::readlastuplinkdatalist3($id);
            $mode = getmode($id);

            // Get limit
            $uid = $_SESSION['userid'];
            $limit =  get_limit($uid, $id);

            // Get name
            $name =  get_name($id);

            $result = array(
                "latest_data" => implode(",", $uplinklist[0]),
                "mode" => $mode,
                "limit" => $limit,
                "name" => $name
            );
            // $array[] = implode(",", $uplinklist[0]);

            $array[] = json_encode($result);

        }
    }
    return join("\n", $array);
}

function set_limit($uid, $id, $data)
{
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

    // ======= TODO - COMMENTED BY SACREDDEVKING - BEGIN =======
    // This is error.
    $period = (isset($dataarry[6]) && $dataarry[6] != '') ? $dataarry[6] : 0;
    $span =  (isset($dataarry[7]) && $dataarry[7] != '') ? $dataarry[7] : 0;
    // ======= TODO - COMMENTED BY SACREDDEVKING - END =======

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
                    dbmgr::upvdbslavesetlog7($id, $onoff, $pr, $up, $down, $time);
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

function change_cycle($id, $pr) {

    //直接送信をデータベース書き込みに変更
    $idhour = (int)$_POST["hour"];
    // $idhour= 0;
    $idmin = (int)$_POST["minu"];
    //$idmin=30;
    //命令状況調査
    $lastset = dbmgr::readdbslavepisetlog($id);
    if (! empty($lastset)) {
        if($lastset[0]['SLAVE_PI_SET_END'] == 0){
            //時間バージョンアップ-完了を1、正常通知
            dbmgr::upenddbslavepisetlog($id, $idhour, $idmin, $pr);
            $result = "00";
        } else {
            if($lastset[0]['SLAVE_PI_ORDER_NO'] > 2) {
                //バージョンアップなし、異常通知
                $result = "99";
            } elseif($lastset[0]['SLAVE_PI_ORDER_NO'] > 0) {
                //バージョンアップなし、待機中通知
                debug_output("change_cycle_001");
                $result = "04";
            } elseif($lastset[0]['SLAVE_PI_ORDER_NO'] == 0) {
                //現在の優先順位と新命令の優先順位を比較
                // $lpr = $lastset[0]['SLAVE_PI_PR'];
                // if($pr <= $lpr) {
                    //時間バージョンアップ-優先順位変更し正常通知
                    dbmgr::upprddbslavepisetlog($id,$pr,$idhour,$idmin);
                    $result="00";
                // } elseif($pr>$lpr) {
                //     //バージョンアップなし、待機中通知
                //     debug_output("change_cycle_002");
                //     $result="04";
                // }
            }
        }
    } else {

        //新規書き込み、正常通知
        dbmgr::writedbslavepisetlog($id,$pr,$idhour,$idmin);
        $result="00";
    }
    return $result;
}

function get_name($id) {

    $slave = dbmgr::readdbslaveinfo($id);
    $name = $slave[0]['SLAVE_NAME'];
    
    return $name;
}

function update_volt($id, $wvolt) {
    //基準電圧データがあるか？
    $result = dbmgr::readSlaveStandard($id) ;
    if (!(count($result) > 0)){
        dbmgr::writeSlaveStandard($id,$wvolt);

    }else{
        dbmgr::vupSlaveStandard($id,$wvolt);
    }

    return "true";
}

// Level_logの日付を配列で返す
function get_data_dates($id) {

    $array = dbmgr::readdbdata_dates($id);
    $data = "";
    
    if (count($array) !== 0 ) {
        $arraylist=array_column($array, "grouping_column");
        foreach($arraylist as $dates){
           $data = $data .",". $dates;
        }
        $data = substr($data , 1 , strlen($data)-1 );
        //$data = trim($maillist[][GROUP_MAIL_AD]);
       //$data = $maillist;
    }

    return $data;
}


// 指定日付のデータを取得
function get_day_data($id, $date) {

    $array = dbmgr::readdbslaveupdaydata($id,$date);
    if (count($array) !== 0 ) {
        $arr = array_column($array, 'SLAVEU_DATE');
        for($i = 0; $i<count($arr); $i++){
            $ret[] = implode(",", $array[$i]);
        }
    }
 
    return join("\n", $ret);
}    

// 指定日付の最終命令を取得
function get_ctrl_log_latest($id) {
    //$id="d84a87fffefe03ca";
    $list = dbmgr::readdbslaveoc2($id);

    if (count($list) == 0) {
        return "";
    }

    $ret = $list[0]['SLAVE_OC_AT'];
    return $ret;
}


// 一週間の平均を取得
function get_week_average() {
    $date = $_POST["date"];
    $id = $_POST["id"];
    //$id="d84a87fffefe03ca";
    //$date='2022-07-20';
    $array = dbmgr::readdbslaveavelist($id, $date);

    if (count($array) !== 0) {
        $arr=array_column($array, 'date');
        for($i = 0; $i<count($arr); $i++) {
            $ret[] = implode(",", $array[$i]);
        }
    }
    return join("\n", $ret);
}

//検討中
function getmode($id) {
    //アップリンクデータ取得  
    $ret = "";
    try {
        $array = dbmgr::readlastuplinkdata($id);
        if (count($array) > 0) {
            $ret = implode(",", $array[0]);
        }
    } catch (Exception $e) {
        error_output($e->getMessage());
        throw $e;
    }
    return  $ret;
}

//子機のゲート情報取得
function get_gwid_and_ip($id) {
    $gwdata = dbmgr::readdbgw($id);
};


// 自動開閉一括処理用
function get_batch($aid) {
    $order = "";
    $data = dbmgr::readslavebatchdate($aid);
    if (count($data) > 0 && count($data[0]) > 0) {
        $arraylist = $data[0];
        $order = implode(",", $arraylist);
    }
    return $order;
}

function set_batch($aid, $auid, $avalues) {
    $values = preg_split('/,/', $avalues);
    dbmgr::write_batch($aid, $auid, $values);
    return "00";
}

// 配下ユーザー情報取得
function get_childuser($gid) {
    $ret = array();
    $data = dbmgr::getchilduser($gid);

    foreach ($data as $item) {
        $ret[] = implode(",",$item);
    }
    return implode("\n",$ret);
}

function get_slaves($agwid) {
    $ret = array();
    $data = dbmgr::readdbgwslave($agwid);
    foreach ($data as $item) {
        $ret[] = implode(",",$item);
    }
    return implode("\n",$ret);
}

function get_parmissionenable($aid) {
    $ret = "0";
    $data = dbmgr::getparmissionenable($aid);
    if (count($data) > 0) {
        $ret = $data[0];
    } else {
        return $ret;
    }
    return implode(",",$ret);
}

function off_slavesetlogtime($aid) {
    dbmgr::upenddbslavesetlogtimeoff($aid);
    return "00";
}

function set_slaveparmission($auid, $agid, $avalues) {
    $values = preg_split('/,/', $avalues);
    dbmgr::write_slaveparmission($auid, $agid, $values);
    return "00";
}

function check_parmission($permid, $sid) {
    $ret = "0";
    if($permid == "1") {
        return $ret; 
    }

    $data = dbmgr::checkparmission($sid);
    if (count($data) > 0) {
        $ret = $data[0];
    } else {
        return $ret;
    }

    return implode(",",$ret);
}

function exists_parmission($permid, $agroupid) {
    $ret = "0";
    if($permid == 1) {
        return $ret; 
    }

    $data = dbmgr::existsparmission($agroupid);
    if (count($data) > 0) {
        $ret = $data[0];
    } else {
        return $ret;
    }

    return implode(",",$ret);
}

function get_slavehist($agwid) {
    $ret = array();
    $data = dbmgr::readslavehist($agwid);
    foreach ($data as $item) {
        $ret[] = implode(",",$item);
    }
    if (count($ret) > 0) {
        return implode("\n",$ret);
    } else {
        return $ret;
    }
    
}

function get_oneslavehist($asid) {
    $ret = array();
    $data = dbmgr::readoneslavehist($asid);
    foreach ($data as $item) {
        $ret[] = implode(",",$item);
    }
    if (count($ret) > 0) {
        return implode("\n",$ret);
    } else {
        return $ret;
    }
}


?>