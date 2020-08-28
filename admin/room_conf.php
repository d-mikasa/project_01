<?php
require_once('../class/Library.php');

//test edit comment
//hogehoge
//banananananana

//配列[1]は新規作成のフラグとしてpostさせているため、ここで配列を入れ直す
if(!empty($_POST['plan'][0])){
$temp= $_POST['plan'][0];
$_SESSION['room_name'] = $temp['room_name'];
for ($i = 1; $i < count($_POST['plan']); $i++) {
    $set_data[$i - 1] = $_POST['plan'][$i];
}
}else{
    for ($i = 1; $i < count($_POST['plan']); $i++) {
        $set_data[$i - 1] = $_POST['plan'][$i];
    }
}

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

    <!--新規作成モードなら新規部屋名を表示 -->
    <?php if ($_SESSION['mode'] == 'create') : ?>
        <table>
            <tr>
                <th>新規部屋名</th>
                <td><?= $_SESSION['room_name'] ?></td>
            </tr>
        </table>
    <?php endif; ?>


    <form action="room_done.php" method="post">
        <?php for ($i = 0; $i < count($set_data); $i++) : ?>
            <input type="hidden" name="set_data[<?= $i ?>][capacity]" value=<?= $set_data[$i]['capacity'] ?>>
            <input type="hidden" name="set_data[<?= $i ?>][price]" value=<?= $set_data[$i]['price'] ?>>
            <input type="hidden" name="set_data[<?= $i ?>][remarks]" value=<?= $set_data[$i]['remarks'] ?>>

            <table class="roomedit_table">
                <tr>
                    <th rowspan="3">プラン[<?= $i+1 ?>]</th>
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
    <!-- フッター部分読み込み -->
    <?php include('parts/footer.parts.php'); ?>
</body>

</html>