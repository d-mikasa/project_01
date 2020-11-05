<?php
require_once('../class/Library.php');

//リダイレクト処理
if (empty($_SESSION['admin_auth'])) {
    header('Location: login.php');
    exit();
}

$room_name = $_POST['name'][0];
$room_detail = $_POST['detail'];
?>

<!-- ヘッダー部分読み込み -->
<?php require_once('parts/top.parts.php');?>

<form action="room_done.php?mode=<?=$_GET['mode']?>&id=<?=$_GET['id']?>" method="post" id="confForm">
    <input type="hidden" name="name" value="<?=$room_name?>">
    <table class="conf_newroom">
        <tr>
            <th>新規部屋名</th>
            <td><?=$room_name?></td>
        </tr>
    </table>
    <?php foreach($room_detail as $key => $value):?>
        <input type="hidden" name="detail[<?=$key?>][capacity]" value="<?=$value['capacity']?>">
        <input type="hidden" name="detail[<?=$key?>][price]" value="<?=$value['price']?>">
        <input type="hidden" name="detail[<?=$key?>][remarks]" value="<?=$value['remarks']?>">
        <table class="roomedit_table">
            <tr>
                <th rowspan="3" class="confPlan">部屋[<?=$key + 1?>]</th>
                <th class="confcapa">人数</th>
                <td class="confcapa_detail"><?=$value['capacity']?></td>
            </tr>
            <tr>
                <th class="confPrice">料金</th>
                <td class="confPrice_detail"><?=$value['price']?></td>
            </tr>
            <tr>
                <th class="confCome">コメント</th>
                <td class="confCome_detail"><?=$value['remarks']?></td>
            </tr>
        </table>
    <?php endforeach; ?>
    <div class="confDone">
        <p><input type="submit" value="確認" class="DoneBtn"></p>
        <p><input type="submit" value="キャンセル" formaction="room_edit.php?mode=<?=$_GET['mode']?>&id=<?=$_GET['id']?>" class="CancelBtn"></p>
    </div>
</form>
<!-- フッター部分読み込み -->
<?php require_once('parts/footer.parts.php');?>