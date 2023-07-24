<?php

require_once(dirname(__FILE__).'/loralib.php');
require_once(dirname(__FILE__).'/db.php');

update_parmission();
down_order();

function update_parmission() {

    $now = strtotime(date("Y-m-d H:i:s"));
    $infos = dbmgr::getparmissioninfo();

    foreach ($infos as $info) {
        
        if ($info['PARMISSION_OFFON'] == 1) {

            if ($info['PARMISSION_ENABLE'] == 0) {
                if ($now >= strtotime($info['PARMISSION_ENABLE_DATE']) && $now <= strtotime($info['PARMISSION_DISABLE_DATE'])) {
                    dbmgr::updateparmissionenable($info['SLAVE_ID'], 1);
                }
            } else {
                if ($now <= strtotime($info['PARMISSION_ENABLE_DATE']) || $now >= strtotime($info['PARMISSION_DISABLE_DATE'])) {
                    dbmgr::updateparmissionenable($info['SLAVE_ID'], 0);
                }
            }

        } else {

            if ($info['PARMISSION_ENABLE'] == 1) {
                dbmgr::updateparmissionenable($info['SLAVE_ID'], 0);
            }
        }
    }
}


function down_order() {
    cron_log("▼▼▼Order.php▼▼▼");
    //全子機id取得
    $slaveinfos = dbmgr::readdbslave();
    foreach ($slaveinfos as $slaveinfo) {
        $slaveid = $slaveinfo["SLAVE_ID"];
        cron_log("\t[{$slaveid}]");
        //全子idの最新アップリンク時間取得
        $lastuplinkdate = dbmgr::readslavelastuplinkdate($slaveid);
        //データがなければデータ作成
        if (count($lastuplinkdate) == 0) {
            //新規作成
            dbmgr::writeslavelastuplinkdate($slaveid);
        }
        //全子idの最新ダウンリンク取得(ダウンリンクなしも含め)
        $downlink = dbmgr::readlastdownlinkdata($slaveid);
        //全子idの最新アップリンク取得（アップリンクなしも含めて）
        $uplink = dbmgr::readlastuplinkdata($slaveid);

        if (count($uplink) > 0) {
            //アップリンクがある
            //今回のアップリンク日時が前回のアップリンク日時より新しい
            if ($uplink[0]['SLAVEU_DATE'] > $lastuplinkdate[0]['SLAVE_LA_AT']) {
                //前回のアップリンク日時を更新する
                dbmgr::vpslavelastuplinkdate($slaveid, $uplink[0]['SLAVEU_DATE']);
                if (count($downlink) > 0) {
                    //ダウンリンクがある
                    if ($uplink[0]['SLAVEU_DATE'] > $downlink[0]['SLAVED_DATE']) {
                        //アップリンクが大きい（新しい）
                        //判断して命令を出す
                        $result1 = checkorder($uplink[0]);
                        $ctrl = $result1[1];
                        if ($ctrl > 0) {
                            $hour = $result1[2];
                            $minu = $result1[3];
                            $seq00 = get_seqno();
                            $id = $result1[5];
                            $gwinfo = $result1[6];

                            $data = create_downlink_data($ctrl, $hour, $minu);
                            $SS = send_downlink($seq00, $data, $id, $gwinfo);

                            cron_log("\t\tDownlink01送信");

                            dbmgr::writedbslavedwon($id, $ctrl, $hour, $minu);
                        } else {
                            cron_log("\t\t処理なし01");
                        }
                    } else {
                        //アップリンクが小さい（古い）
                        //なにもしない
                        cron_log("\t\t処理なし02");
                    }
                } else {
                    // ダウンリンクがない
                    // 判断して命令を出す
                    $result1 = checkorder($uplink[0]);
                    $ctrl = $result1[1];
                    if ($ctrl > 0) {
                        $hour = $result1[2];
                        $minu = $result1[3];
                        $seq00 = $result1[4];
                        $id = $result1[5];
                        $gwinfo = $result1[6];
                        // send_downlink($seq00, $data, $id, $gwinfo);
                        $data = create_downlink_data($ctrl, $hour, $minu);
                        $SS = send_downlink($seq00, $data, $id, $gwinfo);
                        cron_log("\t\tDownlink02送信");
                        //ダウンリンクデータログ
                        dbmgr::writedbslavedwon($id, $ctrl, $hour, $minu);
                    } else {
                        cron_log("\t\t処理なし03");
                    }
                }
            } else {
                //前回アップリンクと今回アップリンクの日時が同じ
                //なにもしない
                cron_log("\t\t処理なし04");
            }
            
        } else {
            //アップリンクがない
            //なにもしない
            cron_log("\t\t処理なし05");
        }
    }
    cron_log("▲▲▲Order.php▲▲▲\n");
}

