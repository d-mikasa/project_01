<?php
require_once('../class/Library.php');

//リダイレクト処理
if (empty($_SESSION['auth'])) {
    header('Location: login.php');
}
if(empty($_SESSION['mode'])){
    header('Location: room_list.php');
}

//EDITからURL直打ちで来られた時の対処
if(empty($_POST)){
    header('Location: room_edit.php');
}

//空値、空白などが入っている場合、エラーを吐くのでここでNULLを上書き
if (!empty($_POST['set_data'])) {
    for ($i = 0; $i < count($_POST['set_data']); $i++) {

        $set_data[$i] = $_POST['set_data'][$i];

        if (empty($set_data[$i]['capacity'])) {
            $set_data[$i]['capacity'] = NULL;
        }
        if (empty($set_data[$i]['price'])) {
            $set_data[$i]['price'] = NULL;
        }

        if (empty($set_data[$i]['remarks'])) {
            $set_data[$i]['remarks'] = NULL;
        }
    }
}

//変なセッション値が残っていたときのため、一応分岐処理
if ($_SESSION['mode'] == 'edit') {
    $update = new AdminRoom;
    $update->room_update($_SESSION['data_id'], $set_data);
} else {
    $update = new AdminRoom;
    $update->room_update($_SESSION['data_id'], $set_data, $_SESSION['room_name']);
}

//セッションを初期化
unset($_SESSION['mode']);
unset($_SESSION['room_name']);
unset($_SESSION['data_id']);
?>

    <!-- ヘッダー部分読み込み -->
    <?php require_once('parts/top.parts.php'); ?>
    <div class = "end">更新しました</div>
        <!-- フッター部分読み込み -->
        <?php require_once('parts/footer.parts.php'); ?>
