<?php

require_once(dirname(__FILE__).'/lib.php');

// データベース通信クラス
class dbmgr {

  /**
   * 通信設定
   * @return PDO
   */
  // データベース通信設定
  private static function dbPDO() {
    //$dsn2 = 'mysql:dbname=hydrant;host=163.44.252.63;charset=utf8';
    $dsn2 = 'mysql:dbname=hydrant;host=localhost;charset=utf8';

    // $dbuser2 = 'user1';
    //$dbpassword2 = getenv('WSDBPass');
    // $dbpassword2 = 'Itec1025';

    $dbuser2 = 'phpmyadmin';
    $dbpassword2 = '47184719Itech@ad';
    
    $dbh = new PDO($dsn2, $dbuser2, $dbpassword2, array(
      PDO::ATTR_EMULATE_PREPARES => false,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ));
    return $dbh;
  }
  
  // seqnoを取得する
  public static function getdbseqno($str) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT NUMBER
      FROM numbering WHERE NUMBERING_ID = ? 
      ;');
    
      $sth->bindValue(1, $str, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result[0]['NUMBER'];
  }
  
  // seqnoを更新する
  public static function writedbseqno($str1,$str2) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      UPDATE numbering SET NUMBER = ?
      WHERE NUMBERING_ID = ? 
      ;');
    
      $sth->bindValue(1, $str1, PDO::PARAM_STR);
      $sth->bindValue(2, $str2, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return;
  }