function checkorder($array) {
    //アップリンク状況と命令セット状況により命令作成
    //開閉命令チェック0制御なし1開制御2閉制御
    $openclose_oder = check_opencloseorder($array);
    //周期命令チェック$priodorder[0]=0制御なし1制御あり$priodorder[1]時間$priodorder[2]分
    $priodorder = check_periodorder($array);
    //命令用データと命令ログ用変数作成
    if (($openclose_oder[0] == 0) && ($priodorder[0] == 0)) {
        $etcorder = 0;
        $state = "";
    }
    if (($openclose_oder[0] == 0) && ($priodorder[0] == 1)) {
        $etcorder = 10;
        $state = "";
    }
    if (($openclose_oder[0] == 1) && ($priodorder[0] == 0)) {
        $etcorder = 2;
        $state = "開制御";
    }
    if (($openclose_oder[0] == 1) && ($priodorder[0] == 1)) {
        $etcorder = 12;
        $state = "開制御";
    }
    if (($openclose_oder[0] == 2) && ($priodorder[0] == 0)) {
        $etcorder = 4;
        $state = "閉制御";
    }
    if (($openclose_oder[0] == 2) && ($priodorder[0] == 1)) {
        $etcorder = 14;
        $state = "閉制御";
    }

    //命令ログの書き込み
    $remark = "";
    if ($openclose_oder[0] > 0) {
        switch ((int)($openclose_oder[2])) {
            case 1 :
                $remark = "管理者手動一括";
                break;
            case 2 :
                $remark = "管理者手動個別";
                break;
            case 3 :
                $remark = "手動個別";
                break;
            case 4 :
                $remark = "時間一括";
                break;
            case 5 :
                $remark = "時間個別";
                break;
            case 6 :
                $remark = "水位一括";
                break;
            case 7 :
                $remark = "水位個別";
                break;
            case 8 :
                $remark = "時間一括逆";
                break;
            case 9 :
                $remark = "時間個別逆";
                break;
            }
        dbmgr::writedbslaveoc($array['SLAVEU_ID'], $state, $remark);
        cron_log("\t命令[{$remark}]");
    }

    $gwinfo = dbmgr::readdbgw($array['SLAVEU_ID']);
    $result1[] = "0";
    $result1[] = $etcorder;
    $result1[] = $priodorder[1];
    $result1[] = $priodorder[2];
    $result1[] = "";
    $result1[] = $array['SLAVEU_ID'];
    $result1[] = $gwinfo;
    
    return  $result1;
}

//
function get_seqno() {
    $seqno = 0;

    $calamname =  "seqno2";
    $seqno = (int)dbmgr::getdbseqno($calamname) + 1;
    if ($seqno > 655351) {
        $seqno = 2;
    }
    dbmgr::writedbseqno($seqno, $calamname);
    return $seqno;
}

