<?php
require_once('../class/Library.php');

//リダイレクト処理
if (empty($_SESSION['auth'])) {
    header('Location: login.php');
}

//EDITからURL直打ちで来られた時の対処
if (empty($_POST['set_data'])) {
    header('Location: room_edit.php');
}

$Room = new Room;
$message = $Room->updateRoom($_GET['id'], $_POST['set_data'], $_GET['mode'] , $_POST['set_data']);

?>

<!-- ヘッダー部分読み込み -->
<?php require_once('parts/top.parts.php'); ?>
<div class="end"><?=$message?></div>
<!-- フッター部分読み込み -->
<?php require_once('parts/footer.parts.php'); ?>