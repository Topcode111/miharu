<?php
	header('Expires: Tue, 1 Jan 2019 00:00:00 GMT');
	header('Last-Modified:' . gmdate( 'D, d M Y H:i:s' ) . 'GMT');
	header('Cache-Control:no-cache,no-store,must-revalidate,max-age=0');
	header('Cache-Control:pre-check=0,post-check=0',false);
	header('Pragma:no-cache');
?>
<?php
  require_once(dirname(__FILE__).'/function.php');
  require_once(dirname(__FILE__).'/../php/db.php');
  
  if(!isset($_SESSION)){
    session_start();
  }
  header('Content-type: text/html; charset=utf-8');
  //$token = $_POST['token'];
  define('TODAY', date("YmdHi"));
  //CSRF エラー
  //if ($token != $_SESSION['token']) {
  //   $_SESSION['error_status'] = 2;
  //   redirect_to_login();
  //   exit();
  //}
  $_SESSION['token'] = get_csrf_token(); // CSRFのトークンを取得する
  $usergroupid =  $_SESSION['usergroupid'];
  $groupdata = dbmgr::getdbgroup($usergroupid);
  $groupname = $groupdata[0]['GROUP_NAME'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
		<title>水田モニター</title>
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../css/miharu-common.css?<?php echo TODAY?>">
    <link rel="stylesheet" type="text/css" href="../top/top.css?<?php echo TODAY?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" src="../js/selfmade.js?<?php echo TODAY?>"></script>
    <script src="passwordchecker.js" type="text/javascript"></script>
    <script src="changepass.js" type="text/javascript"></script>
    <script type="text/javascript">
  
      /*
      * 登録前チェック
      */
      function conrimMessage() {
        var old_pass = document.getElementById("old_password").value;
        var pass = document.getElementById("password").value;
        var conf = document.getElementById("confirm_password").value;
        //必須チェック
        if ((old_pass == "") || (pass == "") || (conf == "")) {
            alert("必須項目が入力されていません。");
            return false;
        }
        //パスワードチェック
        if (pass != conf) {
            alert("パスワードが一致していません。");
            return false;
        }
        if (passwordLevel < 3) {
            alert("半角８文字から１４文字までかつ小文字、大文字、数値、記号のうち二つでありません。");
            return false;
        }
        //  if (passwordLevel < 3) {
        //    return confirm("パスワード強度が弱いですがよいですか？");
        //  }
        return true;
      }
   </script>
</head>   
<form action="passchangesub.php" method="post" onsubmit="return conrimMessage();">  
  <?php
    if ($_SESSION['error_status'] == 1) {
      echo '<h2 style="color:red;">入力内容に誤りがあります。</h2>';
    }
    if ($_SESSION['error_status'] == 2) {
      echo '<h2 style="color:red;">不正なリクエストです。</h2>';
    }
  ?>
  <div name="footer">水田モニター Ver1.1
		<br>
		<br>
	</div>
  <div style="margin-left:5%;margin-right:5%;">
  <div style="text-align:center;" id="hyname">AV自動水栓遠隔開閉システム</div>
      <div style="margin-left:20%;font-size: 20px;"><?php echo $groupname;?></div> 
  </div>    
  <h1 align = "center">パスワード変更画面</h1>

    <table align = "center" style="width: 50%;">
      <tr>
        <td>古いパスワード</td>
        <td><input type="password" name="old_password" id="old_password"></td>
      </tr>
      <tr>
        <td>新しいパスワード</td>
        <td><input type="password" name="password" id="password" onkeyup="setMessage(this.value);"></td>
        <td><div id="pass_message"></div></td>
      </tr>
      <tr>
        <td>新しいパスワード（確認）</td>
        <td><input type="password" name="confirm_password" id="confirm_password" onkeyup="setConfirmMessage(this.value);"></td>
        <td><div id="pass_confirm_message"></div></td>
      </tr>
    </table>
    <br>
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']  , ENT_QUOTES, "UTF-8") ?>">
    <div align = "center">
      <button type="submit" style = "font-size:20px;">更新</button>
      <button type="button" onclick="document.location.href='../top.php';" style = "font-size:20px;">戻る</button>
    </div>
    <br>
    <br> 
    <br>
	<div align = "center">
	    <img src="../src/logo.png" alt="asahi" title="asahi" style="width: 20%;">
		  <div style="width: 95%;font-size: 25px;">
	    旭有機材株式会社
	</div>
</body>
</html>
 