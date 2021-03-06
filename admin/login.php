<?php
require_once('../class/Library.php');

$error = '';
unset($_SESSION['admin_auth']);

if (!empty($_POST['attestation'])) {
    //バリデーション処理
    if (empty($_POST['id']) or empty($_POST['pass'])) {
        $error = 'IDかパスワードが入力されていません';
    } else {
        $admin_user = new AdminUser();
        // PDOクラスのメソッドを使う
        $error = $admin_user->adminUserLogin($_POST['id'], $_POST['pass']);
    }
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap-reboot.min.css">
    <title>CICACUログインページ</title>
</head>

<body>
    <header>
        <h1>CICACU 管理画面ログイン</h1>
    </header>
    <main class = "loginForm">
        <form action="" method="post">
            <div class="error">
                <?= $error; ?>
            </div>
            <p class="loginIdForm">ログインID<input type="text" name="id" size="30" value="<?=(!empty($_POST['id']) ? h($_POST['id']) : '');?>"></p>
            <p class="loginPassForm">パスワード<input type="password" name="pass" size="30"></p>
            <p class = "loginSubmitButton"><input type="submit" value="認証" name="attestation"></p>
        </form>
    </main>
</body>

</html>