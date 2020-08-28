<main class="topContent">
<div class="loginName">ログイン名[ <?= $_SESSION['admin_name'] ?> ]さん、ご機嫌いかがですか？</div>
    <div class="logout">
        <a href="logout.php">ログアウト</a>
    </div>
    <nav class="topNav">
        <a href="top.php">TOP</a>
        <a href="room_list.php">客室一覧</a>
    </nav>

    <?php getPage(); ?>