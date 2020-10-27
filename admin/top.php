<?php
require_once('../class/Library.php');

if (empty($_SESSION['auth'])) {
    header('Location: login.php');
    exit();
}

?>
<!-- ヘッダー部分読み込み -->
    <?php require_once('parts/top.parts.php'); ?>
    <main>

    </main>
    <!-- フッター部分読み込み -->
    <?php require_once('parts/footer.parts.php'); ?>