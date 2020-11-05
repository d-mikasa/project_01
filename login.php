<?php
require_once('class/Library.php');
$error = '';

if(!empty(($_GET['logout']))){
    unset($_SESSION['user_auth']);
}

if(!empty(($_SESSION['user_auth']))){
    header('Location: reservation.php');
    exit();
}

if (!empty($_POST['login'])) {
    if (empty($_POST['login_id']) or empty($_POST['pass'])) {
        $error = 'IDかパスワードが入力されていません';
    } else {
        $user = new User();
        // PDOクラスのメソッドを使う
        $error = $user->userLogin($_POST['login_id'], $_POST['pass']);
    }
}

?>
<?php require_once('rsv_parts/head_info.php');?>
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
                <div class="top_error">
                    <?=$error;?>
                </div>
                <div>
                    <input class="id_form" type="text" name="login_id" <?=!empty($_POST['login_id']) ? 'value=' . h($_POST['login_id']) : 'placeholder="Login_ID" '?>>
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