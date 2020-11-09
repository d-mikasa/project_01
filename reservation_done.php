<?php
require_once('class/Library.php');

checkLogin();

// POSTでトークンが来ていないか、セッションに保存した値とPOSTがマッチしていなければ飛ばすぞ
if (!isset($_POST["csrf_token"]) OR ($_POST["csrf_token"] != $_SESSION['csrf_token'])) {
    header('Location: login.php');
    exit();
}

// sessionに保存してあるトークンを削除
unset($_SESSION["csrf_token"]);

$Reservation= new Reservation();
$insert_date = $Reservation->updateReservation($_POST);

//PDOエラーが発生したらメールを送らない。
if ($insert_date != 'Error') {
    $total_price = $insert_date['total_price'];

    //メール送信内容
    $to = $insert_date['user_name'];
    $title = '予約完了のお知らせ';
    $message = <<<EOD
-----------------------------------------------------------------------
※本メールは、自動的に配信しています。
こちらのメールは送信専用のため、直接ご返信いただいてもお問い合わせには
お答えできませんので、あらかじめご了承ください。
-----------------------------------------------------------------------

このたびは、CICACUをご予約いただき誠にありがとうございます。
ご予約いただいた内容をお知らせします。

宿泊代表者氏名：$insert_date[user_name] 様
宿名：CICACU
電話番号：080-1411-4095
所在地：〒322-0067 栃木県鹿沼市天神町1704
チェックイン日時：$_POST[check_in]
チェックアウト日時：$_POST[check_out]

部屋タイプ：$_POST[name]

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
宿泊日数    ：$insert_date[total_stay] 日
合計            ：$insert_date[total_price] 円（税込・サービス料別）
EOD;

    $header = 'From: d.mikasa@ebacorp.jp' . "\r\n";
    $header .= 'Return-Path: d.mikasa@ebacorp.jp';

    mb_language("Japanese");
    mb_internal_encoding("UTF-8");

    // メール送信メソッド
    if (mb_send_mail($to, $title, $message, $header)) {
        $mail_info =  "ご登録頂いたメールアドレスに、確認メールを送信致しました";
    } else {
        $mail_info =  "メールの送信に失敗しました";
    };
}
?>
<?php require_once('rsv_parts/head_info.php');?>
<body class="background_done">
<?php require_once('rsv_parts/status_nav.php')?>
    <main class="done_message">
        <?php if($insert_date == 'Error'):?>
            <div>
                エラーが発生しました。再度ご登録ください。
            </div>
        <?php else:?>
            <div>
                予約致しました。<br>お客様のメールアドレスへ、確認のメールをお送りいたしました。
            </div>
        <?php endif;?>
        <a href="index.php">トップページへ戻る</a>
    </main>
</body>
</html>