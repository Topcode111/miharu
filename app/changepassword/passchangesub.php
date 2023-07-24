<?php
	header('Expires: Tue, 1 Jan 2019 00:00:00 GMT');
	header('Last-Modified:' . gmdate( 'D, d M Y H:i:s' ) . 'GMT');
	header('Cache-Control:no-cache,no-store,must-revalidate,max-age=0');
	header('Cache-Control:pre-check=0,post-check=0',false);
	header('Pragma:no-cache');
?>
<?php
  if(!isset($_SESSION)){
    session_start();
  }
  require_once(dirname(__FILE__).'/function.php');
  require_once(dirname(__FILE__).'/../php/db.php');

  header('Content-type: text/html; charset=utf-8');
  //$_SESSION['userid'] = 'YSEO88';
  //$_POST['old_password'] = 'testpass1';
  //$_POST['password'] = 'testpass';
  //$_POST['confirm_password'] = 'testpass';

  $userid = $_SESSION['userid'];
  $old_password = $_POST['old_password'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $token = $_POST['token'];
  // CSRFチェック
  if ($token != $_SESSION['token']) {
     $_SESSION = array();
     session_destroy();
     session_start();
    $_SESSION['error_status'] = 2;
    redirect_to_login();
    exit();
  }
    //パスワード不一致
  if ($password != $confirm_password) {
    $_SESSION['error_status'] = 1;
    //POSTで戻る
    echo_html_submit();
    exit();
  }
  try {
    // DB接続
    //$pdo = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());
    //旧パスワードチェック
    //プレースホルダで SQL 作成
    //$sql = "SELECT * FROM users WHERE id = ? AND is_user = 1;";
    //$stmt = $pdo->prepare($sql);
    //$stmt->bindValue(1, $id, PDO::PARAM_STR);
    //$stmt->execute();
    //$row = $stmt->fetch(PDO::FETCH_ASSOC);
    $row = dbmgr::getuserdata($userid);

    // IDがない
    if (empty($row)) {
      $_SESSION['error_status'] = 2;
      //POSTで戻る
      echo_html_submit();
      exit();
    }
    //$mail = $row['mailaddress'];
    // 旧パスワードチェック if (hash("sha256",$_POST['password']) === $decodedpass) 
    if (!(hash("sha256",$old_password) === $row[0]['USER_PASSWORD'])) {
      $_SESSION['error_status'] = 1;
      //POST で戻る
      echo_html_submit();
      exit();
    }
    //パスワード更新
    //新パスワード生成
    //$hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $hashed_password = hash("sha256",$password) ;
    dbmgr::writedbpass($hashed_password,$userid);
    //プレースホルダで SQL 作成
    //$sql = "UPDATE users SET password = ?, reset = 0, last_change_pass_time = ? WHERE id = ?;";
    //$stmt = $pdo->prepare($sql);
    // トランザクションの開始
    //$pdo->beginTransaction();
    //try {
    //  $stmt->bindValue(1, $hashed_password, PDO::PARAM_STR);
    //  $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
    //  $stmt->bindValue(3, $id, PDO::PARAM_STR);
    //  $stmt->execute();
      // コミット
    //  $pdo->commit();
    //} catch (PDOException $e) {
      // ロールバック
    //  $pdo->rollBack();
    //  throw $e;
    //}
  } catch (Exception $e) {
    die($e->getMessage());
  }
  //メール送信
  //$mail = str_replace(array('\r\n','\r','\n'), '', $mail);  //メールヘッダーインジェクション対策
  //$msg = 'パスワードが変更されました。';
  //mb_send_mail($mail, 'パスワードの変更', $msg, ' From : ' . SENDER_EMAIL);
  /*
  * HTML を出力してPOSTリクエストで戻る
  */
  function echo_html_submit() {
    echo '<!DOCTYPE html>';
    echo '<head>';
    echo '<meta charset="utf-8">';
    echo '</head>';
    echo '<html lang="ja">';
    echo '<body onload="document.returnForm.submit();">';
    echo '<form name="returnForm" method="post" action="pass_change.php">';
    echo '<input type="hidden" name="token" value="' .  htmlspecialchars($_SESSION['token'], ENT_QUOTES, 'UTF-8') . '">';
    echo '</form>';
    echo '</body>';
    echo '</html>';
  }
?>
<!DOCTYPE html>
<head>
  <meta charset="utf-8">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>
<html lang="ja">
  <body>
  <h1 align = "center">完了画面</h1>
  <h2 align = "center">パスワードの変更が完了しました。</h2>
    <br><br>
    <div align = "center">
    <button type="button" onclick="document.location.href='../top.php';" style = "font-size:20px;">戻る</button>
    </div>
  </body>
</html>