//周期判定
function check_periodorder($array) {
    $id = $array['SLAVEU_ID'];
    $orderflag = 0;
    $hhh = "";
    $mmm = "";
    $alertflag = 0;
    $orderno = 0;
    $uplinkno = 0;
    $lastset = dbmgr::readdbslavepisetlog($id);

    //命令中
    $orderflag = $lastset[0]['SLAVE_PI_SET_END'];
    $hhh = $lastset[0]['SLAVE_PI_KANNRI_TIMEH'];
    $mmm = $lastset[0]['SLAVE_PI_KANNRI_TIMEM'];
    $orderno = $lastset[0]['SLAVE_PI_ORDER_NO'];
    $uplinkno = $lastset[0]['SLAVE_PI_UPLINK_NO'];
    //今回命令
    $noworder = 0;
    $nowperiod = (strtotime($array['SLAVEU_DATE']) - strtotime($array['SLAVEU_LASTDATE'])) / 60;
    $orderpriod = 60 * $hhh + $mmm;
    $alertflag = $lastset[0]['SLAVE_PI_ALERT'];
    if (! empty($lastset)) {
        //命令ログがあれば
        if ($orderflag > 0) { 
            //命令セットで命令がまだなら命令一回目実施
            if (!$orderno == 0) {
                //命令中ならアップリンク回数調査
                if ($uplinkno > 0) {
                    //アップリンク2回目なら状態判定してアップリンク回数を0に
                    $uplinkno = 0;
                    
                    if ( !((($orderpriod - 3) < $nowperiod) && ($nowperiod < ($orderpriod + 3)))) {
                        //周期が命令プラスマイナス3分内でない
                
                        if ($orderno < 3) {
                            //命令回数が２回以内なら再命令
                            $orderno = $orderno + 1;
                            $noworder = 1;

                        } else {
                            //３回以上なら異常通知と再命令
                            $alertflag = 1;
                            $orderno = $orderno + 1;
                            $noworder = 1;
                        } 

                    } else {
                        //命令通り動作したので完了異常通知と命令フラッグ命令回数をリセット
                        $alertflag = 0;
                        $orderflag = 0;
                        $orderno = 0;
                        $noworder = 0;
                    }
                } else {
                  //アップリンク回数を増やす。命令は出さない
                  $uplinkno = 1;
                  $noworder = 0;
                }
            } else {
                //初回命令
                $orderno = 1;
                $noworder = 1;
            }
            //判定結果でSlavePISetLogの書き換え
            dbmgr::upprddbslavepisetlog_after($id, $uplinkno, $orderflag, $alertflag, $orderno);
        }
        //命令中でなければ命令一回
    }
    //命令セットがなければ特になにもしない

    //ダウンリンク作成情報
    $resultpir[] = $noworder;
    $resultpir[] = $hhh;
    $resultpir[] = $mmm;
    return $resultpir;
}

//開閉判定
function check_opencloseorder($array) {
    $id = $array['SLAVEU_ID'];
    $orderflag = 0;
    $result[0] = 0;
    $resuletoc = 0;
    $pr = 9;

    //現在の命令情報取得
    $lastset = dbmgr::readdbslavesetlog($id);
    //命令ありなしチェック
    if (! empty($lastset)) {
        //
        if ($lastset[0]['SLAVE_SET1OFFON'] == 1) {
            //手動開閉の場合
            $result=check_handorder($id, $array, $lastset);
        } elseif (($lastset[0]['SLAVE_SET4OFFON'] == 1) || ($lastset[0]['SLAVE_SET5OFFON'] == 1)) {
            debug_output("時間設定開閉");
            //時間設定開閉の場合
            $result = check_timeorder($id, $array, $lastset);
            //時間設定で命令タイミングでなければ水位チェック実施
            if (($result[0] == 0) && (($lastset[0]['SLAVE_SET6OFFON'] == 1)) || ($lastset[0]['SLAVE_SET7OFFON'] == 1)) {
                $result = check_levelorder($id, $array, $lastset);
            }
            if ($result[0] == 0) {
                $result = check_time2order($id, $array, $lastset);
            }
        } elseif (($lastset[0]['SLAVE_SET6OFFON'] == 1) || ($lastset[0]['SLAVE_SET7OFFON'] == 1)) {
            //水位設定開閉の場合
            debug_output("水位設定開閉");
            if ($result[0] == 0) {
                $result = check_levelorder($id, $array, $lastset);
            }
        } elseif (($lastset[0]['SLAVE_SET8OFFON'] == 1) || ($lastset[0]['SLAVE_SET9OFFON'] == 1)) {
            //時間設定逆開閉の場合
            debug_output("時間設定逆開閉");
            if ($result[0] == 0) {
                $result = check_time2order($id, $array, $lastset);
            }
        }
        $pr = $lastset[0]['SLAVE_SET_PR'];
    }

    //ダウンリンク作成情報（0:無/1:開制御/2:閉制御)
    if ($result[0] == 0) {
        $resuletoc = 0;
    } elseif ($result[0] == 1) {
        if ($result[1] == 1) {
            $resuletoc = 1;
        } elseif ($result[1] == 0) {
            $resuletoc = 2;
        }
    }

    if ($resuletoc == 0) {
        $result[2] = "";
    }

    $resultnew[] = $resuletoc;
    $resultnew[] = $pr;
    $resultnew[] = $result[2];
   
    return $resultnew;
}

