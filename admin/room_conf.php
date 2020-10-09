<?php
require_once('../class/Library.php');

//リダイレクト処理
if (empty($_SESSION['auth'])) {
    header('Location: login.php');
}
//EDITからURL直打ちで来られた時の対処
// if(empty($_POST)){
//     header('Location: room_list.php');
// }

//配列[1]は新規作成のフラグとしてpostさせているため、ここで配列を入れ直す
if ($_GET['mode'] == 'create') {
    $temp = $_POST['plan'][0];
    $_SESSION['room_name'] = $temp['room_name'];
    for ($i = 1; $i < count($_POST['plan']); $i++) {
        $set_data[$i - 1] = $_POST['plan'][$i];
    }
} else {
    for ($i = 1; $i <= count($_POST['plan']); $i++) {
        $set_data[$i - 1] = $_POST['plan'][$i];
    }
}

?>

<!-- ヘッダー部分読み込み -->
<?php require_once('parts/top.parts.php'); ?>

<!--新規作成モードなら新規部屋名を表示 -->
<?php if ($_GET['mode'] == 'create') : ?>
    <table class = "conf_newroom">
        <tr>
            <th>新規部屋名</th>
            <td><?= $_SESSION['room_name'] ?></td>
        </tr>
    </table>
<?php endif; ?>

<form action="room_done.php" method="post" id = "confForm">
    <?php for ($i = 0; $i < count($set_data); $i++) : ?>
        <input type="hidden" name="set_data[<?= $i ?>][capacity]" value="<?= $set_data[$i]['capacity'] ?>">
        <input type="hidden" name="set_data[<?= $i ?>][price]" value="<?= $set_data[$i]['price'] ?>">
        <input type="hidden" name="set_data[<?= $i ?>][remarks]" value="<?= $set_data[$i]['remarks'] ?>">

        <table class="roomedit_table">
            <tr>
                <th rowspan="3" class = "confPlan">部屋[<?= $i + 1 ?>]</th>
                <th class = "confcapa">人数</th>
                <td class = "confcapa_detail"><?= $set_data[$i]['capacity'] ?></td>
            </tr>
            <tr>
                <th class = "confPrice">料金</th>
                <td class = "confPrice_detail"><?= $set_data[$i]['price'] ?></td>
            </tr>
            <tr>
                <th class = "confCome">コメント</th>
                <td class = "confCome_detail"><?= $set_data[$i]['remarks'] ?></td>
            </tr>
        </table>
        <p><br></p>
    <?php endfor; ?>
    <div class = "confDone">
    <p><input type="submit" value="確認" class = "DoneAdd"></p>
    <p><input type="submit" value="キャンセル" formaction="room_edit.php" class = "Donenot"></p>
    </div>
</form>
<!-- フッター部分読み込み -->
<?php require_once('parts/footer.parts.php'); ?>