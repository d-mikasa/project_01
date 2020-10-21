<?php
require_once('class/Library.php');
$pdo = new rsvUpdate();

$insert_date = $pdo->into_reservation($_POST['detail_id'], $_POST['check_in'], $_POST['check_out'], $_POST['capacity'], $_POST['peyment'], $_POST['price'], $_POST['detail_name'], $_POST['room_id']);

$total_price = $insert_date['total_price'];

//メール送信内容
$to = $insert_date['user_mail'];
$title = '予約完了のお知らせ';
$message = <<<EOD
-----------------------------------------------------------------------
※本メールは、自動的に配信しています。
こちらのメールは送信専用のため、直接ご返信いただいてもお問い合わせには
お答えできませんので、あらかじめご了承ください。
-----------------------------------------------------------------------

このたびは、CICACUをご予約いただき誠にありがとうございます。
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
料金            ：$_POST[price] 円× $_POST[capacity] 人
宿泊日数    ：$insert_date[stay_total] 日
合計            ：$insert_date[total_price] 円（税込・サービス料別）
EOD;

$header = 'From: d.mikasa@ebacorp.jp' . "\r\n";
$header .= 'Return-Path: d.mikasa@ebacorp.jp';


mb_language("Japanese");
mb_internal_encoding("UTF-8");

//メール送信メソッド
if (mb_send_mail($to, $title, $message, $header)) {
    $mail_info =  "ご登録頂いたメールアドレスに、確認メールを送信致しました";
} else {
    $mail_info =  "メールの送信に失敗しました";
};

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/reservation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap-reboot.min.css">
</head>

<body>
<?= getNav('done') ?>
    <div>
    予約致しました。<br>
お客様のメールアドレスへ、確認のメールをお送りいたしました。
    </div>
    <a href="reservation.php">トップページへ戻る</a>
</body>

</html>