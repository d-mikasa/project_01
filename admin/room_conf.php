<?php
require_once('../class/Library.php');

//リダイレクト処理
if (empty($_SESSION['auth'])) {
    header('Location: login.php');
}
//EDITからURL直打ちで来られた時の対処
// if(empty($_POST)){
//     header('Location: room_list.php');
//

echo '<pre>';
print_r($_POST);
echo '</pre>';

$room_name = $_POST['plan']['room_name'][0];
$room_detail = $_POST['plan']['room_detail'];

echo '<pre>';
print_r($room_name);
echo '</pre>';
echo '<pre>';
print_r($room_detail);
echo '</pre>';


?>

<!-- ヘッダー部分読み込み -->
<?php require_once('parts/top.parts.php'); ?>

<!--新規作成モードなら新規部屋名を表示 -->

    <table class = "conf_newroom">
        <tr>
            <th>新規部屋名</th>
            <td><?= $room_name ?></td>
        </tr>
    </table>

<form action="room_done.php" method="post" id = "confForm">
    <?php for ($i = 0; $i < count($room_detail); $i++) : ?>
        <input type="hidden" name="set_data[<?= $i ?>][capacity]" value="<?= $room_detail[$i]['capacity'] ?>">
        <input type="hidden" name="set_data[<?= $i ?>][price]" value="<?= $room_detail[$i]['price'] ?>">
        <input type="hidden" name="set_data[<?= $i ?>][remarks]" value="<?= $room_detail[$i]['remarks'] ?>">

        <table class="roomedit_table">
            <tr>
                <th rowspan="3" class = "confPlan">部屋[<?= $i + 1 ?>]</th>
                <th class = "confcapa">人数</th>
                <td class = "confcapa_detail"><?= $room_detail[$i]['capacity'] ?></td>
            </tr>
            <tr>
                <th class = "confPrice">料金</th>
                <td class = "confPrice_detail"><?= $room_detail[$i]['price'] ?></td>
            </tr>
            <tr>
                <th class = "confCome">コメント</th>
                <td class = "confCome_detail"><?= $room_detail[$i]['remarks'] ?></td>
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