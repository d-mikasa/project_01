<?php
require_once('class/Library.php');

checkLogin();

// POSTでトークンが来ていないか、セッションに保存した値とPOSTがマッチしていなければ飛ばすぞ
if (!isset($_POST['csrf_token']) OR ($_POST['csrf_token'] != $_SESSION['csrf_token'])) {
    header('Location: login.php');
    exit();
}

// sessionに保存してあるトークンを削除
unset($_SESSION["csrf_token"]);

$Reservation= new Reservation();
$insert_data = $Reservation->updateReservation($_POST);

$total_price = $insert_data['total_price'];

//メール送信内容
$title = '予約完了のお知らせ';
$message =
'-----------------------------------------------------------------------'. "\n" .
'※本メールは、自動的に配信しています。'. "\n" .
'こちらのメールは送信専用のため、直接ご返信いただいてもお問い合わせには'. "\n" .
'お答えできませんので、あらかじめご了承ください。'. "\n" .
'-----------------------------------------------------------------------'. "\n" .
"\n" .
'このたびは、CICACUをご予約いただき誠にありがとうございます。'. "\n" .
'ご予約いただいた内容をお知らせします。'. "\n" .
"\n" .
'宿泊代表者氏名：' . $insert_data['user_name'] . ' 様'. "\n" .
'宿名：CICACU'. "\n" .
'電話番号：080-1411-4095'. "\n" .
'所在地：〒322-0067 栃木県鹿沼市天神町1704'. "\n" .
'チェックイン日時：' . $_POST['check_in'] .''. "\n" .
'チェックアウト日時：' . $_POST['check_out'] . ''. "\n" .
"\n" .
'部屋タイプ：' . $insert_data['name'] . ''. "\n" .
"\n" .
'チェックイン可能時間：16:00～23:00'. "\n" .
'チェックアウト時間：10:00'. "\n" .
"\n" .
'-----------------------------------------------------------------------'. "\n" .
'【キャンセル規定】'. "\n" .
'宿泊予定の２日前からキャンセル料が発生します。'. "\n" .
'２日前、前日のキャンセル：50％'. "\n" .
'当日のキャンセルまたは、不泊の場合：100％　頂戴いたします。'. "\n" .
"\n" .
'-----------------------------------------------------------------------'. "\n" .
'【料金明細】'. "\n" .
'料金            ：' . $insert_data['price'] . ' 円× ' . $_POST['capacity'] . ' 人'. "\n" .
'宿泊日数    ：' . $insert_data['total_stay'] . '日'. "\n" .
'合計            ：' . $insert_data['total_price'] . ' 円（税込・サービス料別）';

$header = 'From: d.mikasa@ebacorp.jp' . "\r\n";
$header .= 'Return-Path: d.mikasa@ebacorp.jp';

mb_language('Japanese');
mb_internal_encoding('UTF-8');

// メール送信メソッド
mb_send_mail($insert_data['user_mail'], $title, $message, $header);

if($insert_data == 'Error'){
    $done_message = '予約に失敗しました。再度ご予約ください。';
}else{
    $done_message = '予約が完了しました。<br>予約完了メールを送信しました。<br><br>※届かない場合は予約番号をお控えの上ご連絡ください。';
}

?>
<?php require_once('rsv_parts/head_info.php');?>
<body class="background_done">
    <?php require_once('rsv_parts/status_nav.php')?>
    <main class="done_message">
        <div>
            <div>
                予約番号 ＝ <?=$insert_data['reservation_id']?>
            </div>
            <?=$done_message?>
        </div>
        <a href="index.php">トップページへ戻る</a>
    </main>
</body>
</html>