  // seqnoを作成する
  public static function insetdbseqno($str1) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      INSERT INTO numbering (
        NUMBERING_ID, NUMBER 
        )
      VALUES(
        :NUMBERING_ID,1 
      )'
      );
    
      $sth->bindValue('NUMBERING_ID', $str1);

      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return;
  }

  // 対象ユーザのグループ情報を取得する
  public static function getdbgroup($str) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM hydrant.group WHERE GROUP_ID = ? AND GROUP_TYPE_CONTINUE = "0"
      ;');
    
      $sth->bindValue(1, $str, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }
  
  // ユーザー情報を取得する
  public static function getuserdata($str) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM user WHERE USER_LOGIN_ID = ? and USER_TYPE_CONTINUE = "0"
      ;');
    
      $sth->bindValue(1, $str, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  // パスワードを更新する
  public static function writedbpass($str1, $str2) {
    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
    UPDATE user SET USER_PASSWORD = ?
    WHERE USER_LOGIN_ID = ? 
    ;');

    $dbh ->beginTransaction();
    try { 
      $sth->bindValue(1, $str1, PDO::PARAM_STR);
      $sth->bindValue(2, $str2, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }  

  //アップリンクデータ取得
  public static function readuplinkdata($UPARRAY) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        SELECT *
        FROM slaveupdata WHERE SLAVEU_ID = ? ORDER BY SLAVEU_DATE DESC
      ;');
    
      $sth->bindValue(1, $UPARRAY[0], PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  //最新アップリンクデータを一つ取得
  public static function readlastuplinkdata($id) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM slaveupdata WHERE SLAVEU_ID = ? ORDER BY SLAVEU_DATE DESC limit 1
      ;');
    
      $sth->bindValue(1, $id, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }       

  // アップリンクデータを書き込む
  public static function writeuplinkdate($UPARRAY) {
    //重複チェックして重複していなければ書き込む
    if ((count(dbmgr::readuplinkdata($UPARRAY)) === 0) || ((count(dbmgr::readuplinkdata($UPARRAY)) > 0) &&
      (!($UPARRAY[1] === (dbmgr::readuplinkdata($UPARRAY))[0]["SLAVEU_DATE"])))) {
      $dbh = self::dbPDO();
    
      $sth = $dbh->prepare('
        INSERT INTO slaveupdata (
          SLAVEU_ID, SLAVEU_DATE, SLAVEU_SET, SLAVEU_STATE, SLAVEU_BAT, 
          SLAVEU_RSSI, SLAVEU_SNR, SLAVEU_SUIIDATA, SLAVEU_TEMPDATA, SLAVEU_LASTDATE, 
          SLAVEU_LASTSUIIDATA, SLAVEU_LASTTEMPDATA, SLAVEU_CONTDATE, SLAVEU_OPCLSTATE, SLAVEU_SUIIDATAV, SLAVEU_SUIIDATALV)
        VALUES(
          :SLAVEU_ID, :SLAVEU_DATE, :SLAVEU_SET, :SLAVEU_STATE, :SLAVEU_BAT,  
          :SLAVEU_RSSI, :SLAVEU_SNR, :SLAVEU_SUIIDATA, :SLAVEU_TEMPDATA, :SLAVEU_LASTDATE,
          :SLAVEU_LASTSUIIDATA, :SLAVEU_LASTTEMPDATA, :SLAVEU_CONTDATE, :SLAVEU_OPCLSTATE, :SLAVEU_SUIIDATAV, :SLAVEU_SUIIDATALV
        )'
      );

      $dbh ->beginTransaction();
      try {
        $sth->bindValue(':SLAVEU_ID', $UPARRAY[0]);
        $sth->bindValue(':SLAVEU_DATE', $UPARRAY[1]);
        $sth->bindValue(':SLAVEU_SET', $UPARRAY[2]);
        $sth->bindValue(':SLAVEU_STATE', $UPARRAY[3]);
        $sth->bindValue(':SLAVEU_BAT', $UPARRAY[4]);
        $sth->bindValue(':SLAVEU_RSSI', $UPARRAY[5]);
        $sth->bindValue(':SLAVEU_SNR', $UPARRAY[6]);
        $sth->bindValue(':SLAVEU_SUIIDATA', $UPARRAY[7]);
        $sth->bindValue(':SLAVEU_TEMPDATA', $UPARRAY[8]);
        $sth->bindValue(':SLAVEU_LASTDATE', $UPARRAY[9]);
        $sth->bindValue(':SLAVEU_LASTSUIIDATA', $UPARRAY[10]);
        $sth->bindValue(':SLAVEU_LASTTEMPDATA', $UPARRAY[11]);
        $sth->bindValue(':SLAVEU_CONTDATE', $UPARRAY[12]);
        $sth->bindValue(':SLAVEU_OPCLSTATE', $UPARRAY[13]);
        $sth->bindValue(':SLAVEU_SUIIDATAV', $UPARRAY[14]);
        $sth->bindValue(':SLAVEU_SUIIDATALV', $UPARRAY[15]);

        // 実行
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        $dbh->commit();
      } catch (Exception $e) {
        error_output("writeuplinkdate".$e->getMessage());
        $dbh->rollBack();
        throw $e;
      }
      return;
    } 
  }

  // 基準電圧を書き込む
  public static function writeSlaveStandard($id,$str) {
    $ddate=date("Y/m/d H:i:s");
    $dbh = self::dbPDO();
    $sth = $dbh->prepare('
      INSERT INTO slavestandardvlog (
        SLAVE_SET_ID, SLAVE_SET_AT, SALVE_SET_REMARKS, SLAVE_SET_STADARD, SLAVE_SET_UPDATE_ID 
      )
      VALUES(
        :SLAVE_SET_ID, :SLAVE_SET_AT, :SALVE_SET_REMARKS, :SLAVE_SET_STADARD, :SLAVE_SET_UPDATE_ID  
      )'
    );
    $dbh ->beginTransaction();

    try { 
      $sth->bindValue(':SLAVE_SET_ID', $id);
      $sth->bindValue(':SLAVE_SET_AT', $ddate);
      $sth->bindValue(':SALVE_SET_REMARKS', "");
      $sth->bindValue(':SLAVE_SET_STADARD', $str);
      $sth->bindValue(':SLAVE_SET_UPDATE_ID', "");

      // 実行
      $sth->execute();

      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      $dbh->rollBack();
      error_output($e->getMessage());
      throw $e;
    }
    return;
  }

  // 基準電圧を更新
  public static function vupSlaveStandard($id,$str) {
    $ddate=date("Y/m/d H:i:s");
    $dbh = self::dbPDO();
      
    $sth = $dbh->prepare('
      UPDATE slavestandardvlog
      SET
      SLAVE_SET_AT = :SLAVE_SET_AT,
      SLAVE_SET_STADARD = :SLAVE_SET_STADARD
      WHERE  SLAVE_SET_ID = :SLAVE_SET_ID'
    );

    $dbh ->beginTransaction();
    try { 
      $sth->bindValue(':SLAVE_SET_ID', $id);
      $sth->bindValue(':SLAVE_SET_AT', $ddate);
      $sth->bindValue(':SLAVE_SET_STADARD', $str);

      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  //基準電圧データ取得
  public static function readSlaveStandard($id) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM slavestandardvlog WHERE SLAVE_SET_ID = ? ORDER BY SLAVE_SET_AT DESC
      ;');
      $sth->bindValue(1, $id, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }

    return $result;
  }

  //子機idからgw情報取得
  public static function readdbgw($id) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT * FROM gateway INNER JOIN slave
      ON gateway.GW_ID = slave.SLAVE_GW_ID 
      WHERE slave.SLAVE_ID = ? AND GW_TYPE_CONTINUE ="0"
      ;');

      $sth->bindValue(1, $id, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  //グループidからメールリスト情報取得
  public static function readdbgroupmaillist($STR) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        SELECT * FROM groupmaillist 
        WHERE GROUP_MAIL_ID = ? 
        ORDER BY GROUP_MAIL_DATE DESC limit 1
      ;');
    
      $sth->bindValue(1, $STR, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result1 = $sth->fetchAll(PDO::FETCH_ASSOC);
      //最新メール設定日時取得
      $lastdate =  $result1[0]["GROUP_MAIL_DATE"];

      $sth = $dbh->prepare('
        SELECT * FROM groupmaillist 
        WHERE GROUP_MAIL_ID = ? and GROUP_MAIL_DATE = ?
        ORDER BY GROUP_MAIL_UP_ID
      ;');
    
      $sth->bindValue(1, $STR, PDO::PARAM_STR);
      $sth->bindValue(2, $lastdate, PDO::PARAM_STR);
      // 実行
      $sth->execute();

      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }  

  //グループidメールリスト消し
  public static function deletedbgroupmaillist($id)
  {
    $dbh = self::dbPDO();
      
    $sth = $dbh->prepare('
      DELETE FROM groupmaillist WHERE GROUP_MAIL_ID = :id
    ');
    $dbh ->beginTransaction();
    try { 
      $sth->bindValue(':id', $id);

      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    } 
    return;
  }

  // グループidのメールリスト書き込み
  public static function writedbgroupmaillist($id,$uid,$str,$setat){
    $dbh = self::dbPDO();
      
    $sth = $dbh->prepare('
      INSERT INTO groupmaillist (
      GROUP_MAIL_ID, GROUP_MAIL_DATE, GROUP_MAIL_AD, GROUP_MAIL_UP_ID 
      )
      VALUES(
        :GROUP_MAIL_ID, :GROUP_MAIL_DATE, :GROUP_MAIL_AD, :GROUP_MAIL_UP_ID 
      )'
    );
    $dbh ->beginTransaction();
    try { 
      $sth->bindValue(':GROUP_MAIL_ID', $id);
      $sth->bindValue(':GROUP_MAIL_DATE', $setat);
      $sth->bindValue(':GROUP_MAIL_AD', $str);
      $sth->bindValue(':GROUP_MAIL_UP_ID', $uid);

      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }   
   //アラート設定の書き込み
   //「true」と「false」は「1」と「0」
  public static function write_db_group_alert_level($id, $uid, $values, $setat) {

    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
    INSERT INTO groupalertsetlog (
      GROUP_SET_ID, GROUP_SET_AT, GROUP_SET_REMARKS,
      GROUP_SET_UP_LIM_WATER_AL, GROUP_SET_UP_LIM_WATER, 
      GROUP_SET_LO_LIM_WATER_AL, GROUP_SET_LO_LIM_WATER, 
      GROUP_SET_HIGH_TEMP_LIM_AL, GROUP_SET_HIGH_TEMP_LIM,
      GROUP_SET_LOW_TEMP_LIM_AL, GROUP_SET_LOW_TEMP_LIM, 
      GROUP_SET_UPDATE_ID
    )
    VALUES(
      :GROUP_SET_ID, :GROUP_SET_AT, :GROUP_SET_REMARKS,
      :GROUP_SET_UP_LIM_WATER_AL, :GROUP_SET_UP_LIM_WATER, 
      :GROUP_SET_LO_LIM_WATER_AL, :GROUP_SET_LO_LIM_WATER, 
      :GROUP_SET_HIGH_TEMP_LIM_AL, :GROUP_SET_HIGH_TEMP_LIM,
      :GROUP_SET_LOW_TEMP_LIM_AL, :GROUP_SET_LOW_TEMP_LIM, 
      :GROUP_SET_UPDATE_ID 
    )'
    );

    $dbh ->beginTransaction();
    try { 
      $sth->bindValue(':GROUP_SET_ID', $id);
      $sth->bindValue(':GROUP_SET_AT', $setat);
      $sth->bindValue(':GROUP_SET_REMARKS', "");
      $sth->bindValue(':GROUP_SET_UP_LIM_WATER_AL', $values[0]);
      $sth->bindValue(':GROUP_SET_UP_LIM_WATER', $values[1]);
      $sth->bindValue(':GROUP_SET_LO_LIM_WATER_AL', $values[2]);
      $sth->bindValue(':GROUP_SET_LO_LIM_WATER', $values[3]);
      $sth->bindValue(':GROUP_SET_HIGH_TEMP_LIM_AL', $values[4]);
      $sth->bindValue(':GROUP_SET_HIGH_TEMP_LIM', $values[5]);
      $sth->bindValue(':GROUP_SET_LOW_TEMP_LIM_AL', $values[6]);
      $sth->bindValue(':GROUP_SET_LOW_TEMP_LIM', $values[7]);
      $sth->bindValue(':GROUP_SET_UPDATE_ID', $uid);

      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }
 
  //グループidから水位アラートリスト情報取得
  public static function readdbgroupalertlist($STR) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT * FROM groupalertsetlog 
      WHERE GROUP_SET_ID = ? 
      ORDER BY GROUP_SET_AT DESC limit 1
      ;');
    
      $sth->bindValue(1, $STR, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result1 = $sth->fetchAll(PDO::FETCH_ASSOC);
      $lastdate =  $result1 [0]['GROUP_SET_AT'];

      $sth = $dbh->prepare('
      SELECT * FROM groupalertsetlog 
      WHERE GROUP_SET_ID = ? and GROUP_SET_AT = ?
      ;');
    
      $sth->bindValue(1, $STR, PDO::PARAM_STR);
      $sth->bindValue(2, $lastdate, PDO::PARAM_STR);
      // 実行
      $sth->execute();

      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  //グループ内ゲート情報取得
  public static function readdbgroupgw($usergroupid){
    $dbh = self::dbPDO();
      try {
        $sth = $dbh->prepare('
        SELECT * FROM gateway  
        WHERE GW_GROUP_ID = ? AND GW_TYPE_CONTINUE = "0"
        ;');
        $sth->bindValue(1, $usergroupid, PDO::PARAM_STR);
        //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
        // 実行
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      } catch (Exception $e) {
        error_output($e->getMessage());
        throw $e;
    }
    return $result;
  }

  //グループ内ゲート数取得
//  public static function readdbgroupgwno($usergroupid){
//    $dbh = self::dbPDO();
//      try {
//        $sth = $dbh->prepare('
//        SELECT count(GW_ID) FROM gateway  
//        WHERE GW_GROUP_ID = ? 
//        ;');
//        $sth->bindValue(1, $usergroupid, PDO::PARAM_STR);
//        //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
//        // 実行
//        $sth->execute();
//        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
//      } catch (Exception $e) {
//          throw $e;
//    }
//    return $result;
//  }

  //ゲート内子機情報取得
  public static function readdbgwslave($gwid) {
    $dbh = self::dbPDO();
      try {
        $sth = $dbh->prepare('
        SELECT * FROM slave  
        WHERE SLAVE_GW_ID = ? AND SLAVE_TYPE_CONTINUE="0"
        ORDER BY SLAVE_NUMBER 
        ;');
        $sth->bindValue(1, $gwid, PDO::PARAM_STR);
        //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
        // 実行
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      } catch (Exception $e) {
        error_output($e->getMessage());
        throw $e;
    }
    return $result;
  }

  //グループ内子機情報取得
  public static function readdbgroupslave($usergroupid) {
    $dbh = self::dbPDO();
      try {
        $sth = $dbh->prepare('
        SELECT * FROM slave 
        INNER JOIN  gateway
        ON gateway.GW_ID = slave.SLAVE_GW_ID 
        WHERE gateway.GW_GROUP_ID = ?  AND SLAVE_TYPE_CONTINUE="0"
        ORDER BY GW_NUMBER , SLAVE_NUMBER
        ;');
        $sth->bindValue(1, $usergroupid, PDO::PARAM_STR);
        //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
        // 実行
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      } catch (Exception $e) {
        error_output($e->getMessage());
        throw $e;
    }
    return $result;
  }

  //グループ内子機IDと最新アップリンク時刻取得
  public static function readdbgroupslaveuplastlink($usergroupid) {
    $dbh = self::dbPDO();
      try {
        $sth = $dbh->prepare('
        SELECT  SLAVEU_ID,MAX(SLAVEU_DATE)
        FROM slaveupdata 
        WHERE SLAVEU_ID = ANY (SELECT SLAVE_ID FROM slave 
                                INNER JOIN  gateway
                                ON gateway.GW_ID = slave.SLAVE_GW_ID 
                                WHERE gateway.GW_GROUP_ID = ?
                                ORDER BY GW_NUMBER , SLAVE_NUMBER)
                        
          GROUP BY SLAVEU_ID       
        ;');
        $sth->bindValue(1, $usergroupid, PDO::PARAM_STR);
        //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
        // 実行
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      } catch (Exception $e) {
        error_output($e->getMessage());
          throw $e;
    }
    return $result;
  }

   //グループ内子機IDと最新アップリンク時刻よりアップリンクリスト取得
   public static function readlastuplinkdatalist($id,$lasttaime) {
    $dbh = self::dbPDO();
    try {
        $sth = $dbh->prepare('
        SELECT *
        FROM slaveupdata WHERE SLAVEU_ID = ? ORDER BY SLAVEU_DATE DESC limit 1
        ;');
     
        $sth->bindValue(1, $id, PDO::PARAM_STR);
        $sth->bindValue(2, $lasttaime, PDO::PARAM_STR);
        // 実行
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  //自動設定の書き込み
  public static function writedbslaveautosetlog($id, $uid, $values, $setat) {
    $dbh = self::dbPDO();
   
    $sth = $dbh->prepare('
    INSERT INTO slaveautosetlog (
      SLAVE_AUTO_SET_ID, SLAVE_AUTO_SET_AT, SLAVE_AUTO_SET_REMARKS,
      SLAVE_AUTO_SET_WATER, SLAVE_AUTO_SET_UP_LIM_WATER , 
      SLAVE_AUTO_SET_LO_LIM_WATER , SLAVE_AUTO_SET_WATER_DEADLINE, 
      SLAVE_AUTO_SET_TEMP_LIM_AL, SLAVE_AUTO_SET_TEMP_LIM_AL,
      SLAVE_AUTO_SET_HIGH_TEMP_LIM, SLAVE_AUTO_SET_UPDATE_ID 
    )
    VALUES(
      :SLAVE_AUTO_SET_ID, :SLAVE_AUTO_SET_AT, :SLAVE_AUTO_SET_REMARKS,
      :SLAVE_AUTO_SET_WATER, :SLAVE_AUTO_SET_UP_LIM_WATER , 
      :SLAVE_AUTO_SET_LO_LIM_WATER , :SLAVE_AUTO_SET_WATER_DEADLINE, 
      :SLAVE_AUTO_SET_TEMP_LIM_AL, :SLAVE_AUTO_SET_TEMP_LIM_AL,
      :SLAVE_AUTO_SET_HIGH_TEMP_LIM, :SLAVE_AUTO_SET_UPDATE_ID 
    )'
    );
    $dbh ->beginTransaction();
    try {
      $sth->bindValue(':SLAVE_AUTO_SET_ID', $id);
      $sth->bindValue(':SLAVE_AUTO_SET_AT', $setat);
      $sth->bindValue(':SLAVE_AUTO_SET_REMARKS', "");
      $sth->bindValue(':SLAVE_AUTO_SET_WATER', $values[0]);
      $sth->bindValue(':SLAVE_AUTO_SET_UP_LIM_WATER', $values[1]);
      $sth->bindValue(':SLAVE_AUTO_SET_LO_LIM_WATER', $values[2]);
      $sth->bindValue(':SLAVE_AUTO_SET_WATER_DEADLINE', $values[3]);
      $sth->bindValue(':SLAVE_AUTO_SET_TEMP_LIM_AL', $values[4]);
      $sth->bindValue(':SLAVE_AUTO_SET_TEMP_LIM_AL', $values[5]);
      $sth->bindValue(':SLAVE_AUTO_SET_HIGH_TEMP_LIM', $values[6]);
      $sth->bindValue(':SLAVE_AUTO_SET_UPDATE_ID', $values[7]);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }
    //自動設定の読込
  public static function readdbautoslavelist($id) {
      $dbh = self::dbPDO();
      try {
          $sth = $dbh->prepare('
            SELECT * FROM slaveautosetlog 
            WHERE SLAVE_AUTO_SET_ID = ? 
            ORDER BY SLAVE_AUTO_SET_AT DESC limit 1
           ;');
          $sth->bindValue(1, $id, PDO::PARAM_STR);
          //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
          // 実行
          $sth->execute();
          $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      } catch (Exception $e) {
        error_output($e->getMessage());
        throw $e;
    }
    return $result;
  }

  //ゲート内子機の最新アップリンクデータ一時刻覧
  public static function readdbgwslaveuplastlink($gwid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT  SLAVEU_ID ,MAX(SLAVEU_DATE)
      FROM slaveupdata 
      WHERE SLAVEU_ID =
      ANY (SELECT SLAVE_ID FROM slave 
        WHERE SLAVE_GW_ID = ?
        ORDER BY SLAVE_NUMBER)
      GROUP BY SLAVEU_ID  
      ;');
      $sth->bindValue(1, $gwid, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  //GW内子機IDと最新アップリンク時刻よりアップリンクリスト取得
  public static function readlastuplinkdatalist2($id, $lasttaime) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM slaveupdata WHERE SLAVEU_ID = ? ORDER BY SLAVEU_DATE DESC limit 1
      ;');

      $sth->bindValue(1, $id, PDO::PARAM_STR);
      $sth->bindValue(2, $lasttaime, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  //GW内子機IDの最新アップリンクリスト取得   
  public static function readlastuplinkdatalist3($id) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT SLAVE_ID,SLAVE_GW_ID,SLAVE_NUMBER,SLAVEU_DATE,
             SLAVEU_BAT,SLAVEU_SUIIDATA,SLAVEU_TEMPDATA,SLAVEU_OPCLSTATE,
             SLAVE_SET_STADARD,SLAVEU_LASTSUIIDATA,SLAVEU_SUIIDATAV,SLAVEU_SUIIDATALV,
             SLAVEU_STATE
      FROM slave left outer join slaveupdata 
      ON slave.SLAVE_ID = slaveupdata.SLAVEU_ID 
      left outer join slavestandardvlog ON 
      slaveupdata.SLAVEU_ID =  slavestandardvlog.SLAVE_SET_ID
      WHERE SLAVE_ID = ? ORDER BY SLAVEU_DATE DESC limit 1
      ;');
  
      $sth->bindValue(1, $id, PDO::PARAM_STR);
      //$sth->bindValue(2, $lasttaime, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output("readlastuplinkdatalist3".$e->getMessage());
      throw $e;
    }
    return $result;
  }

  //CSRF取得
  public static function readcsrf($uuid) {
    $dbh = self::dbPDO();
    try {
        $sth = $dbh->prepare('
        SELECT *
        FROM csrf
        WHERE CSRF_ID = ? 
        ;');
    
        $sth->bindValue(1, $uuid, PDO::PARAM_STR);
        //$sth->bindValue(2, $lasttaime, PDO::PARAM_STR);
        // 実行
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  //CSRF書き込み
  public static function writecsrf($uuid, $csrf) {
    $nowdatetime=date("Y/m/d H:i:s");
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM csrf
      WHERE CSRF_ID = ? 
      ;');
  
      $sth->bindValue(1, $uuid, PDO::PARAM_STR);
      //$sth->bindValue(2, $lasttaime, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }

    if(count($result)>0){
      $dbh = self::dbPDO();
      
      $sth = $dbh->prepare('
        UPDATE csrf SET CSRF_DATE = :CSRF_DATE, CSRF_CD = :CSRF_CD, CSRF_REMARKS= :CSRF_REMARKS WHERE CSRF_ID = :CSRF_ID
        ' );

      $dbh ->beginTransaction();
      try { 
        $sth->bindValue(':CSRF_ID', $uuid);
        $sth->bindValue(':CSRF_DATE', $nowdatetime);
        $sth->bindValue(':CSRF_CD', $csrf);
        $sth->bindValue(':CSRF_REMARKS', "");

        // 実行
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        $dbh->commit();
      } catch (Exception $e) {
        error_output($e->getMessage());
          $dbh->rollBack();
          throw $e;
      }
    } else{
      $dbh = self::dbPDO();
      
      $sth = $dbh->prepare('
      INSERT INTO csrf(
        CSRF_ID, CSRF_DATE, CSRF_CD, CSRF_REMARKS 
      )
      VALUES(
        :CSRF_ID, :CSRF_DATE, :CSRF_CD, :CSRF_REMARKS 
      )'
      );
      $dbh ->beginTransaction();
      try { 
        $sth->bindValue(':CSRF_ID', $uuid);
        $sth->bindValue(':CSRF_DATE', $nowdatetime);
        $sth->bindValue(':CSRF_CD', $csrf);
        $sth->bindValue(':CSRF_REMARKS', "");

        // 実行
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        $dbh->commit();
      } catch (Exception $e) {
        error_output($e->getMessage());
          $dbh->rollBack();
          throw $e;
      }
      return;
    }
  }

  //大グループ内の小グループ数情報取得 
  public static function readdbmingroupg($usergroupid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT COUNT(GROUP_ID)
      FROM hydrant.group
      WHERE GROUP_IN_GROUP = ? AND GROUP_TYPE_CONTINUE= "0"
      ;');
    
      $sth->bindValue(1, $usergroupid, PDO::PARAM_STR);
      //$sth->bindValue(2, $lasttaime, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  //大グループ内の小グループ情報取得
  public static function readdbmingroupdata($usergroupid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM hydrant.group
      WHERE GROUP_IN_GROUP = ? AND GROUP_TYPE_CONTINUE= "0"
      ORDER BY GROUP_NO
      ;');
    
      $sth->bindValue(1, $usergroupid, PDO::PARAM_STR);
      //$sth->bindValue(2, $lasttaime, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  //指定日のデータ取得
  public static function readdbslaveupdaydata2($id,$date) {

    $date1=$date->modify('-1 day');
    $date2=$date->modify('+1 day');
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM slaveupdate
      WHERE SLAVEU_ID = ? and SLAVEU_DATE between ? and ?
      ORDER BY SLAVEU_DATE
      ;');
    
      $sth->bindValue(1, $id, PDO::PARAM_STR);
      $sth->bindValue(2, $date1, PDO::PARAM_STR);
      $sth->bindValue(3, $date2, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  //子機idより子機情報取得
  public static function readdbslaveinfo($id) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM slave
      WHERE SLAVE_ID = ? AND SLAVE_TYPE_CONTINUE = "0"
      ;');
    
      $sth->bindValue(1, $id, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function readdbdata_dates($id) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT DATE_FORMAT(SLAVEU_DATE,"%Y-%m-%d")
      as grouping_column
      FROM slaveupdata WHERE SLAVEU_ID = ?
      GROUP BY DATE_FORMAT(SLAVEU_DATE,"%Y-%m-%d")
      ORDER BY grouping_column DESC
      ;');
    
      $sth->bindValue(1, $id, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  //指定日のデータ取得
  public static function readdbslaveupdaydata($id, $date) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT SLAVE_ID,SLAVE_GW_ID,SLAVE_NUMBER,SLAVEU_DATE,
      SLAVEU_BAT,SLAVEU_SUIIDATA,SLAVEU_TEMPDATA,SLAVEU_OPCLSTATE,
      SLAVE_SET_STADARD,SLAVEU_LASTSUIIDATA,SLAVEU_SUIIDATAV,SLAVEU_SUIIDATALV,
      SLAVEU_STATE
      FROM slave left outer join slaveupdata 
      ON slave.SLAVE_ID = slaveupdata.SLAVEU_ID 
      left outer join slavestandardvlog ON 
      slaveupdata.SLAVEU_ID =  slavestandardvlog.SLAVE_SET_ID
      WHERE SLAVE_ID = ? and DATE_FORMAT(SLAVEU_DATE,"%Y-%m-%d") = ?
      ;');
    
      $sth->bindValue(1, $id, PDO::PARAM_STR);
      $sth->bindValue(2, $date, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output("readdbslaveupdaydata".$e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function readdbslaveavelist($id, $date) {
    $date3=new DateTime($date);
    $date4=new DateTime($date);
    $date3=date_modify($date3, "-6 day");
    $date1=$date3->format('Y-m-d');
    $date4=date_modify($date4, "+1 day");
    $date2=$date4->format('Y-m-d');
    //$date1= DATE_FORMAT($date,"%Y-%m-%d");

    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT date(SLAVEU_DATE) as date
      , AVG(SLAVEU_SUIIDATA) as avglevel
      , AVG(SLAVEU_TEMPDATA) as avgtemp
      FROM hydrant.slaveupdata
      WHERE SLAVEU_ID = ? AND SLAVEU_DATE BETWEEN ?
        AND ?
      GROUP BY date;
      ');
    
      $sth->bindValue(1, $id, PDO::PARAM_STR);
      $sth->bindValue(2, $date1, PDO::PARAM_STR);
      $sth->bindValue(3, $date2, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output("readdbslaveavelist".$e->getMessage());
      throw $e;
    }
    return $result;
  }

  //子機idより周期設定情報取得
  public static function readdbslavepisetlog($id) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM slavepisetlog
      WHERE SLAVE_PI_ID = ?
      ORDER BY SLAVE_PI_AT DESC limit 1
      ;');
    
      $sth->bindValue(1, $id, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }

    // if (empty($result)) {
    //   dbmgr::writedbslavepisetlog($id, 3, 0, 30);

    //   try {
    //     $sth = $dbh->prepare('
    //     SELECT *
    //     FROM slavepisetlog
    //     WHERE SLAVE_PI_ID = ?
    //     ORDER BY SLAVE_PI_AT DESC limit 1
    //     ;');
      
    //     $sth->bindValue(1, $id, PDO::PARAM_STR);
    //     // 実行
    //     $sth->execute();
    //     $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    //   } catch (Exception $e) {
    //     error_output($e->getMessage());
    //     throw $e;
    //   }
    // }

    return $result;
  }

  //周期設定の新規書き込み
  public static function writedbslavepisetlog($id, $pr, $idhour, $idmin) {
    $uid="";//$_SESSION['userid'];
    $i1=0;
    $i2=0;
    $i3=1;
    $ddate=date('Y-m-d H:i:s');

    $dbh = self::dbPDO();
   
    $sth = $dbh->prepare('
    INSERT INTO slavepisetlog (
      SLAVE_PI_ID,SLAVE_PI_AT,SLAVE_PI_REMARKS,
      SLAVE_PI_ALERT,SLAVE_PI_KANNRI_TIMEH,SLAVE_PI_KANNRI_TIMEM,
      SLAVE_PI_PR,SLAVE_PI_UPLINK_NO,
      SLAVE_PI_ORDER_NO,SLAVE_PI_SET_END,SLAVE_PI_SET_UPDATE_ID 
    )
    VALUES(
      :SLAVE_PI_ID, :SLAVE_PI_AT, :SLAVE_PI_REMARKS,
      :SLAVE_PI_ALERT, :SLAVE_PI_KANNRI_TIMEH, 
      :SLAVE_PI_KANNRI_TIMEM, :SLAVE_PI_PR, 
      :SLAVE_PI_UPLINK_NO,
      :SLAVE_PI_ORDER_NO,:SLAVE_PI_SET_END,:SLAVE_PI_SET_UPDATE_ID 
    )'
    );

    $dbh ->beginTransaction();
    try {
      $sth->bindValue(':SLAVE_PI_ID', $id);
      $sth->bindValue(':SLAVE_PI_AT', $ddate);
      $sth->bindValue(':SLAVE_PI_REMARKS', "");
      $sth->bindValue(':SLAVE_PI_ALERT', $i1);
      $sth->bindValue(':SLAVE_PI_KANNRI_TIMEH', $idhour);
      $sth->bindValue(':SLAVE_PI_KANNRI_TIMEM', $idmin);
      $sth->bindValue(':SLAVE_PI_PR', $pr);
      $sth->bindValue(':SLAVE_PI_UPLINK_NO', $i1);
      $sth->bindValue(':SLAVE_PI_ORDER_NO', $i2);
      $sth->bindValue(':SLAVE_PI_SET_END', $i3);
      $sth->bindValue(':SLAVE_PI_SET_UPDATE_ID', $uid);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  //周期設定の/時間バージョンアップ-完了を1優先淳入力
  public static function  upenddbslavepisetlog($id,$idhour,$idmin,$pr){
    $uid=$_SESSION['userid'];
    //$uid="0033";
    $ddate=date('Y-m-d H:i:s');
    $dbh = self::dbPDO();
   
    $sth = $dbh->prepare('
      UPDATE slavepisetlog
      SET SLAVE_PI_AT = :SLAVE_PI_AT,
          SLAVE_PI_PR = :SLAVE_PI_PR,
          SLAVE_PI_KANNRI_TIMEH = :SLAVE_PI_KANNRI_TIMEH,
          SLAVE_PI_KANNRI_TIMEM = :SLAVE_PI_KANNRI_TIMEM,
          SLAVE_PI_SET_END = 1,
          SLAVE_PI_SET_UPDATE_ID = :SLAVE_PI_SET_UPDATE_ID
        WHERE  SLAVE_PI_ID = :id'
    );

    $dbh ->beginTransaction();
    try {
      $sth->bindParam( ':id', $id, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_PI_AT', $ddate, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_PI_PR', $pr, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_PI_KANNRI_TIMEH', $idhour, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_PI_KANNRI_TIMEM', $idmin, PDO::PARAM_INT);
//       $sth->bindParam( ':SLAVE_PI_SET_END', 1, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_PI_SET_UPDATE_ID', $uid, PDO::PARAM_STR);
      
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }
  //周期設定の時間バージョンアップ-優先順位をアップ
  public static function  upprddbslavepisetlog($id, $pr, $idhour, $idmin) {
    $uid = $_SESSION['userid'];
    //$uid="0033";
    $ddate=date('Y-m-d H:i:s');
    $dbh = self::dbPDO();

    $sth = $dbh->prepare('
      UPDATE slavepisetlog
      SET SLAVE_PI_AT = :SLAVE_PI_AT,
          SLAVE_PI_PR = :SLAVE_PI_PR,
          SLAVE_PI_KANNRI_TIMEH = :SLAVE_PI_KANNRI_TIMEH,
          SLAVE_PI_KANNRI_TIMEM = :SLAVE_PI_KANNRI_TIMEM,
          SLAVE_PI_SET_UPDATE_ID = :SLAVE_PI_SET_UPDATE_ID
      WHERE  SLAVE_PI_ID = :id'
    );

    $dbh ->beginTransaction();
    try {
      $sth->bindParam( ':id', $id, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_PI_AT', $ddate, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_PI_PR', $pr, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_PI_KANNRI_TIMEH', $idhour, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_PI_KANNRI_TIMEM', $idmin, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_PI_SET_UPDATE_ID', $uid, PDO::PARAM_STR);
      
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  //周期設定判定後のバージョンアップ
  public static function upprddbslavepisetlog_after($id, $uplinkno, $orderflag, $alertflag, $orderno) {
    $uid = "AUTO";
    //$uid="0033";
    $ddate = date('Y-m-d H:i:s');
    $dbh = self::dbPDO();

    $sth = $dbh->prepare('
      UPDATE slavepisetlog
      SET SLAVE_PI_AT = :SLAVE_PI_AT,
          SLAVE_PI_UPLINK_NO = :SLAVE_PI_UPLINK_NO,
          SLAVE_PI_SET_END = :SLAVE_PI_SET_END,
          SLAVE_PI_ORDER_NO = :SLAVE_PI_ORDER_NO,
          SLAVE_PI_ALERT = :SLAVE_PI_ALERT,
          SLAVE_PI_SET_UPDATE_ID = :SLAVE_PI_SET_UPDATE_ID
      WHERE  SLAVE_PI_ID = :id'
    );

    $dbh->beginTransaction();
    try {
      $sth->bindParam( ':id', $id, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_PI_AT', $ddate, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_PI_UPLINK_NO', $uplinkno, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_PI_SET_END',$orderflag, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_PI_ORDER_NO', $orderno, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_PI_ALERT', $alertflag, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_PI_SET_UPDATE_ID', $uid, PDO::PARAM_STR);
      
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  //子機idより開閉設定情報取得
  public static function readdbslavesetlog($id) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM slavesetlog
      WHERE SLAVE_SET_ID = ?
      ;');

      $sth->bindValue(1, $id, PDO::PARAM_STR);

      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function writedbslavesetlog1($id, $pr, $ctrl) {
    $i1 = 1;
    $uid = $_SESSION['userid'];
    $ddate = date('Y-m-d H:i:s');
    $dbh = self::dbPDO();

    $sth = $dbh->prepare('
      INSERT INTO slavesetlog (
        SLAVE_SET_ID,SLAVE_SET_AT,SLAVE_SET_REMARKS,
        SLAVE_SET1OFFON,SLAVE_SET1CLOSEOPEN,
        SLAVE_SET_PR,SLAVE_SET_END,SLAVE_SET_UPDATE_ID 
      )
      VALUES(
        :SLAVE_SET_ID, :SLAVE_SET_AT, :SLAVE_SET_REMARKS,
        :SLAVE_SET1OFFON, :SLAVE_SET1CLOSEOPEN, 
        :SLAVE_SET_PR,:SLAVE_SET_END,:SLAVE_SET_UPDATE_ID 
      )'
    );

    $dbh ->beginTransaction();
    try { 
      $sth->bindValue(':SLAVE_SET_ID', $id);
      $sth->bindValue(':SLAVE_SET_AT', $ddate);
      $sth->bindValue(':SLAVE_SET_REMARKS', "");
      $sth->bindValue(':SLAVE_SET1OFFON', $i1);
      $sth->bindValue(':SLAVE_SET1CLOSEOPEN', $ctrl);
      $sth->bindValue(':SLAVE_SET_PR', $pr);
      $sth->bindValue(':SLAVE_SET_END', $i1);
      $sth->bindValue(':SLAVE_SET_UPDATE_ID', $uid);

      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  // //時間開閉セットをoffにする。
  public static function upenddbslavesetlogtimeoff($id) {
    $uid = $_SESSION['userid'];
    $i1 = 1;
    $ddate = date('Y-m-d H:i:s');
    $dbh = self::dbPDO();
    
      $sth = $dbh->prepare('
        UPDATE slavesetlog
        SET SLAVE_SET_AT = :SLAVE_SET_AT,
        SLAVE_SET4OFFON = 0,
        SLAVE_SET5OFFON = 0,
        SLAVE_SET6OFFON = 0,
        SLAVE_SET7OFFON = 0,
        SLAVE_SET8OFFON = 0,
        SLAVE_SET9OFFON = 0,
        SLAVE_SET_UPDATE_ID = :SLAVE_SET_UPDATE_ID
        WHERE  SLAVE_SET_ID = :id'
      );

    $dbh ->beginTransaction();
    try {
      $sth->bindParam( ':id', $id, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_SET_AT', $ddate, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_SET_UPDATE_ID', $uid, PDO::PARAM_STR);

      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  //開閉設定の/開閉バージョンアップ-完了を1
  public static function  upenddbslavesetlog($id, $ctrl, $pr) {
    $uid = $_SESSION['userid'];
    $i1 = 1;
    $ddate = date('Y-m-d H:i:s');
    $dbh = self::dbPDO();
   
    $sth = $dbh->prepare('
      UPDATE slavesetlog
      SET SLAVE_SET_AT = :SLAVE_SET_AT,
        SLAVE_SET1OFFON = :SLAVE_SET1OFFON,
        SLAVE_SET1CLOSEOPEN = :SLAVE_SET1CLOSEOPEN,
        SLAVE_SET_END = :SLAVE_SET_END,
        SLAVE_SET_PR = :SLAVE_SET_PR,
        SLAVE_SET_UPDATE_ID = :SLAVE_SET_UPDATE_ID
        WHERE  SLAVE_SET_ID = :id' 
    );

    $dbh ->beginTransaction();
    try { 
      $sth->bindParam(':id', $id, PDO::PARAM_STR);
      $sth->bindParam(':SLAVE_SET_AT', $ddate, PDO::PARAM_STR);
      $sth->bindParam(':SLAVE_SET1OFFON', $i1, PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET1CLOSEOPEN', $ctrl, PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET_END', $i1, PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET_PR', $pr, PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET_UPDATE_ID', $uid, PDO::PARAM_STR);
          // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  //開閉設定の開閉バージョンアップ-優先順位をアップ
  public static function  upprddbslavesetlog($id, $pr, $ctrl) {
    $uid = $_SESSION['userid'];
    $ddate = date('Y-m-d H:i:s');
    $dbh = self::dbPDO();
   
    $sth = $dbh->prepare('
      UPDATE slavesetlog
      SET SLAVE_SET_AT = :SLAVE_SET_AT,
          SLAVE_SET_PR = :SLAVE_SET_PR,
          SLAVE_SET1OFFON = 1,
          SLAVE_SET1CLOSEOPEN = :SLAVE_SET1CLOSEOPEN,
          SLAVE_SET_UPDATE_ID = :SLAVE_SET_UPDATE_ID
      WHERE  SLAVE_SET_ID = :id'
    );

    $dbh ->beginTransaction();
    try {
      $sth->bindParam(':id',                  $id,    PDO::PARAM_STR);
      $sth->bindParam(':SLAVE_SET_AT',        $ddate, PDO::PARAM_STR);
      $sth->bindParam(':SLAVE_SET_PR',        $pr,    PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET1CLOSEOPEN', $ctrl,  PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET_UPDATE_ID', $uid,   PDO::PARAM_STR);
      
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  public static function writedbslavesetlog4($id, $pr, $onoff, $time, $ctrl, $period, $span) {
    $time3 = new DateTime($time);
    $time3 = date_modify($time3, "+{$span} hour");
    $time1 = $time3->format('Y-m-d H:i:s');
    
    if ($ctrl == 0) {
      $ctrl1 = 1;
    } else {
      $ctrl1 = 0;
    }

    $afteronoff = $onoff;
    if ($period == 0) {
      $afteronoff = 0;
    }

    $ddate = date('Y-m-d H:i:s');

    $uid = $_SESSION['userid'];
    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
      INSERT INTO slavesetlog (
        SLAVE_SET_ID,SLAVE_SET4_AT,SLAVE_SET_REMARKS,
        SLAVE_SET4OFFON,SLAVE_SET4TIME,SLAVE_SET4CLOSEOPEN,
        SLAVE_SET4_LIMIT,
        SLAVE_SET8_AT,
        SLAVE_SET8OFFON,SLAVE_SET8TIME,SLAVE_SET8CLOSEOPEN
        SLAVE_SET_PR,SLAVE_SET_END,SLAVE_SET_UPDATE_ID,
      )
      VALUES(
        :SLAVE_SET_ID, :SLAVE_SET4_AT, :SLAVE_SET_REMARKS,
        :SLAVE_SET4OFFON, :SLAVE_SET4TIME, :SLAVE_SET4CLOSEOPEN,
        :SLAVE_SET4_LIMIT,
        :SLAVE_SET8_AT,
        :SLAVE_SET8OFFON, :SLAVE_SET8TIME, :SLAVE_SET8CLOSEOPEN, 
        :SLAVE_SET_PR,:SLAVE_SET_END,:SLAVE_SET_UPDATE_ID 
      )'
    );

    $dbh->beginTransaction();
    try {
      $sth->bindValue(':SLAVE_SET_ID',        $id);
      $sth->bindValue(':SLAVE_SET4_AT',       $ddate);
      $sth->bindValue(':SLAVE_SET_REMARKS',   "");
      $sth->bindValue(':SLAVE_SET4OFFON',     $onoff);
      $sth->bindValue(':SLAVE_SET4TIME',      $time);
      $sth->bindValue(':SLAVE_SET4CLOSEOPEN', $ctrl);
      $sth->bindValue(':SLAVE_SET4_LIMIT',    $period);
      $sth->bindValue(':SLAVE_SET8_AT',       $ddate);
      $sth->bindValue(':SLAVE_SET8OFFON',     $afteronoff);
      $sth->bindValue(':SLAVE_SET8TIME',      $time1);
      $sth->bindValue(':SLAVE_SET8CLOSEOPEN', $ctrl1);
      $sth->bindValue(':SLAVE_SET_PR',        $pr);
      $sth->bindValue(':SLAVE_SET_END',       $onoff);
      $sth->bindValue(':SLAVE_SET_UPDATE_ID', $uid);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  public static function writedbslavesetlog5($id, $pr, $onoff, $time, $ctrl, $period) {
    $time3 = new DateTime($time);
    $time3 = date_modify($time3, "+{$period} hour");
    $time1 = $time3->format('Y-m-d H:i:s');
    if ($ctrl == 0) {
      $ctrl1 = 1;
    } else {
      $ctrl1 = 0;
    }

    $afteronoff = $onoff;
    if ($period == 0) {
      $afteronoff = 0;
    }

    $ddate = date('Y-m-d H:i:s');

    $uid = $_SESSION['userid'];
    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
      INSERT INTO slavesetlog (
        SLAVE_SET_ID,SLAVE_SET5_AT,SLAVE_SET_REMARKS,
        SLAVE_SET5OFFON,SLAVE_SET5TIME,SLAVE_SET5CLOSEOPEN,
        SLAVE_SET5_LIMIT,
        SLAVE_SET9_AT,
        SLAVE_SET9OFFON,SLAVE_SET9TIME,SLAVE_SET9CLOSEOPEN
        SLAVE_SET_PR,SLAVE_SET_END,SLAVE_SET_UPDATE_ID        
      )
      VALUES(
        :SLAVE_SET_ID, :SLAVE_SET5_AT, :SLAVE_SET_REMARKS,
        :SLAVE_SET5OFFON, :SLAVE_SET5TIME, :SLAVE_SET5CLOSEOPEN,
        :SLAVE_SET5_LIMIT,
        :SLAVE_SET9_AT,
        :SLAVE_SET9OFFON, :SLAVE_SET9TIME, :SLAVE_SET9CLOSEOPEN, 
        :SLAVE_SET_PR,:SLAVE_SET_END,:SLAVE_SET_UPDATE_ID    
      )'
    );

    $dbh->beginTransaction();
    try {
      $sth->bindValue(':SLAVE_SET_ID',        $id);
      $sth->bindValue(':SLAVE_SET5_AT',       $ddate);
      $sth->bindValue(':SLAVE_SET_REMARKS',   "");
      $sth->bindValue(':SLAVE_SET5OFFON',     $onoff);
      $sth->bindValue(':SLAVE_SET5TIME',      $time);
      $sth->bindValue(':SLAVE_SET5CLOSEOPEN', $ctrl);
      $sth->bindValue(':SLAVE_SET5_LIMIT',    $ctrl);
      $sth->bindValue(':SLAVE_SET9_AT',       $ddate);
      $sth->bindValue(':SLAVE_SET9OFFON',     $afteronoff);
      $sth->bindValue(':SLAVE_SET9TIME',      $time1);
      $sth->bindValue(':SLAVE_SET9CLOSEOPEN', $ctrl1);
      $sth->bindValue(':SLAVE_SET_PR',        $pr);
      $sth->bindValue(':SLAVE_SET_END',       $onoff);
      $sth->bindValue(':SLAVE_SET_UPDATE_ID', $uid);

      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();

    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  public static function writedbslavesetlog6($id, $onoff, $pr, $up, $down, $time) {
    $uid = $_SESSION['userid'];
    $ddate = date('Y-m-d H:i:s');
    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
      INSERT INTO slavesetlog (
        SLAVE_SET_ID,SLAVE_SET6_AT,SLAVE_SET_REMARKS,
        SLAVE_SET6OFFON,SLAVE_SET6UP,SLAVE_SET6DOWN,SLAVE_SET6TIME,
        SLAVE_SET_PR,SLAVE_SET_END,SLAVE_SET_UPDATE_ID        
      )
      VALUES(
        :SLAVE_SET_ID, :SLAVE_SET6_AT, :SLAVE_SET_REMARKS,
        :SLAVE_SET6OFFON, :SLAVE_SET6UP, :SLAVE_SET6DOWN, :SLAVE_SET6TIME, 
        :SLAVE_SET_PR,:SLAVE_SET_END,:SLAVE_SET_UPDATE_ID    
      )'
    );

    $dbh->beginTransaction();
    try {
      $sth->bindValue(':SLAVE_SET_ID', $id);
      $sth->bindValue(':SLAVE_SET6_AT', $ddate);
      $sth->bindValue(':SLAVE_SET_REMARKS', "");
      $sth->bindValue(':SLAVE_SET6OFFON', $onoff);
      $sth->bindValue(':SLAVE_SET6UP', $up);
      $sth->bindValue(':SLAVE_SET6DOWN', $down);
      $sth->bindValue(':SLAVE_SET6TIME', $time);
      $sth->bindValue(':SLAVE_SET_PR', $pr);
      $sth->bindValue(':SLAVE_SET_END', $onoff);
      $sth->bindValue(':SLAVE_SET_UPDATE_ID', $uid);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  public static function writedbslavesetlog7($id, $onoff, $pr, $up, $down, $time) {
    
    $uid = $_SESSION['userid'];
    $ddate = date('Y-m-d H:i:s');
    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
    INSERT INTO slavesetlog (
      SLAVE_SET_ID,SLAVE_SET7_AT,SLAVE_SET_REMARKS,
      SLAVE_SET7OFFON,SLAVE_SET7UP,SLAVE_SET7DOWN,SLAVE_SET7TIME,
      SLAVE_SET_PR,SLAVE_SET_END,SLAVE_SET_UPDATE_ID        
    )
    VALUES(
      :SLAVE_SET_ID, :SLAVE_SET7_AT, :SLAVE_SET_REMARKS,
      :SLAVE_SET7OFFON, :SLAVE_SET7UP, :SLAVE_SET7DOWN, :SLAVE_SET7TIME, 
      :SLAVE_SET_PR,:SLAVE_SET_END,:SLAVE_SET_UPDATE_ID    
    )'
    );

    $dbh->beginTransaction();
    try {
      $sth->bindValue(':SLAVE_SET_ID', $id);
      $sth->bindValue(':SLAVE_SET7_AT', $ddate);
      $sth->bindValue(':SLAVE_SET_REMARKS', "");
      $sth->bindValue(':SLAVE_SET7OFFON', $onoff);
      $sth->bindValue(':SLAVE_SET7UP', $up);
      $sth->bindValue(':SLAVE_SET7DOWN', $down);
      if ($time == "NULL") {
        $sth->bindValue(':SLAVE_SET7TIME', NULL);
      } else {
        $sth->bindValue(':SLAVE_SET7TIME', $time);
      }
      $sth->bindValue(':SLAVE_SET_PR', $pr);
      $sth->bindValue(':SLAVE_SET_END', $onoff);
      $sth->bindValue(':SLAVE_SET_UPDATE_ID', $uid);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output("writedbslavesetlog7".$e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  public static function upvdbslavesetlog4($id, $pr, $onoff, $time, $ctrl, $period, $span) {
    $roottime = new DateTime($time);
    $time3 = date_modify($roottime, "+{$span} day");
    $time1 = $time3->format('Y-m-d H:i:s');
    if ($ctrl == 0) {
      $ctrl1 = 1;
    } else {
      $ctrl1 = 0;
    }

    $afteronoff = $onoff;
    if ($period == 0) {
      $afteronoff = 0;
    }

    $ddate = date('Y-m-d H:i:s');

    $uid = $_SESSION['userid'];
    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
      UPDATE slavesetlog
      SET
        SLAVE_SET4_AT       = :SLAVE_SET4_AT,
        SLAVE_SET4OFFON     = :SLAVE_SET4OFFON,
        SLAVE_SET4CLOSEOPEN = :SLAVE_SET4CLOSEOPEN,
        SLAVE_SET4TIME      = :SLAVE_SET4TIME,
        SLAVE_SET8_AT       = :SLAVE_SET8_AT,
        SLAVE_SET8OFFON     = :SLAVE_SET8OFFON,
        SLAVE_SET8CLOSEOPEN = :SLAVE_SET8CLOSEOPEN,
        SLAVE_SET8TIME      = :SLAVE_SET8TIME,
        SLAVE_SET4_LIMIT    = :SLAVE_SET4_LIMIT,
        
        SLAVE_SET_UPDATE_ID = :SLAVE_SET_UPDATE_ID
      WHERE  SLAVE_SET_ID = :id'
    );

    $dbh->beginTransaction();
    try {
      $sth->bindParam(':id',                  $id,         PDO::PARAM_STR);
      $sth->bindParam(':SLAVE_SET4_AT',       $ddate,      PDO::PARAM_STR);
      $sth->bindParam(':SLAVE_SET4OFFON',     $onoff,      PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET4CLOSEOPEN', $ctrl,       PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET4TIME',      $time,       PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET8_AT',       $ddate,      PDO::PARAM_STR);
      $sth->bindParam(':SLAVE_SET8OFFON',     $afteronoff, PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET8CLOSEOPEN', $ctrl1,      PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET8TIME',      $time1,      PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET4_LIMIT',    $period,     PDO::PARAM_STR);
      $sth->bindParam(':SLAVE_SET_UPDATE_ID', $uid,        PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  public static function upvdbslavesetlog5($id, $pr, $onoff, $time, $ctrl, $period, $span) {
    $roottime = new DateTime($time);
    //$time3 = date_modify($time3, "+{$period} hour");
    $time3 = date_modify($roottime, "+{$span} day");
    $time1 = $time3->format('Y-m-d H:i:s');
    if ($ctrl == 0) {
      $ctrl1 = 1;
    } else {
      $ctrl1 = 0;
    }

    $afteronoff = $onoff;
    if ($period == 0) {
      $afteronoff = 0;
    }

    $ddate = date('Y-m-d H:i:s');

    $uid = $_SESSION['userid'] ;
    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
      UPDATE slavesetlog
      SET
          SLAVE_SET4OFFON = 0,
          SLAVE_SET5_AT = :SLAVE_SET5_AT,
          SLAVE_SET5OFFON = :SLAVE_SET5OFFON,
          SLAVE_SET5CLOSEOPEN = :SLAVE_SET5CLOSEOPEN,
          SLAVE_SET5TIME = :SLAVE_SET5TIME,
          SLAVE_SET8OFFON = 0,
          SLAVE_SET9_AT = :SLAVE_SET9_AT,
          SLAVE_SET9OFFON = :SLAVE_SET9OFFON,
          SLAVE_SET9CLOSEOPEN = :SLAVE_SET9CLOSEOPEN,
          SLAVE_SET9TIME = :SLAVE_SET9TIME,
          SLAVE_SET5_LIMIT    = :SLAVE_SET5_LIMIT,
          
          SLAVE_SET_UPDATE_ID = :SLAVE_SET_UPDATE_ID
        WHERE  SLAVE_SET_ID = :id'
    );

    $dbh ->beginTransaction();
    try { 
      $sth->bindParam(':id', $id, PDO::PARAM_STR);
      $sth->bindParam(':SLAVE_SET5_AT', $ddate, PDO::PARAM_STR);
      $sth->bindParam(':SLAVE_SET5OFFON', $onoff, PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET5CLOSEOPEN', $ctrl, PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET5TIME', $time, PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET9_AT', $ddate, PDO::PARAM_STR);
      $sth->bindParam(':SLAVE_SET9OFFON', $afteronoff, PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET9CLOSEOPEN', $ctrl1, PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET9TIME', $time1, PDO::PARAM_INT);
      $sth->bindParam(':SLAVE_SET5_LIMIT', $period, PDO::PARAM_STR);
      $sth->bindParam(':SLAVE_SET_UPDATE_ID', $uid, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }  

  public static function upvdbslavesetlog6($id, $onoff, $pr, $up, $down, $time) {
      
    $uid=$_SESSION['userid'] ;
    $ddate=date('Y-m-d H:i:s');
    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
    UPDATE slavesetlog
      SET
          SLAVE_SET6_AT   = :SLAVE_SET6_AT,
          SLAVE_SET6OFFON = :SLAVE_SET6OFFON,
          SLAVE_SET6UP    = :SLAVE_SET6UP,
          SLAVE_SET6DOWN  = :SLAVE_SET6DOWN,
          SLAVE_SET6TIME  = :SLAVE_SET6TIME,
          
          SLAVE_SET_UPDATE_ID = :SLAVE_SET_UPDATE_ID
      WHERE  SLAVE_SET_ID = :id'
      
    );
    
    $dbh->beginTransaction();
    try {
      $sth->bindParam( ':id', $id, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_SET6_AT', $ddate, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_SET6OFFON', $onoff, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_SET6UP', $up, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_SET6DOWN', $down, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_SET6TIME', $time, PDO::PARAM_INT);
        $sth->bindParam( ':SLAVE_SET_UPDATE_ID', $uid, PDO::PARAM_STR);
          // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
    error_output($e->getMessage());
        $dbh->rollBack();
        throw $e;
    }
    return;
  }

  public static function upvdbslavesetlog7($id, $onoff, $pr, $up, $down, $time) {
    
    $uid = $_SESSION['userid'];
    //$uid = "001" ;
    $ddate = date('Y-m-d H:i:s');
    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
    UPDATE slavesetlog
      SET
          SLAVE_SET6OFFON = 0,
          SLAVE_SET7_AT = :SLAVE_SET7_AT,
          SLAVE_SET7OFFON = :SLAVE_SET7OFFON,
          SLAVE_SET7UP = :SLAVE_SET7UP,
          SLAVE_SET7DOWN = :SLAVE_SET7DOWN,
          SLAVE_SET7TIME = :SLAVE_SET7TIME,
          
          SLAVE_SET_UPDATE_ID = :SLAVE_SET_UPDATE_ID
      WHERE  SLAVE_SET_ID = :id'
    );
   
    $dbh->beginTransaction();
    try { 
      $sth->bindParam( ':id', $id, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_SET7_AT', $ddate, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_SET7OFFON', $onoff, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_SET7UP', $up, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_SET7DOWN', $down, PDO::PARAM_INT);

      if ($time == "NULL") {
        $sth->bindParam( ':SLAVE_SET7TIME', NULL, PDO::PARAM_STR);
      } else {
        $sth->bindParam( ':SLAVE_SET7TIME', $time, PDO::PARAM_STR);
      }
      $sth->bindParam( ':SLAVE_SET_UPDATE_ID', $uid, PDO::PARAM_STR);
          // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output("upvdbslavesetlog7".$e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  public static function upprddbslavesetlog_after1($id, $setflag, $uplinkno, $orderflag, $alertflag, $orderno, $pr) {
    $uid = "AUTO";
    //$uid="0033";
    $ddate = date('Y-m-d H:i:s');
    $dbh = self::dbPDO();
   
    $sth = $dbh->prepare('
      UPDATE slavesetlog
      SET
      SLAVE_SET_AT = :SLAVE_SET_AT,
      SLAVE_SET1OFFON = :SLAVE_SET1OFFON,
      SLAVE_SET_UPLINK_NO = :SLAVE_SET_UPLINK_NO,
      SLAVE_SET_END = :SLAVE_SET_END,
      SLAVE_SET_ORDER_NO = :SLAVE_SET_ORDER_NO,
      SLAVE_SET_ALERT = :SLAVE_SET_ALERT,
      SLAVE_SET_UPDATE_ID = :SLAVE_SET_UPDATE_ID,
      SLAVE_SET4OFFON = 0,
      SLAVE_SET5OFFON = 0,
      SLAVE_SET8OFFON = 0,
      SLAVE_SET9OFFON = 0,
      SLAVE_SET_PR = :SLAVE_SET_PR
      WHERE  SLAVE_SET_ID = :id'
      );

    $dbh->beginTransaction();
    try {
       $sth->bindParam( ':id', $id, PDO::PARAM_STR);
       $sth->bindParam( ':SLAVE_SET_AT', $ddate, PDO::PARAM_STR);
       $sth->bindParam( ':SLAVE_SET1OFFON', $setflag, PDO::PARAM_INT);
       $sth->bindParam( ':SLAVE_SET_UPLINK_NO', $uplinkno, PDO::PARAM_INT);
       $sth->bindParam( ':SLAVE_SET_END',$orderflag, PDO::PARAM_INT);
       $sth->bindParam( ':SLAVE_SET_ORDER_NO', $orderno, PDO::PARAM_INT);
       $sth->bindParam( ':SLAVE_SET_ALERT', $alertflag, PDO::PARAM_INT);
       $sth->bindParam( ':SLAVE_SET_UPDATE_ID', $uid, PDO::PARAM_STR);
       $sth->bindParam( ':SLAVE_SET_PR', $pr, PDO::PARAM_INT);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
        $dbh->rollBack();
        throw $e;
    }
    return;
  }

  //開閉制御ログ書き込み
  public static function writedbslaveoc($id, $state, $remark) {
    $ddate = date("Y/m/d H:i:s");
    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
    INSERT INTO slaveoclog (
    SLAVE_OC_ID, SLAVE_OC_AT, SLAVE_OC_STATE, SLAVE_OC_REMARKS 
    )
    VALUES(
    :SLAVE_OC_ID, :SLAVE_OC_AT, :SLAVE_OC_STATE, :SLAVE_OC_REMARKS 
    )'
    );
    $dbh->beginTransaction();
    try {
      $sth->bindValue(':SLAVE_OC_ID', $id);
      $sth->bindValue(':SLAVE_OC_AT', $ddate);
      $sth->bindValue(':SLAVE_OC_STATE', $state);
      $sth->bindValue('SLAVE_OC_REMARKS',$remark);
      
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
    error_output($e->getMessage());
          $dbh->rollBack();
          throw $e;
    }
    return;
  }

  //開閉ログ取得
  public static function readdbslaveoc($id,$date1,$date2) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM slaveoclog WHERE SLAVE_OC_ID = :id AND SLAVE_OC_AT BETWEEN :date1 AND :date2  ORDER BY SLAVE_OC_AT DESC
      ;');
  
      $sth->bindValue(':id', $id);
      $sth->bindValue(':date1', $date1);
      $sth->bindValue(':date2', $date2);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  //最新開閉命令情報取得
  public static function readdbslaveoc2($id) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM slaveoclog WHERE SLAVE_OC_ID = :id
      ORDER BY SLAVE_OC_AT  DESC limit 1
      ;');
  
      $sth->bindValue(':id', $id);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function readlastuplinkdata2($id) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM slaveupdata WHERE SLAVEU_ID = ? ORDER BY SLAVEU_DATE
      ;');
  
      $sth->bindValue(1, $id, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  //全子機情報取得
  public static function readdbslave(){
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT * FROM slave  
      WHERE SLAVE_TYPE_CONTINUE ="0"
      ;');
       
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  //最新アップリンクデータを一つ取得
  public static function readlastdownlinkdata($id) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM slavedowndata WHERE SLAVED_ID = ? ORDER BY SLAVED_DATE DESC limit 1
      ;');
      
      $sth->bindValue(1, $id, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function writedbslavedwon($id, $ctrl, $hour, $minu) {
    $ddate=date("Y/m/d H:i:s");
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      INSERT INTO slavedowndata (
      SLAVED_ID,SLAVED_DATE,
      SLAVED_WTEMP,SLAVED_PERIODH,SLAVED_PERIODM 
        )
      VALUES(
      :SLAVED_ID, :SLAVED_DATE, :SLAVED_WTEMP, :SLAVED_PERIODH, :SLAVED_PERIODM 
      )'
      );
      
      $sth->bindValue(':SLAVED_ID', $id);
      $sth->bindValue(':SLAVED_DATE', $ddate);
      $sth->bindValue(':SLAVED_WTEMP', $ctrl);
      $sth->bindValue(':SLAVED_PERIODH', $hour);
      $sth->bindValue(':SLAVED_PERIODM', $minu);
     
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return;
  }

  public static function upprddbslavesetlog_after2($id, $orderflag) {
    $uid="AUTO";
    //$uid="0033";
    $ddday = date('Y-m-d H:i:s');
    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
    UPDATE slavesetlog
      SET
      SLAVE_SET_AT = :SLAVE_SET_AT,
      SLAVE_SET_SORDER_NO = :SLAVE_SET_SORDER_NO,
      SLAVE_SET_UPDATE_ID = :SLAVE_SET_UPDATE_ID
      WHERE  SLAVE_SET_ID = :id'
      );
  
    $dbh->beginTransaction();
    try {
      $sth->bindParam( ':id', $id, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_SET_AT',  $ddday, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_SET_SORDER_NO', $orderflag, PDO::PARAM_INT);
      $sth->bindParam( ':SLAVE_SET_UPDATE_ID', $uid, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  //最終アップリンク日時書き込み
  public static function writeslavelastuplinkdate($id) {
    $dbh = self::dbPDO();

    $date = "2000-01-01 00:00:00";

    try {
      $sth = $dbh->prepare('
        INSERT INTO lastuplinkdate (
          SLAVE_LA_ID,
          SLAVE_LA_AT
        )
        VALUES(
          :SLAVE_LA_ID,
          :SLAVE_LA_AT
        )'
      );
      $sth->bindValue(':SLAVE_LA_ID', $id);
      $sth->bindValue(':SLAVE_LA_AT', $date);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      } catch (Exception $e) {
        error_output($e->getMessage());
        throw $e;
      }
    return;
  }

  public static function vpslavelastuplinkdate($id, $ddate) {
  
    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
      UPDATE lastuplinkdate
        SET
        SLAVE_LA_AT = :SLAVE_LA_AT
        WHERE 
        SLAVE_LA_ID = :id'
      );

    $dbh->beginTransaction();
    try {
      $sth->bindParam( ':id', $id, PDO::PARAM_STR);
      $sth->bindParam( ':SLAVE_LA_AT', $ddate, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
        $dbh->rollBack();
        throw $e;
    }
    return;
  }

  public static function readslavelastuplinkdate($aid) {
    $dbh = self::dbPDO();
    try {
        $sth = $dbh->prepare('
        SELECT *
        FROM lastuplinkdate
        WHERE SLAVE_LA_ID = ?
        ;');
     
        $sth->bindValue(1, $aid, PDO::PARAM_STR);
       // 実行
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function readslavebatchdate($agwid) {
    $dbh = self::dbPDO();
    $result = "";
    try {
      $sth = $dbh->prepare('
      SELECT *
      FROM slavesetbatch
      WHERE SLAVE_SET_ID = ?
      ;');
  
      $sth->bindValue(1, $agwid, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function write_batch($id, $uid, $values) {

    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare("
    INSERT INTO slavesetbatch (
      SLAVE_SET_ID,
      SLAVE_SET_ENABLE,
      SLAVE_SET1OFFON,
      SLAVE_SET1UP,
      SLAVE_SET1DOWN,
      SLAVE_SET1_TIME,
      SLAVE_SET2OFFON,
      SLAVE_SET2_TIME,
      SLAVE_SET2CLOSEOPEN,
      SLAVE_SET2_LIMIT,
      SLAVE_SET2_SPAN,
      SLAVE_SET_UPDATE_ID,
      SLAVE_SET_AT
    )
    VALUES(
      :SLAVE_SET_ID,
      :SLAVE_SET_ENABLE,
      :SLAVE_SET1OFFON,
      :SLAVE_SET1UP,
      :SLAVE_SET1DOWN,
      :SLAVE_SET1_TIME,
      :SLAVE_SET2OFFON,
      :SLAVE_SET2_TIME,
      :SLAVE_SET2CLOSEOPEN,
      :SLAVE_SET2_LIMIT,
      :SLAVE_SET2_SPAN,
      :SLAVE_SET_UPDATE_ID,
      :SLAVE_SET_AT
    )
    ON DUPLICATE KEY UPDATE
      SLAVE_SET_ENABLE=:SLAVE_SET_ENABLE2,
      SLAVE_SET1OFFON=:SLAVE_SET1OFFON2,
      SLAVE_SET1UP=:SLAVE_SET1UP2,
      SLAVE_SET1DOWN=:SLAVE_SET1DOWN2,
      SLAVE_SET1_TIME=:SLAVE_SET1_TIME2,
      SLAVE_SET2OFFON=:SLAVE_SET2OFFON2,
      SLAVE_SET2_TIME=:SLAVE_SET2_TIME2,
      SLAVE_SET2_LIMIT=:SLAVE_SET2_LIMIT2,
      SLAVE_SET2_SPAN=:SLAVE_SET2_SPAN2,
      SLAVE_SET2CLOSEOPEN=:SLAVE_SET2CLOSEOPEN2,
      SLAVE_SET_UPDATE_ID=:SLAVE_SET_UPDATE_ID2,
      SLAVE_SET_AT=:SLAVE_SET_AT2;
    "
    );
    $dbh ->beginTransaction();

    try {
      $udate = date('Y-m-d H:i:s');

      $sth->bindValue(':SLAVE_SET_ID', $id);
      $sth->bindValue(':SLAVE_SET_ENABLE', $values[0]);
      $sth->bindValue(':SLAVE_SET1OFFON', $values[1]);
      $sth->bindValue(':SLAVE_SET1UP', $values[2]);
      $sth->bindValue(':SLAVE_SET1DOWN', $values[3]);
      $sth->bindValue(':SLAVE_SET1_TIME', $values[4]);
      $sth->bindValue(':SLAVE_SET2OFFON', $values[5]);
      $sth->bindValue(':SLAVE_SET2_TIME', $values[6]);
      $sth->bindValue(':SLAVE_SET2CLOSEOPEN', $values[7]);
      $sth->bindValue(':SLAVE_SET2_LIMIT', $values[8]);
      $sth->bindValue(':SLAVE_SET2_SPAN', $values[9]);
      $sth->bindValue(':SLAVE_SET_UPDATE_ID', $uid);
      $sth->bindValue(':SLAVE_SET_AT', $udate);

      $sth->bindValue(':SLAVE_SET_ENABLE2', $values[0]);
      $sth->bindValue(':SLAVE_SET1OFFON2', $values[1]);
      $sth->bindValue(':SLAVE_SET1UP2', $values[2]);
      $sth->bindValue(':SLAVE_SET1DOWN2', $values[3]);
      $sth->bindValue(':SLAVE_SET1_TIME2', $values[4]);
      $sth->bindValue(':SLAVE_SET2OFFON2', $values[5]);
      $sth->bindValue(':SLAVE_SET2_TIME2', $values[6]);
      $sth->bindValue(':SLAVE_SET2CLOSEOPEN2', $values[7]);
      $sth->bindValue(':SLAVE_SET2_LIMIT2', $values[8]);
      $sth->bindValue(':SLAVE_SET2_SPAN2', $values[9]);
      $sth->bindValue(':SLAVE_SET_UPDATE_ID2', $uid);
      $sth->bindValue(':SLAVE_SET_AT2', $udate);

      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  public static function change_childuserpremission($agid, $premission) {

    $ddday = date('Ymd');
    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
      UPDATE user
        SET
        USER_PERMISSION_ID = :USER_PERMISSION_ID,
        USER_UPDATE_AT = :USER_UPDATE_AT
        WHERE  USER_GROUP_ID = :id
        AND USER_PERMISSION_ID <> 1
    ');
  
    $dbh->beginTransaction();
    try {
      $sth->bindParam( ':id', $agid, PDO::PARAM_STR);
      $sth->bindParam( ':USER_PERMISSION_ID',  $premission, PDO::PARAM_STR);
      $sth->bindParam( ':USER_UPDATE_AT',  $ddday, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  // 該当グループのユーザー情報を取得する
  public static function getchilduser($gid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        SELECT USER_LOGIN_ID, USER_NAME, USER_GROUP_ID
        FROM user
        WHERE USER_GROUP_ID = :USER_GROUP_ID
        AND USER_PERMISSION_ID <> 1
        AND USER_TYPE_CONTINUE = "0"
      ;');
    
      $sth->bindParam(':USER_GROUP_ID',  $gid, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  // 開閉権限情報の取得
  public static function getparmissionenable($aid) {

    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        SELECT PARMISSION_OFFON
        FROM slaveparmission
        WHERE SLAVE_ID = :SLAVE_ID
      ;');
    
      $sth->bindParam(':SLAVE_ID', $aid, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function getparmissioninfo() {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        SELECT *
        FROM slaveparmission
      ;');
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function write_slaveparmission($uid, $gid, $values) {

    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare("
    INSERT INTO slaveparmission (
      SLAVE_ID,
      PARMISSION_GROUP_ID,
      PARMISSION_ENABLE_DATE,
      PARMISSION_DISABLE_DATE,
      PARMISSION_LIMIT,
      PARMISSION_OPENCLOSE,
      PARMISSION_UPDATE_AT,
      PARMISSION_UPDATE_ID,
      PARMISSION_OFFON
    )
    VALUES(
      :SLAVE_ID,
      :PARMISSION_GROUP_ID,
      :PARMISSION_ENABLE_DATE,
      :PARMISSION_DISABLE_DATE,
      :PARMISSION_LIMIT,
      :PARMISSION_OPENCLOSE,
      :PARMISSION_UPDATE_AT,
      :PARMISSION_UPDATE_ID,
      :PARMISSION_OFFON
    )
    ON DUPLICATE KEY UPDATE
      PARMISSION_ENABLE_DATE  = :PARMISSION_ENABLE_DATE2,
      PARMISSION_DISABLE_DATE = :PARMISSION_DISABLE_DATE2,
      PARMISSION_LIMIT        = :PARMISSION_LIMIT2,
      PARMISSION_OPENCLOSE    = :PARMISSION_OPENCLOSE2,
      PARMISSION_UPDATE_AT    = :PARMISSION_UPDATE_AT2,
      PARMISSION_UPDATE_ID    = :PARMISSION_UPDATE_ID2,
      PARMISSION_OFFON        = :PARMISSION_OFFON2;
    "
    );
    $dbh ->beginTransaction();

    try {
      $udate = date('Ymd');

      $sth->bindValue(':SLAVE_ID', $values[0]);
      $sth->bindValue(':PARMISSION_GROUP_ID', $gid);
      
      $sth->bindValue(':PARMISSION_ENABLE_DATE', $values[1]);
      $sth->bindValue(':PARMISSION_DISABLE_DATE', $values[2]);
      $sth->bindValue(':PARMISSION_LIMIT', $values[3]);
      $sth->bindValue(':PARMISSION_OPENCLOSE', $values[4]);
      $sth->bindValue(':PARMISSION_OFFON', $values[5]);
      $sth->bindValue(':PARMISSION_UPDATE_AT', $udate);
      $sth->bindValue(':PARMISSION_UPDATE_ID', $uid);

      //$sth->bindValue(':SLAVE_ID2', $values[0]);
      $sth->bindValue(':PARMISSION_ENABLE_DATE2', $values[1]);
      $sth->bindValue(':PARMISSION_DISABLE_DATE2', $values[2]);
      $sth->bindValue(':PARMISSION_LIMIT2', $values[3]);
      $sth->bindValue(':PARMISSION_OPENCLOSE2', $values[4]);
      $sth->bindValue(':PARMISSION_OFFON2', $values[5]);
      $sth->bindValue(':PARMISSION_UPDATE_AT2', $udate);
      $sth->bindValue(':PARMISSION_UPDATE_ID2', $uid);

      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  public static function updateparmissionenable($asid, $enable) {
    $ddday = date('Ymd');
    $dbh = self::dbPDO();
    
    $sth = $dbh->prepare('
      UPDATE slaveparmission
        SET
        PARMISSION_ENABLE = :PARMISSION_ENABLE,
        PARMISSION_UPDATE_AT = :PARMISSION_UPDATE_AT
        WHERE  SLAVE_ID = :id;
    ');
  
    $dbh->beginTransaction();
    try {
      $sth->bindParam( ':id', $asid, PDO::PARAM_STR);
      $sth->bindParam( ':PARMISSION_ENABLE',  $enable, PDO::PARAM_STR);
      $sth->bindParam( ':PARMISSION_UPDATE_AT',  $ddday, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      $dbh->rollBack();
      throw $e;
    }
    return;
  }

  public static function checkparmission($asid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        SELECT PARMISSION_ENABLE
        FROM slaveparmission
        WHERE SLAVE_ID = :SLAVE_ID
      ;');

      $sth->bindValue(':SLAVE_ID', $asid);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function existsparmission($agroupid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        SELECT EXISTS(
          SELECT PARMISSION_ENABLE
          FROM slaveparmission
          WHERE PARMISSION_GROUP_ID = :PARMISSION_GROUP_ID
          AND PARMISSION_ENABLE = 1
        ) AS parmission_check
      ;');

      $sth->bindValue(':PARMISSION_GROUP_ID', $agroupid);

      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function deleteslaveupdata($asid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        DELETE FROM slaveupdata
          WHERE SLAVEU_ID = :SLAVEU_ID
      ;');
      $dbh->beginTransaction();
      $sth->bindValue(':SLAVEU_ID', $asid);

      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      $dbh->commit();
    } catch (Exception $e) {
      error_output("deleteslaveupdata".$e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function deleteslavedowndata($asid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        DELETE FROM slavedowndata
          WHERE SLAVED_ID = :SLAVED_ID
      ;');
      $dbh->beginTransaction();
      $sth->bindValue(':SLAVED_ID', $asid);

      // 実行
      $sth->execute();
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function deleteslavestandardvlog($asid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        DELETE FROM slavedowndata
          WHERE SLAVED_ID = :SLAVED_ID
      ;');
      $dbh->beginTransaction();
      $sth->bindValue(':SLAVED_ID', $asid);

      // 実行
      $sth->execute();
      $dbh->commit();
    } catch (Exception $e) {
      error_output("deleteslavestandardvlog".$e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function deletelastuplinkdate($asid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        DELETE FROM lastuplinkdate
          WHERE SLAVE_LA_ID = :SLAVE_LA_ID
      ;');
      $dbh->beginTransaction();
      $sth->bindValue(':SLAVE_LA_ID', $asid);

      // 実行
      $sth->execute();
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function deleteslaveoclog($asid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        DELETE FROM slaveoclog
          WHERE SLAVE_OC_ID = :SLAVE_OC_ID
      ;');
      $dbh->beginTransaction();
      $sth->bindValue(':SLAVE_OC_ID', $asid);

      // 実行
      $sth->execute();
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function deleteslaveparmission($asid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        DELETE FROM slaveparmission
          WHERE SLAVE_ID = :SLAVE_ID
      ;');
      $dbh->beginTransaction();
      $sth->bindValue(':SLAVE_ID', $asid);

      // 実行
      $sth->execute();
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function deleteslavepisetlog($asid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        DELETE FROM slavepisetlog
          WHERE SLAVE_PI_ID = :SLAVE_PI_ID
      ;');
      $dbh->beginTransaction();
      $sth->bindValue(':SLAVE_PI_ID', $asid);

      // 実行
      $sth->execute();
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }

  public static function deleteslavesetlog($asid) {
    $dbh = self::dbPDO();
    
    try {
      $sth = $dbh->prepare('
        DELETE FROM slavesetlog
          WHERE SLAVE_SET_ID = :SLAVE_SET_ID
      ;');
      $dbh->beginTransaction();
      $sth->bindValue(':SLAVE_SET_ID', $asid);

      // 実行
      $sth->execute();
      $dbh->commit();
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }


  //ゲート内子機の最新アップリンクデータ履歴取得
  public static function readslavehist($gwid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        SELECT slave.SLAVE_NAME, SLAVEU_DATE, SLAVEU_SUIIDATA, SLAVEU_TEMPDATA, SLAVEU_BAT
        FROM slaveupdata 
        INNER JOIN slave
        ON slaveupdata.SLAVEU_ID = slave.SLAVE_ID
        WHERE SLAVEU_ID =
        ANY (SELECT SLAVE_ID FROM slave 
          WHERE SLAVE_GW_ID = ?
          ORDER BY SLAVE_NUMBER)
        ORDER BY slave.SLAVE_NUMBER ASC, SLAVEU_DATE DESC
      ;');
      $sth->bindValue(1, $gwid, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output("readslavehist".$e->getMessage());
      throw $e;
    }
    return $result;
  }

  //子機のアップリンクデータ履歴取得
  public static function readoneslavehist($asid) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
        SELECT slave.SLAVE_NAME, SLAVEU_DATE, SLAVEU_SUIIDATA, SLAVEU_TEMPDATA, SLAVEU_BAT
        FROM slaveupdata 
        INNER JOIN slave
        ON slaveupdata.SLAVEU_ID = slave.SLAVE_ID
        WHERE SLAVEU_ID = ?
        ORDER BY slave.SLAVE_NUMBER ASC, SLAVEU_DATE DESC
      ;');
      $sth->bindValue(1, $asid, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output("readoneslavehist".$e->getMessage());
      throw $e;
    }
    return $result;
  }


  //API用最新アップリンクデータ取得
  public static function read_slave_data($id) {
    $dbh = self::dbPDO();
    try {
      $sth = $dbh->prepare('
      SELECT main.SLAVE_ID, main.SLAVE_GW_ID,
             sub1.SLAVEU_SUIIDATA, sub1.SLAVEU_TEMPDATA, sub1.SLAVEU_OPCLSTATE,
             sub2.SLAVE_SET1OFFON, sub2.SLAVE_SET1CLOSEOPEN, sub2.SLAVE_SET_AT
      FROM slave AS main
      LEFT JOIN slaveupdata AS sub1
      ON main.SLAVE_ID = sub1.SLAVEU_ID
      LEFT JOIN slavesetlog AS sub2
      ON main.SLAVE_ID = sub2.SLAVE_SET_ID
      WHERE SLAVE_ID = ?
      ;');

      $sth = $dbh->prepare('
      SELECT main.SLAVEU_ID, sub1.SLAVE_GW_ID,
             main.SLAVEU_SUIIDATA, main.SLAVEU_TEMPDATA, main.SLAVEU_OPCLSTATE,
             sub2.SLAVE_SET1OFFON, sub2.SLAVE_SET1CLOSEOPEN, sub2.SLAVE_SET_AT,
             main.SLAVEU_DATE
      FROM slaveupdata AS main
      LEFT JOIN slave AS sub1
      ON main.SLAVEU_ID = sub1.SLAVE_ID
      LEFT JOIN slavesetlog AS sub2
      ON main.SLAVEU_ID = sub2.SLAVE_SET_ID
      WHERE SLAVEU_ID = ?
      ORDER BY main.SLAVEU_DATE DESC limit 1
      ;');
    
      $sth->bindValue(1, $id, PDO::PARAM_STR);
      //$sth->bindValue(2, $loginname, PDO::PARAM_STR);
      // 実行
      $sth->execute();
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_output($e->getMessage());
      throw $e;
    }
    return $result;
  }


}