function check_handorder($id, $array, $lastset) {
    $orderflag = 0;
    $alertflag = 0;
    $orderno = 0;
    $uplinkno = 0;
    //命令中
    $orderflag = $lastset[0]['SLAVE_SET_END'];
    //命令回数
    $orderno = $lastset[0]['SLAVE_SET_ORDER_NO'];
    //アップリンク回数
    $uplinkno = $lastset[0]['SLAVE_SET_UPLINK_NO'];
    //アラーと情報
    $alertflag = $lastset[0]['SLAVE_SET_ALERT'];
    //開閉
    $orderstate = $lastset[0]['SLAVE_SET1CLOSEOPEN'];
    //現状の開閉状態
    $nowstate = $array['SLAVEU_OPCLSTATE'];
    //優先順位
    $pr = $lastset[0]['SLAVE_SET_PR'];
    //命令ありなし
    $setflag = 1;
    //命令実行有無
    $noworderflag = 0;
    if ($nowstate == 2) {
        $nowstate = 0;
    }
    
    if ($orderstate !== $nowstate) {
       if (!$orderno == 0) {
        //命令中ならアップリンク回数調査
             if ($uplinkno > 0) {
                //アップリンク2回目なら状態判定してアップリンク回数を0に
                $uplinkno = 0;

                if ($orderstate !== $nowstate) {
                    //開閉は不整合 
                    if ($orderno < 3) {
                        //命令実施
                        //命令回数が２回以内なら再命令
                        $orderno = $orderno + 1;
                        $noworderflag = 1;

                    } else {
                        //命令実施 
                        //３回以上なら異常通知
                        $alertflag = 1;
                        $orderno = $orderno + 1;
                        $noworderflag = 1;
                    }

                } else {
                    //命令通り開閉動作したので完了異常通知と命令フラッグ命令回数をリセット
                    $alertflag = 0;
                    $orderflag = 0;
                    $orderno = 0;
                    //$orderstate=0;
                    $setflag = 0;
                    $pr = 9;
                }

            } else {
                //アップリンク回数を増やす
                $uplinkno = 1;
            }
        } else {
            $noworderflag = 1;
            $orderno = 1;
        }
    } else {
        //命令通り開閉動作したので完了異常通知と命令フラッグ命令回数をリセット
        $alertflag = 0;
        $orderflag = 0;
        $orderno = 0;
        //$orderstate=0;
        $setflag = 0;
        $pr = 9;
        $uplinkno = 0;
        $alertflag = 0; 
    }

    //判定結果でSlavePISetLogの書き換え
    dbmgr::upprddbslavesetlog_after1($id, $setflag, $uplinkno, $orderflag, $alertflag, $orderno, $pr);
    $result[] = $noworderflag;
    $result[] = $orderstate;
    $result[] = $pr;
    return $result;
}

function check_timeorder($id, $array, $lastset) {
    debug_output("▼▼▼check_timeorder▼▼▼");
    debug_output("\t子機ID：".$id);
    // 現状の開閉状態
    $nowstate = $array['SLAVEU_OPCLSTATE'];
    if ($nowstate == 2) {
        $nowstate = 0;
    }

    $orderflag = 0;
    $orderstate2 = 0;

    $time = "";
    $etime = "";
    $PR = 0;
    // アップリンク回数
    $uplinkno = $lastset[0]['SLAVE_SET_UPLINK_NO'];
    if ($lastset[0]['SLAVE_SET4OFFON'] == 1) {
        // 一括時間設定
        debug_output("\t一括時間設定");
        // debug_output("\tSLAVE_SET4CLOSEOPEN ".$lastset[0]['SLAVE_SET4CLOSEOPEN']);
        // debug_output("\tSLAVE_SET4TIME ".$lastset[0]['SLAVE_SET4TIME']);
        // debug_output("\tSLAVE_SET8TIME ".$lastset[0]['SLAVE_SET8TIME']);
        $orderstate2 = $lastset[0]['SLAVE_SET4CLOSEOPEN'];
        $time = $lastset[0]['SLAVE_SET4TIME'];
        $etime = $lastset[0]['SLAVE_SET8TIME'];

        $PR = 4;
    } elseif ($lastset[0]['SLAVE_SET5OFFON'] == 1) {
        // 個別時間設定
        debug_output("個別時間設定");
        // debug_output("\tSLAVE_SET5CLOSEOPEN ".$lastset[0]['SLAVE_SET5CLOSEOPEN']);
        // debug_output("\tSLAVE_SET5TIME ".$lastset[0]['SLAVE_SET5TIME']);
        // debug_output("\tSLAVE_SET9TIME ".$lastset[0]['SLAVE_SET9TIME']);
        $orderstate2 = $lastset[0]['SLAVE_SET5CLOSEOPEN'];
        $time = $lastset[0]['SLAVE_SET5TIME'];
        $etime = $lastset[0]['SLAVE_SET9TIME'];
        $PR = 5;
    }

    if ($time != "") {

        $slavetime = strtotime($array['SLAVEU_DATE']);

        if (strtotime($time) < $slavetime && strtotime($etime) >= $slavetime) {  

            $timeToday = date("Y-m-d")." ".explode(' ', $time)[1];
            $nowperiod = (strtotime($array['SLAVEU_DATE']) - strtotime($array['SLAVEU_LASTDATE'])) / 60;
            debug_output("\t前回周期との差(分) $nowperiod");
            $timediff = $slavetime - strtotime($timeToday);

            //アップリンク２回目を判定して２回目ならアップリンク回数を0にして判定１回目なら判定しない 
            debug_output("\ttimediff{$timediff}");
            debug_output("\tnowperiod".$nowperiod * 62);
            if ($timediff > 0 && ($timediff < ($nowperiod * 62))) {
                // 命令の開閉と現状の開閉があっていなければ命令を出す。
                if ($orderstate2 != $nowstate) {
                    debug_output("\t時間設定 指示あり");
                    $orderflag = 1;
                } else {
                    // 命令の開閉と現状の開閉があっていれば命令をださない。
                    debug_output("\t時間設定 指示無");
                    $orderflag = 0;
                }
            } else {
                debug_output("\t時間設定 時間外 指示無");
            }
        } else { 
            debug_output("\t時間設定 期間外 指示無");
        }
    } else {
        debug_output("\t設定なし 指示無");
    }

    $result[] = $orderflag;
    $result[] = $orderstate2;
    $result[] = $PR;

    // debug_output("\tresult[0]".$orderflag);
    // debug_output("\tresult[1]".$orderstate2);
    // debug_output("\tresult[2]".$PR);
    debug_output("▲▲▲check_timeorder▲▲▲");
    return $result;
}

