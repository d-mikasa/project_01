<?php
require_once('class/Library.php');

$pdo = new RoomShow();
$pull_down_list = $pdo->room();

if ($_SESSION['user_auth'] == false) {
    header('Location: login.php');
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap-reboot.min.css">
    <link rel="stylesheet" href="./css/reservation.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

    <!--Googleフォント-->
    <!--font-family: 'Prompt', sans-serif;-->
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
</head>

<body class="background_reservation">
<?= getNav('reservation'); ?>
    <main class="reservation_main">
        <form action="reservation_conf.php" method="post">
            <div class="titles">情報入力欄</div>
            <table>
                <tr>
                    <th>部屋名</th>
                    <td>
                        <select name="detail_id" id="target">
                            <?php foreach ($pull_down_list as $value) : ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?> (<?= $value['capacity'] ?>名様  ¥<?= number_format($value['price'])?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>チェックイン</th>
                    <td>
                        <input type="date" name="check_in">
                    </td>
                </tr>
                <tr>
                    <th>チェックアウト</th>
                    <td>
                        <input type="date" name="check_out">
                    </td>
                </tr>
                <tr>
                    <th>宿泊人数</th>
                    <td>
                        <input type="number" name="capacity" min="1">
                    </td>
                </tr>
                <tr>
                    <th>支払い方法</th>
                    <td>
                        <div> <input type="radio" name="peyment" value="1" checked>現金（現地支払い）</div>
                        <div><input type="radio" name="peyment" value="2">クレジットカード（オンライン決算）</div>
                        <div><input type="radio" name="peyment" value="3">クレジットカード（現地支払い）</div>
                    </td>
                </tr>

            </table>
            <p class="submit_form"><input type="submit" value="予約"></p>
        </form>

    </main>
</body>

</html>