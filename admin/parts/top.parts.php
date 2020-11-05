<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin_style.css">
    <title>CICACU管理</title>
</head>

<body>

<main class="topContent">
    <div class="loginName">ログイン名[<?=$_SESSION['admin_name'] ?>]さん、ご機嫌いかがですか？</div>
    <div class="logoutLink">
        <a href="logout.php">ログアウト</a>
    </div>
    <nav class="topNav">
        <a href="top.php">TOP</a>
        <a href="room_list.php">客室一覧</a>
        <a href="reservation_list.php">客室情報</a>
    </nav>

    <?php getPage(); ?>