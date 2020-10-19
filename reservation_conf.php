<?php
require_once('class/Library.php');
$error = [];

$pdo = new rsvUpdate();

//プルダウンの内容を取得する
$pull_down_list = $pdo->room();

//予約状況を確認するための配列を取得する
$reservation_check = $pdo->reservation_check($_POST['detail_id']);

//選択した部屋の内容を取得する
$room_detail = $pdo->room_detail($_POST['detail_id']);

////////////////////////////////////////////日付系のバリデーションまとめ////////////////////////////////////////////
//チェックインのバリデーション
if (empty($_POST['check_in'])) {
    $error['check_in'] = 'チェックイン日時が空欄です';
} else {
    if (strtotime($_POST['check_in']) < time()) {
        $error['check_in'] = 'チェックイン日時が過去を指定しています';
    }
}

//チェックアウトのバリデーション
if (empty($_POST['check_out'])) {
    $error['check_out'] = 'チェックアウト日時が空欄です';
} else {
    if (strtotime($_POST['check_out']) < time()) {
        $error['check_out'] = 'チェックアウト日時が過去を指定しています';
    }
}

//日付の整合性に関するバリデーション
if (empty($error['check_in']) and empty($error['check_out'])) {
    //チェックイン・チェックアウトが入力されていた場合

    if (strtotime($_POST['check_in']) > strtotime($_POST['check_out'])) {
        $error['check_in'] = 'チェックイン日時がチェックアウト日時より後に指定されています';
    }

    if (strtotime($_POST['check_in']) == strtotime($_POST['check_out'])) {
        $error['check_out'] = 'チェックイン日時とチェックアウト日時が同日に指定されています';
    }

    //予約が日付以内の物であるかどうかの確認
    if (strtotime($_POST['check_in']) >= (strtotime("+90 day")) or strtotime($_POST['check_out']) >= (strtotime("+90 day"))) {
        $error['check_out'] = '３ヶ月以内のご予約のみ承っております';
    }

    if (empty($error['check_in']) and empty($error['check_out'])) {
        // 期間内の日付をすべて取得
        for ($i = date('Ymd', strtotime($_POST['check_in'])); $i < date('Ymd', strtotime($_POST['check_out'])); $i++) {
            $year = substr($i, 0, 4);
            $month = substr($i, 4, 2);
            $day = substr($i, 6, 2);

            if (checkdate($month, $day, $year)) {
                $days[] = date('Y-m-d', strtotime($i));
            }
        }

        //予約情報がなければ「予約済みかチェックする」処理をしない
        if ($reservation_check != 'not reservation room') {
            foreach ($reservation_check as $value) {
                $ch_days = explode(',', $value['date']);
                //日付が予約済みかチェックする
                for ($i = 0; $i < count($days); $i++) {
                    for ($k = 0; $k < count($ch_days); $k++) {
                        if (date('d-m-Y', strtotime($ch_days[$k])) == date('d-m-Y', strtotime($days[$i]))) {
                            $error['ather'] = 'すでに予約済みの日程が含まれます';
                        }
                    }
                }
            }
        }
    }
}
    //宿泊人数のバリデーション
    if (empty($_POST['capacity'])) {
        $error['capacity'] = '宿泊人数が空欄です';
    } else {
        //部屋詳細から該当の宿泊人数のプランがあるかを検索する
        if ($_POST['capacity'] != $room_detail['capacity']) {
            if ($_POST['capacity'] > $room_detail['capacity']) {
                $error['capacity'] = '宿泊人数が多いです。';
            }
        }
    }
?>
<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>CICACU</title>
    <meta name="description" content="CICACU(シカク)">
    <meta name="keywords" content="CICACU,cafe饗茶庵,鹿沼,ゲストハウス,民宿">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!--スマホ用に見れるように-->
    <meta name="robots" content="noindex,nofollow,noarchive">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap-reboot.min.css">
    <link rel="stylesheet" href="./css/reservation.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>

