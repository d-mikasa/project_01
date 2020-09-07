<?php
require_once('class/Library.php');
$error = '';
unset($_SESSION['user_auth']);

if (!empty($_POST['login'])) {
    if (empty($_POST['login_id']) or empty($_POST['pass'])) {
        $error = 'IDかパスワードが入力されていません';
    } else {
        $user = new UserLogin();
        // PDOクラスのメソッドを使う
        $error = $user->Login($_POST['login_id'],$_POST['pass']);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap-reboot.min.css">
    <link rel="stylesheet" href="./css/reservation_style.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

</head>

<body class="reservation">
    <header>
        <h1>CICACU </h1>
        <h2>ログインページ</h2>
    </header>
    <main class = "login_main">
        <form action="" method="post">
        <div class = "titles">ログイン情報</div>
        <div class="error">
                <?= $error; ?>
            </div>
            <table>
                <tr>
                    <th>ログインID</th>
                    <td>
                        <input type="text" name="login_id" value="<?=(!empty($_POST['login_id']) ? h($_POST['login_id']) : '');?>">
                    </td>
                </tr>
                <tr>
                    <th>パスワード</th>
                    <td>
                        <input type="password" name="pass">
                    </td>
                </tr>

            </table>
            <p class = "submit_form"><input type="submit" value="ログイン" class = "login_button" name = "login"></p>
        </form>

    </main>
</body>

</html>