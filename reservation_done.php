<?php
require_once('class/Library.php');
echo 'POSTの値' . '<br>';
print_r('<pre>');
print_r($_POST);
print_r('</pre>');
$pdo = new UpdateReservation();
$pdo -> into_reservation($_POST['detail_id'], $_POST['check_in'], $_POST['check_out'], $_POST['capacity'], $_POST['peyment'], $_POST['price'], $_POST['detail_name'],$_POST['room_id']);
$total_price = $_POST['price'] * $_POST['capacity'];

$to = 'natume21gin@gmail.com';
$title = '予約完了のお知らせ';
$message = <<<EOD
-----------------------------------------------------------------------
※本メールは、自動的に配信しています。
こちらのメールは送信専用のため、直接ご返信いただいてもお問い合わせには
お答えできませんので、あらかじめご了承ください。
-----------------------------------------------------------------------

このたびは、CICACU　をご予約いただき誠にありがとうございます。
ご予約いただいた内容をお知らせします。

宿泊代表者氏名：$_SESSION[user_name] 様
宿名：CICACU
電話番号：080-1411-4095
所在地：〒322-0067 栃木県鹿沼市天神町1704
チェックイン日時：$_POST[check_in]
チェックアウト日時：$_POST[check_out]

部屋タイプ：$_POST[room_name]

チェックイン可能時間：16:00～23:00
チェックアウト時間：10:00

-----------------------------------------------------------------------
【キャンセル規定】
宿泊予定の２日前からキャンセル料が発生します。
２日前、前日のキャンセル：50％
当日のキャンセルまたは、不泊の場合：100％　頂戴いたします。

-----------------------------------------------------------------------
【料金明細】
$_POST[price] 円× $_POST[capacity] 人
合計：$total_price 円（税込・サービス料別）
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