<body class="background_conf"> <?php if (empty($error)) : ?>
    <?= getNav('conf') ?>
        <!--エラーが無く、送信することが可能な画面-->
        <main class="reservation_main">
            <form action="reservation_done.php" method="post">
                <!--実際に送信する情報群-->
                <input type="hidden" name="detail_id" value="<?= $_POST['detail_id'] ?>">
                <input type="hidden" name="check_in" value="<?= $_POST['check_in'] ?>">
                <input type="hidden" name="check_out" value="<?= $_POST['check_out'] ?>">
                <input type="hidden" name="capacity" value="<?= $_POST['capacity'] ?>">
                <input type="hidden" name="peyment" value="<?= $_POST['peyment'] ?>">
                <input type="hidden" name="price" value="<?= $room_detail['price'] ?>">
                <input type="hidden" name="detail_name" value="<?= $room_detail['detail_name'] ?>">
                <input type="hidden" name="room_id" value="<?= $room_detail['id'] ?>">
                <input type="hidden" name="room_name" value="<?= $room_detail['name'] ?>">
                <!--実際に送信する情報群-->
                <div class="titles">ご予約内容確認</div>
                <table class = "conf_check_table">
                    <tr>
                        <th>部屋名</th>
                        <td> <?= $room_detail['name'] ?> </td>
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
                </table>

                <div class = "final_check">以上の内容でお間違い無いでしょうか？</div>
                <p class="submit_form">

                    <button type="submit" value="確認">確認</button>
                    <button type="button" value="キャンセル" onclick="location.href='reservation.php'" >キャンセル</button>
                </p>
            </form>
        </main>
</body>

<?php else : ?>

    <!--
    エラーがあって、もう一度フォームを送信する
    -->

    <body class="background_reservation">
        <?= getNav('reservation') ?>
        <main class="reservation_main">

            <!--情報を入力する場所-->
            <form action="reservation_conf.php" method="post">
                <div class="titles">情報入力欄</div>
                <table class="reservation_table">
                    <!--部屋名を入力する場所-->
                    <tr class="reservation_room_name">
                        <th>部屋名</th>
                        <td>
                            <select name="detail_id" id="target"> <?php foreach ($pull_down_list as $value) : ?> <option value="<?= $value['id'] ?>" <?php if (($_POST['detail_id']) == $value['id']) echo 'selected' ?>><?= $value['name'] ?> (<?= $value['capacity'] ?>名様 ¥<?= number_format($value['price']) ?>)</option> <?php endforeach; ?> </select>
                        </td>
                    </tr>

                    <!--dummy-->
                        <tr class="room_name_error">
                            <td colspan="2" class="error"></td>
                        </tr>

                    <!--チェックインを入力する場所-->
                    <tr class="reservation_check_in">
                        <th>チェックイン</th>
                        <td>
                            <input type="date" name="check_in" value="<?php if (!empty($_POST['check_in'])) echo $_POST['check_in'] ?>">
                        </td>
                    </tr>

                    <!--チェックインエラー表示-->
                        <!-- dammy error message -->
                        <tr class="room_name_error">
                            <td colspan="2" class="error"><?php if (!empty($error['check_in'])) echo $error['check_in'] ?></td>
                        </tr>



                    <tr>
                        <th>チェックアウト </th>
                        <td>
                            <input type="date" name="check_out" value="<?php if (!empty($_POST['check_out'])) echo $_POST['check_out'] ?>">
                        </td>
                    </tr>

                    <!-- チェックアウトのエラー -->
                        <tr class="room_name_error">
                            <td colspan="2" class="error"><?php if (!empty($error['check_out'])) echo $error['check_out'] ?></td>
                        </tr>


                    <tr>
                        <th>宿泊人数 </th>
                        <td>
                            <input type="number" name="capacity" min="1" value="<?php if (!empty($_POST['capacity'])) echo $_POST['capacity'] ?>">
                        </td>
                    </tr>

                        <tr class="room_name_error">
                            <td colspan="2" class="error"> <?php if (!empty($error['capacity'])) echo $error['capacity'] ?> </td>
                        </tr>


                    <tr>
                        <th>支払い方法 <br><span class="error"><?php if (!empty($error['payment'])) echo $error['peyment'] ?></span></th>
                        <td>
                            <div> <input type="radio" name="peyment" value="1" <?php if ($_POST['peyment'] == '1') echo 'checked' ?>>現金（現地支払い）</div>
                            <div><input type="radio" name="peyment" value="2" <?php if ($_POST['peyment'] == '2') echo 'checked' ?>>クレジットカード（オンライン決算）</div>
                            <div><input type="radio" name="peyment" value="3" <?php if ($_POST['peyment'] == '3') echo 'checked' ?>>クレジットカード（現地支払い）</div>
                        </td>
                    </tr>
                </table>
                <div class="date_error"><?php if (!empty($error['ather'])) echo $error['ather'] ?></div>
                <p class="submit_form"><button type="submit" >予約</p>
            </form>
        </main>
    </body> <?php endif; ?>

</html>