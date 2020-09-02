<?php
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
    <main class = "reservation_main">
        <form action="reservation_conf.php" method="post">
            <div class="titles">情報入力欄</div>
            <table>
                <tr>
                    <th>部屋名</th>
                    <td>
                        部屋A
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>チェックイン</th>
                    <td>
                        ○○○○年○月○日
                    </td>
                </tr>
                <tr>
                    <th>チェックアウト</th>
                    <td>
                        ○○○○年○月○日
                    </td>
                </tr>
                <tr>
                    <th>宿泊人数</th>
                    <td>
                        ○人
                    </td>
                </tr>
                <tr>
                    <th>支払い方法</th>
                    <td>
                        <div>現金（現地支払い）</div>
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