<?php
require_once('class/Library.php');
const IMGS_PATH = '../img/temp/';

for ($i = 0; $i < count($_POST['plan']); $i++) {
    $set_data[$i] = $_POST['plan'][$i];
}
// アクセスを許可する
exec('sudo chmod 0777 ' . IMGS_PATH);


if (!empty($_FILES)) {
    if ($_FILES['userfile']['error'] == UPLOAD_ERR_OK) {
        $name = $_FILES['userfile']['name'];
        $name = mb_convert_encoding($name, 'cp932', 'utf8');
        $temp = $_FILES['userfile']['tmp_name'];
        $result = move_uploaded_file($temp, IMGS_PATH . $name);
        if ($result == true) {
            $message = 'ファイルをアップロードしました';
            $_SESSION['tmp_path'] = IMGS_PATH . $name;
            $_SESSION['img_name'] = $name;
        } else {
            $message = 'ファイルの移動に失敗しました';
        }
    } elseif ($_FILES['userfile']['error'] == UPLOAD_ERR_NO_FILE) {
        $message = 'ファイルがアップロードされませんでした';
    } else {
        $message = 'ファイルのアップロードに失敗しました';
    }
}

// 元の状態に戻す
echo $message;

print_r($_FILES);


?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin_style.css">
    <title>Document</title>
</head>

<body>
    <!-- ヘッダー部分読み込み -->
    <?php include('parts/nav.parts.php'); ?>

    <form action="room_done.php" method="post">
        <?php for ($i = 0; $i < count($set_data); $i++) : ?>
            <input type="hidden" name="set_data[<?= $i ?>][capacity]" value=<?= $set_data[$i]['capacity'] ?>>
            <input type="hidden" name="set_data[<?= $i ?>][price]" value=<?= $set_data[$i]['price'] ?>>
            <input type="hidden" name="set_data[<?= $i ?>][remarks]" value=<?= $set_data[$i]['remarks'] ?>>

            <table class="roomedit_table">
                <tr>
                    <th rowspan="3">プラン[<?= $i ?>]</th>
                    <th>人数</th>
                    <td><?= $set_data[$i]['capacity'] ?></td>
                </tr>
                <tr>
                    <th>料金</th>
                    <td><?= $set_data[$i]['price'] ?></td>
                </tr>
                <tr>
                    <th>コメント</th>
                    <td><?= $set_data[$i]['remarks'] ?></td>
                </tr>
            </table>
            <p><br></p>
        <?php endfor; ?>
        <p><input type="submit" value="確認"></p>
        <p><input type="submit" value="キャンセル" formaction="room_edit.php"></p>
    </form>

</body>

</html>