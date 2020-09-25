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
        $error = $user->Login($_POST['login_id'], $_POST['pass']);
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
    <link rel="stylesheet" href="./css/reservation.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

    <!--Googleフォント-->
    <!--font-family: 'Prompt', sans-serif;-->
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
</head>



<body class="background_login">
    <nav>
        <img src="img/logo.png" class="header_logo">
        <div class="status">
            <div class = "status_now">ログイン</div>
            <span class="triangle"></span>
            <div class = "status_none">情報入力</div>
            <span class="triangle"></span>
            <div class = "status_none">内容確認</div>
            <span class="triangle"></span>
            <div class = "status_none">予約完了</div>
        </div>
    </nav>
    <main class="login_main">
        <div class="title_group">
            <div class="top_title">
                <span>C</span>
                <span>I</span>
                <span>C</span>
                <span>A</span>
                <span>C</span>
                <span>U</span>
            </div>
            <div class="bottom_title">ログインページ</div>

            <form action="" method="post">
                <div class="error">
                    <?= $error; ?>
                </div>
                <div>
                    <input class="id_form" type="text" name="login_id" <?php if (!empty($_POST['login_id'])) {
                                                                            echo 'value = "' . h($_POST['login_id']) . '"';
                                                                        } else {
                                                                            echo 'placeholder="Login_ID"';
                                                                        } ?>>
                </div>
                <div>
                    <input class="pass_form" type="password" name="pass" placeholder="Password">
                </div>
                <p class="submit_form"><input type="submit" value="ログイン" class="login_button" name="login"></p>
            </form>
        </div>
    </main>
</body>

</html>