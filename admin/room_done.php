<?php
require_once('../class/Library.php');

//リダイレクト処理
if (empty($_SESSION['auth'])) {
    header('Location: login.php');
}

//EDITからURL直打ちで来られた時の対処
if (empty($_POST)) {
    header('Location: room_edit.php');
}

$update = new Room;
$update->roomUpdate($_GET['id'], $_POST['set_data']['room_detail'], $_POST['set_data']['room_name'], $_GET['mode']);

?>

<!-- ヘッダー部分読み込み -->
<?php require_once('parts/top.parts.php'); ?>
<div class="end">更新しました</div>
<!-- フッター部分読み込み -->
<?php require_once('parts/footer.parts.php'); ?>