function check_levelorder($id, $array, $lastset) {
    $orderflag = 0;
    $nowstate = $array['SLAVEU_OPCLSTATE'];
    //命令回数
    $orderno = $lastset[0]['SLAVE_SET_SORDER_NO'];
    if ($nowstate == 2)
    {
        $nowstate = 0;
    }

    if ($lastset[0]['SLAVE_SET6OFFON'] == 1) {
        $up = $lastset[0]['SLAVE_SET6UP'];
        $down = $lastset[0]['SLAVE_SET6DOWN'];
        $time = $lastset[0]['SLAVE_SET6TIME'];
        $PR = 6;
    } elseif ($lastset[0]['SLAVE_SET7OFFON'] == 1) {
        $up = $lastset[0]['SLAVE_SET7UP'];
        $down = $lastset[0]['SLAVE_SET7DOWN'];
        $time = $lastset[0]['SLAVE_SET7TIME'];
        $PR = 7;
    }
    //命令回数により現在命令０回なら命令出し命令１に現在命令１回なら命令をださずに命令0に
    if ($orderno == 0) {
        //限度時間以内で上限水位オーバー
        if (($up <= $array['SLAVEU_SUIIDATA']) and ($time > date("Y-m-d H:i:s"))) {
            $orderstate3 = 0;
            if ($orderstate3 != $nowstate) {
                $orderflag = 1;
                dbmgr::upprddbslavesetlog_after2($id, $orderflag);
            } else {
                //命令の開閉と現状の開閉があっていなければ命令をださない。
                $orderflag = 0;
            }
        }
        //限度時間以内で下限水位以下  
        if (($down >= $array['SLAVEU_SUIIDATA']) and ($time>date("Y-m-d H:i:s"))) {
            $orderstate3 = 1;
            if ($orderstate3 != $nowstate) {
                $orderflag = 1;
                dbmgr::upprddbslavesetlog_after2($id, $orderflag);
            } else {
                //命令の開閉と現状の開閉があっていなければ命令をださない。
                $orderflag = 0;
            }
        }
    } else {
        $orderflag = 0;
        dbmgr::upprddbslavesetlog_after2($id, $orderflag);
    } 

    if ($orderflag == 0) {
        $orderstate3 = "";
    } 
    $result[] = $orderflag;
    $result[] = $orderstate3;
    $result[] = $PR;
    return $result;
}

