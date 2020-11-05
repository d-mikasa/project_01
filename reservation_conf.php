<?php
require_once('class/Library.php');
$Reservation = new Reservation();

checkLogin();

//予約状況を確認するための配列を取得する
$rsv_info = $Reservation->checkReservation($_POST['detail_id'], $_POST['check_in'], $_POST['check_out']);

//選択した部屋の内容を取得する　メソッド名がおかしいらしい
$room_info = $Reservation->rsvGetRoom($_POST['detail_id']);

//エラー内容変数の初期化
$error = [];

////////////////////////////////////////////日付系のバリデーションまとめ////////////////////////////////////////////
//チェックインのバリデーション
if (empty($_POST['check_in'])) {
    $error['check_in'] = 'チェックイン日時が空欄です';
} else {
    if (strtotime($_POST['check_in']) < strtotime("-1 day")) {
        $error['check_in'] = 'チェックイン日が過去を指定しています';
    }
}

//チェックアウトのバリデーション
if (empty($_POST['check_out'])) {
    $error['check_out'] = 'チェックアウト日が空欄です';
} else {
    if (strtotime($_POST['check_out']) < strtotime("-1 day")) {
        $error['check_out'] = 'チェックアウト日が過去を指定しています';
    }
}

//日付の整合性に関するバリデーション
if (empty($error['check_in']) and empty($error['check_out'])) {
    //チェックイン・チェックアウトが入力されていた場合

    if (strtotime($_POST['check_in']) > strtotime($_POST['check_out'])) {
        $error['check_in'] = 'チェックイン日がチェックアウト日時より後に指定されています';
    }

    if (strtotime($_POST['check_in']) == strtotime($_POST['check_out'])) {
        $error['check_out'] = 'チェックイン日とチェックアウト日時が同日に指定されています';
    }

    //予約が日付以内の物であるかどうかの確認
    if (strtotime($_POST['check_in']) >= (strtotime("+90 day"))) {
        $error['check_in'] = '３ヶ月以内のご予約のみ承っております';
    }

    if (strtotime($_POST['check_out']) >= (strtotime("+90 day"))) {
        $error['check_out'] = '３ヶ月以内のご予約のみ承っております';
    }

    //チェックインとチェックアウトの日付を獲得、その後にSQL側で範囲の指定を行う。
    if (empty($error['check_in']) and empty($error['check_out'])) {
        // 期間内の日付をすべて取得
        if ($rsv_info != 'Not reservation room') {
            $error['ather'] = $rsv_info;
        }
    }
}

//宿泊人数のバリデーション
if (empty($_POST['capacity'])) {
    $error['capacity'] = '宿泊人数が空欄です';
} else {
    //部屋詳細から該当の宿泊人数のプランがあるかを検索する
    if ($_POST['capacity'] != $room_info['capacity']) {
        if ($_POST['capacity'] > $room_info['capacity']) {
            $error['capacity'] = '宿泊人数が上限を超えています';
        }
    }
}

for ($i = date('Ymd', strtotime($_POST['check_in'])); $i < date('Ymd', strtotime($_POST['check_out'])); $i++) {
    $year = substr($i, 0, 4);
    $month = substr($i, 4, 2);
    $day = substr($i, 6, 2);
    if (checkdate($month, $day, $year)) {
        $cnt_stay[] = date('Y-m-d H:i:s', strtotime($i));
    }
}
?>
<?php require_once('rsv_parts/head_info.php'); ?>

<body class="background_conf">
    <?php if (!empty($error)) : ?>
        <!--
        エラーがあって、もう一度フォームを送信する
        -->
        <?php require_once('reservation.php'); ?>
        <?php exit(); ?>
    <?php endif; ?>

    <!--
    フォーム確認画面
    -->
    <?php require_once('rsv_parts/status_nav.php') ?>
    <!--
        エラーが無く、送信することが可能な画面
        -->
    <main class="reservation_main">
        <form action="reservation_done.php" method="post">
            <!--トークンを送信-->
            <input type="hidden" name="csrf_token" value="<?= $Reservation->getToken() ?>">
            <!--実際に送信する情報群-->
            <input type="hidden" name="detail_id" value="<?= $_POST['detail_id'] ?>"><!-- 詳細番号 -->
            <input type="hidden" name="check_in" value="<?= $_POST['check_in'] ?>"><!-- チェックイン日 -->
            <input type="hidden" name="check_out" value="<?= $_POST['check_out'] ?>"><!-- チェックアウト日 -->
            <input type="hidden" name="capacity" value="<?= $_POST['capacity'] ?>"><!-- 宿泊人数 -->
            <input type="hidden" name="peyment" value="<?= $_POST['peyment'] ?>"><!-- 支払い方法 -->
            <input type="hidden" name="room_id" value="<?= $room_info['id'] ?>">
            <input type="hidden" name="name" value="<?= $room_info['name'] ?>">
            <input type="hidden" name="price" value="<?= $room_info['price'] ?>">
            <div class="titles">ご予約内容確認</div>
            <table class="conf_check_table">
                <tr>
                    <th>部屋名</th>
                    <td> <?= $room_info['name'] ?> </td>
                </tr>
                <tr>
                    <th>チェックイン</th>
                    <td> <?= $_POST['check_in'] ?> </td>
                </tr>
                <tr>
                    <th>チェックアウト</th>
                    <td> <?= $_POST['check_out'] ?> </td>
                </tr>
                <tr>
                    <th>宿泊人数</th>
                    <td> <?= $_POST['capacity'] ?> </td>
                </tr>
                <tr>
                    <th>支払い方法</th>
                    <td> <?php
                            switch ($_POST['peyment']) {
                                case '1':
                                    echo '現金（現地支払い）';
                                    break;
                                case '2':
                                    echo 'クレジットカード（オンライン決算）';
                                    break;
                                default:
                                    echo 'クレジットカード（現地支払い）';
                                    break;
                            }
                            ?> </td>
                </tr>
                <tr>
                    <th>合計金額</th>
                    <td>¥<?= number_format($room_info['price'] * count($cnt_stay)) ?></td>
                </tr>
            </table>
            <div class="final_check">以上の内容でお間違い無いでしょうか？</div>
            <p class="submit_form">
                <button type="button" onclick="multipleaction('reservation_done.php')">予約する</button>
                <button type="button" onclick="multipleaction('reservation.php')">キャンセル</button>
            </p>
        </form>
    </main>
</body>

<script>
    function multipleaction(u) {
        var f = document.querySelector("form");
        var a = f.setAttribute("action", u);
        document.querySelector("form").submit();
    }
</script>

</html>