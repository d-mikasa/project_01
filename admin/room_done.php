<?php
require_once('../class/Library.php');

//リダイレクト処理
if (empty($_SESSION['auth'])) {
    header('Location: login.php');
    exit();
}

//EDITからURL直打ちで来られた時の対処
if (empty($_POST['name']) and empty($_POST['detail'])) {
    header('Location: room_edit.php');
    exit();
}

$Room = new Room;
$updateRoom = $Room->updateRoom($_GET['id'], $_POST, $_GET['mode']);

?>

<!-- ヘッダー部分読み込み -->
<?php require_once('parts/top.parts.php');?>
<div class="end"><?= $updateRoom?></div>
<!-- フッター部分読み込み -->
<?php require_once('parts/footer.parts.php');?>