function check_time2order($id, $array, $lastset) {
    debug_output("▼▼▼check_time2order▼▼▼");
    debug_output("\t子機ID：".$id);
    $nowstate = $array['SLAVEU_OPCLSTATE'];

    if ($nowstate == 2) {
        $nowstate = 0;
    }

    $orderflag = 0;
    $orderstate4 = 0;
    $time = "";
    $etime = "";
    $period = "";
    $PR = 0;
    if ($lastset[0]['SLAVE_SET8OFFON'] == 1) {
        $orderstate4 = $lastset[0]['SLAVE_SET8CLOSEOPEN'];

        $time = $lastset[0]['SLAVE_SET4TIME'];
        $etime = $lastset[0]['SLAVE_SET8TIME'];

        $period = $lastset[0]['SLAVE_SET4_LIMIT'];

        $PR = 8;

        debug_output("\t一括時間設定逆");
        // debug_output("\t開閉値 SLAVE_SET8CLOSEOPEN ".$lastset[0]['SLAVE_SET8CLOSEOPEN']);
        // debug_output("\t期間開始日時 SLAVE_SET4TIME ".$lastset[0]['SLAVE_SET4TIME']);
        // debug_output("\t期間終了日時 SLAVE_SET8TIME ".$lastset[0]['SLAVE_SET8TIME']);
        // debug_output("\t開閉時間 SLAVE_SET4_LIMIT ".$lastset[0]['SLAVE_SET4_LIMIT']);

    } elseif ($lastset[0]['SLAVE_SET9OFFON'] == 1) {
        $orderstate4 = $lastset[0]['SLAVE_SET9CLOSEOPEN'];
        // $time = $lastset[0]['SLAVE_SET9TIME'];

        $time = $lastset[0]['SLAVE_SET5TIME'];
        $etime = $lastset[0]['SLAVE_SET9TIME'];

        $period = $lastset[0]['SLAVE_SET5_LIMIT'];

        $PR = 9;

        debug_output("個別時間設定逆");
        // debug_output("\t開閉値 SLAVE_SET9CLOSEOPEN ".$lastset[0]['SLAVE_SET9CLOSEOPEN']);
        // debug_output("\t期間開始日時 SLAVE_SET5TIME ".$lastset[0]['SLAVE_SET5TIME']);
        // debug_output("\t期間終了日時 SLAVE_SET8TIME ".$lastset[0]['SLAVE_SET8TIME']);
        // debug_output("\t開閉時間 SLAVE_SET5_LIMIT ".$lastset[0]['SLAVE_SET5_LIMIT']);
        
    }

    if ($time != "") {
        if ($period != "" && $period != 0) {
            $roottime = new DateTime($time);
            $time = date_modify($roottime, "+{$period} hour")->format('Y-m-d H:i:s');

            $roottime = new DateTime($etime);
            $etime = date_modify($roottime, "+{$period} hour")->format('Y-m-d H:i:s');
        }
    }

    if ($time != "") {

        $slavetime = strtotime($array['SLAVEU_DATE']);

        // 設定期間内か否か
        if ((strtotime($time) < $slavetime) && (strtotime($etime) >= $slavetime)) {   

            $timeToday = date("Y-m-d")." ".explode(' ', $time)[1];
            $timediff = $slavetime - strtotime($timeToday);

            //アップリンク２回目を判定して２回目ならアップリンク回数を0にして判定１回目なら判定しない

            $nowperiod = (strtotime($array['SLAVEU_DATE']) - strtotime($array['SLAVEU_LASTDATE'])) / 60;

            debug_output("\ttimediff{$timediff}");
            debug_output("\tnowperiod".$nowperiod * 62);
            debug_output("\t前回周期との差(分) $nowperiod");
            if ($timediff > 0 && $timediff < ($nowperiod * 62)) {
                //命令の開閉と現状の開閉があっていなければ命令を出す。
                if ($orderstate4 != $nowstate) {
                    debug_output("\t時間設定 指示あり");
                    $orderflag = 1;
                } else {
                    debug_output("\t時間設定 指示無");
                    //命令の開閉と現状の開閉があっていれば命令をださない。
                    $orderflag = 0;
                }
            } else {
                debug_output("\t時間設定 時間外 指示無");
            }
        } else { 
            debug_output("\t時間設定 期間外 指示無");
        }
    } else {
        debug_output("\t設定なし 指示無");
    }

    $result[] = $orderflag;
    $result[] = $orderstate4;
    $result[] = $PR;

    // debug_output("\tresult[0]".$orderflag);
    // debug_output("\tresult[1]".$orderstate4);
    // debug_output("\tresult[2]".$PR);
    debug_output("▲check_time2order▲");
    return $result;
}

?>