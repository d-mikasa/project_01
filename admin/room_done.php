<?php
require_once('../class/Library.php');
const IMGS_PATH = '../img/';
if (isset($_SESSION['tmp_path'])) {
    if (rename($_SESSION['tmp_path'], IMGS_PATH . $_SESSION['img_name'])) {
        echo "ファイルの移動に成功しました";
    } else {
        echo "ファイルの移動に失敗しました";
    }
}

if (!empty($_POST['set_data'])) {
    for ($i = 0; $i < count($_POST['set_data']); $i++) {
        $set_data[$i] = $_POST['set_data'][$i];
    }
}


$update = new UpdateDetail;
$update -> update($_SESSION['data_id'],$set_data,$_SESSION['room_name']);


unset($_SESSION['tmp_path']);
unset($_SESSION['img_name']);
unset($_SESSION['mode']);
unset($_SESSION['room_name']);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin_style.css">
    <title>ROOM_LIST</title>
</head>

<body>
    <!-- ヘッダー部分読み込み -->
    <?php include('parts/nav.parts.php'); ?>
    更新しました
</body>

</html>