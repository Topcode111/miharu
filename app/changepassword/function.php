<?php
//define('DNS','mysql:host=localhost;dbname=loginsample;charset=utf8');
//define('USER_NAME', 'mysql');
//define('PASSWORD', 'Mysql@1234');
//define('SERVER', '192.168.33.10');
//define('SENDER_EMAIL', 'admin@example.com');
/*
* PDO の接続オプション取得
*/
//function get_pdo_options() {
//  return array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//               PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
//               PDO::ATTR_EMULATE_PREPARES => false);
//}
/*
* CSRF トークン作成
*/
function get_csrf_token() {
 $token_legth = 16;//16*2=32byte
 $bytes = openssl_random_pseudo_bytes($token_legth);
 return bin2hex($bytes);
}
/*
* URL の一時パスワードを作成
*/
function get_url_password() {
  $token_legth = 16;//16*2=32byte
  $bytes = openssl_random_pseudo_bytes($token_legth);
  return hash('sha256', $bytes);
}
/*
* ログイン画面へのリダイレクト
*/
function redirect_to_login() {
  header('HTTP/1.1 301 Moved Permanently');
  header('Location: login.php');
}
/*
* パスワードリセット画面へのリダイレクト
*/
function redirect_to_password_reset() {
  header('HTTP/1.1 301 Moved Permanently');
  header('Location: password_reset.php');
}
/*
* Welcome画面へのリダイレクト
*/
function redirect_to_welcome() {
  header('HTTP/1.1 301 Moved Permanently');
  header('Location: welcome.php');
}
/*
* 登録画面へのリダイレクト
*/
function redirect_to_register() {
  header('HTTP/1.1 301 Moved Permanently');
  header('Location: register.php');
}