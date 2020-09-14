<?php
require_once('class/Library.php');
echo 'POSTの値' . '<br>';
print_r('<pre>');
print_r($_POST);
print_r('</pre>');
$pdo = new UpdateReservation();
$days = $pdo -> into_reservation($_POST['detail_id'], $_POST['check_in'], $_POST['check_out'], $_POST['capacity'], $_POST['peyment'], $_POST['price'], $_POST['detail_name'],$_POST['room_id']);

$to = 'natume21gin@gmail.com';
$title = '予約完了のお知らせ';
$message = <<<EOD
内容のテストです。
EOD;

$header = 'From: d.mikasa@ebacorp.jp' . "\r\n";
$header .= 'Return-Path: d.mikasa@ebacorp.jp';

mb_language("Japanese");
mb_internal_encoding("UTF-8");


if(mb_send_mail($to, $title, $message, $header)){
    echo "メールを送信しました";
} else {
  echo "メールの送信に失敗しました";
};

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
予約しました。
<a href="reservation.php">トップページへ戻る</a>
</body>

</html>