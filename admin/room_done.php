<?php
require_once('../class/Library.php');

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
    $update = new UpdateDetail;
    $update->update($_SESSION['data_id'], $set_data);
} else {
    $update = new UpdateDetail;
    $hoge = $update->update($_SESSION['data_id'], $set_data, $_SESSION['room_name']);
}

print_r('<pre>');
print_r($hoge);
print_r('</pre>');

//セッションを初期化
unset($_SESSION['tmp_path']);
unset($_SESSION['img_name']);
unset($_SESSION['mode']);
unset($_SESSION['room_name']);
?>

    <!-- ヘッダー部分読み込み -->
    <?php include('parts/top.parts.php'); ?>
    更新しました
        <!-- フッター部分読み込み -->
        <?php include('parts/footer.parts.php'); ?>

</html>