<?php
require_once('../class/Library.php');

if ($_SESSION['auth'] == 0) {
    header('Location: login.php');
}

?>
 <!-- ヘッダー部分読み込み -->
    <?php include('parts/top.parts.php'); ?>
    <main>

    </main>
    <!-- フッター部分読み込み -->
    <?php include('parts/footer.parts.php'); ?>

</html>