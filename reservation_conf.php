<?php
require_once('class/Library.php');
$error = [];



$pdo = new RoomShow();
//プルダウンの内容を取得する
$room = $pdo->room();

//選択した部屋の内容を取得する
$select = $pdo->room_select($_POST['room_name']);

//選択した部屋が予約されているかを取得する
$reservation = $pdo->room_reservation($_POST['room_name']);

print_r('<pre>');
print_r($select);
print_r('</pre>');





/*
バリデーション群
*/
//部屋のバリデーション
// if (empty($_POST['room_name'])) { //部屋名が空欄になることはない？
//     $error['room'] = '部屋名が空欄です';
// }

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
    if (strtotime($_POST['check_in']) >= (time() + 90) or strtotime($_POST['check_out']) >= (time() + 90)) {
        $error['check_out'] = '３ヶ月以内のご予約のみ承っております';
    }
}

//宿泊日数系のバリデーションチェック

//宿泊人数のバリデーション
if (empty($_POST['capacity'])) {
    $error['capacity'] = '宿泊人数が空欄です';
} else {
    //部屋詳細から該当の宿泊人数のプランがあるかを検索する
    if ($_POST['capacity'] != $select['capacity']) {
        if ($_POST['capacity'] < $select['capacity']) {
            $error['capacity'] = '宿泊人数が少ないです。';
        } else {
            $error['capacity'] = '宿泊人数が多いです。';
        }
    }
}

//支払い方法のバリデーション
// if (empty($_POST['peyment'])) { //支払い方法が空欄になることはない？
//     $error['peyment'] = '支払い方法が空欄です';
// }
print_r('<pre>');
print_r($reservation);
print_r('</pre>');
//選択した部屋が予約されていないかどうか
// if ($reservation != 'not reservation room') {

    // 予約情報に選択した部屋番号が存在していた場合、すでに予約済みかどうかを判定する
//     foreach($reservation as $value){

//     }
//     if ($reservation['status'] == 1) {


//         $error['ather'] = 'すでに予約されています';
//     }
// }

echo $error['check_in'] . '<br>';
echo $error['check_out'] . '<br>';
echo $error['capacity'] . '<br>';
echo $error['ather'] . '<br>';


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
    <link rel="stylesheet" href="./css/reservation_style.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

</head>

<body class="reservation">
    <header>
        <h1>CICACU</h1>
        <h2>予約ページ</h2>
    </header>
    <contaner class="step_group">
        <div class="step_conf">入力</div>
        <p>→</p>
        <div class="step_input">確認</div>
        <p>→</p>
        <div class="step_done">完了</div>
    </contaner>
    <main class="reservation_main">
        <form action="reservation_conf.php" method="post">
            <div class="titles">情報入力欄</div>
            <table>
                <tr>
                    <th>部屋名</th>
                    <td>
                        <?= $_POST['room_name'] ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>チェックイン</th>
                    <td>
                        <?= $_POST['check_in'] ?>

                    </td>
                </tr>
                <tr>
                    <th>チェックアウト</th>
                    <td>
                        <?= $_POST['check_out'] ?>
                    </td>
                </tr>
                <tr>
                    <th>宿泊人数</th>
                    <td>
                        <?= $_POST['capacity'] ?>
                    </td>
                </tr>
                <tr>
                    <th>支払い方法</th>
                    <td>
                        <?php
                        switch ($_POST['peyment']) {
                            case 'card_after':
                                echo 'クレジットカード（オンライン決算）';
                                break;
                            case 'card_now':
                                echo 'クレジットカード（現地支払い）';
                                break;
                            default:
                                echo '現金（現地支払い）';
                                break;
                        }

                        ?>
                    </td>
                </tr>

            </table>
            <p class="submit_form">
                以上の内容でお間違い無いでしょうか？
                <input type="submit" value="確認">
            </p>
        </form>

    </main>
</body>

</html>