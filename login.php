<?php
require_once('class/Library.php');
$error = '';

unset($_SESSION['user_auth']);
unset($_SESSION['user_name']);
unset($_SESSION['user_id']);

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
    <?php require_once('rsv_parts/status_nav.php')?>
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
                    <input class="id_form" type="text" name="login_id" value = "<?=!empty($_POST['login_id']) ? h($_POST['login_id']) : '';?>" placeholder="Login_ID">
                </div>
                <div>
                    <input class="pass_form" type="password" name="pass" placeholder="Password">
                </div>
                <p class="submit_form">
                    <input type="submit" value="ログイン" class="login_button" name="login">
                </p>
            </form>
        </div>
    </main>
</body>
</html>