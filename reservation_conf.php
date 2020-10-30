<?php
require_once('class/Library.php');
$rsv = new Rsv();

echo '<pre>';
print_r($_POST);
echo '</pre>';

//プルダウンの内容を取得する
$pull_down_list = $rsv->getPullDownList();

//予約状況を確認するための配列を取得する
$reservation_check = $rsv->reservation_check($_POST['detail_id']);

//選択した部屋の内容を取得する
$room_detail = $rsv->room_detail($_POST['detail_id']);

//エラー内容変数の初期化
$error = [];

////////////////////////////////////////////日付系のバリデーションまとめ////////////////////////////////////////////
//チェックインのバリデーション
if (empty($_POST['check_in'])) {
    $error['check_in'] = 'チェックイン日時が空欄です';
} else {
    if (strtotime($_POST['check_in']) < strtotime("-1 day")) {
        $error['check_in'] = 'チェックイン日時が過去を指定しています';
    }
}

//チェックアウトのバリデーション
if (empty($_POST['check_out'])) {
    $error['check_out'] = 'チェックアウト日時が空欄です';
} else {
    if (strtotime($_POST['check_out']) < strtotime("-1 day")) {
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
                console_log($ch_days);
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

<?php require_once('rsv_parts/head_info.php'); ?>

<body class="background_conf"> <?php if (empty($error)) : ?>
        <?= getNav('conf') ?>
        <!--
        エラーが無く、送信することが可能な画面
        -->
        <main class="reservation_main">
            <form action="reservation_done.php" method="post">


                <!--トークンを送信-->
                <input type="hidden" name="csrf_token" value="<?= $rsv->getToken() ?>">
                <!--実際に送信する情報群-->
                <input type="hidden" name="detail_id" value="<?= $_POST['detail_id'] ?>"><!-- 詳細番号 -->
                <input type="hidden" name="check_in" value="<?= $_POST['check_in'] ?>"><!-- チェックイン日 -->
                <input type="hidden" name="check_out" value="<?= $_POST['check_out'] ?>"><!-- チェックアウト日 -->
                <input type="hidden" name="capacity" value="<?= $_POST['capacity'] ?>"><!-- 宿泊人数 -->
                <input type="hidden" name="peyment" value="<?= $_POST['peyment'] ?>"><!-- 支払い方法 -->
                <input type="hidden" name="price" value="<?= $room_detail['price'] ?>"><!-- 値段 -->
                <input type="hidden" name="detail_name" value="<?= $room_detail['detail_name'] ?>"><!-- 詳細名 -->
                <input type="hidden" name="room_id" value="<?= $room_detail['id'] ?>"><!-- 部屋番号 -->
                <input type="hidden" name="room_name" value="<?= $room_detail['name'] ?>"><!-- 部屋名 -->

                <div class="titles">ご予約内容確認</div>
                <table class="conf_check_table">
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

                <div class="final_check">以上の内容でお間違い無いでしょうか？</div>
                <p class="submit_form">

                    <!-- <button type="submit" value="確認">確認</button>
                    <button type="button" value="キャンセル" onclick="location.href='reservation_conf.php'" >キャンセル</button> -->

                    <button type="button" onclick="multipleaction('reservation_done.php')">確認</button>
                    <button type="button" onclick="multipleaction('reservation.php')">キャンセル</button>
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
                            <select name="detail_id" id="target">
                                <?php foreach ($pull_down_list as $value) : ?>
                                    <option value="<?= $value['id'] ?>" <?php if (($_POST['detail_id']) == $value['id']) echo 'selected' ?>>
                                        <?= $value['name'] ?> (<?= $value['capacity'] ?>名様 ¥<?= number_format($value['price']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
                <p class="submit_form"><button type="submit">予約</p>
            </form>
        </main>
    </body> <?php endif; ?>

<script>
    function multipleaction(u) {
        var f = document.querySelector("form");
        var a = f.setAttribute("action", u);
        document.querySelector("form").submit();
    }
</script>


</html>