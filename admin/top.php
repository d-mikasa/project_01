<?php
session_start();

if ($_SESSION['admin_login'] == false) {
    header('Location: login.php');
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin_style.css">
    <title>TOP</title>
</head>

<body>
    <!-- ヘッダー部分読み込み -->
    <?php include('parts/nav.parts.php'); ?>
    <main>

    </main>
    <!-- フッター部分読み込み -->
    <?php include('parts/footer.parts.php'); ?>
</body>

</html>