<?php
require_once ('library.php');

$error = '';
$_SESSION['auth'] = 0;

if(!empty($_POST['id_form'])){
    //バリデーション処理
    if (empty($_POST['id']) or empty($_POST['pass'])) {
        $error = 'IDかパスワードが入力されていません';
    }

    if (empty($error)) {
        $admin_user = new AdminUser();

        // PDOクラスのメソッドを使う
        $error = $admin_user->checkUser($_POST['id'], $_POST['pass']);
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
    <title>Login</title>
</head>

<body>
    <header>
        <h1>CICACU部屋管理　管理画面ログイン</h1>
    </header>
    <main>
        <form action="" method="post">
            <div class = "error">
                <?php echo $error; ?>
            </div>
            <p class="id">ログインID<input type="text" name="id" size="30" value="<?php if (!empty($_POST['id'])) echo $_POST['id']  ?>"></p>
            <p class="pas">パスワード<input type="password" name="pass" size="30"></p>
            <p><input type="submit" value="認証" name = "id_form"></p>
        </form>
    </main>
</body>

</html>