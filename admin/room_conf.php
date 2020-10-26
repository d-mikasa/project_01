<?php
require_once('../class/Library.php');

//リダイレクト処理
if (empty($_SESSION['auth'])) {
    header('Location: login.php');
}

$room_name = $_POST['set_data']['name'][0];
$room_detail = $_POST['set_data']['detail'];
?>

<!-- ヘッダー部分読み込み -->
<?php require_once('parts/top.parts.php');?>

<form action="room_done.php?mode=<?=$_GET['mode']?>&id=<?=$_GET['id']?>" method="post" id="confForm">
    <input type="hidden" name="set_data[name]" value="<?=$room_name?>">

    <table class="conf_newroom">
        <tr>
            <th>新規部屋名</th>
            <td><?=$room_name?></td>
        </tr>
    </table>
    <?php for ($i = 0; $i < count($room_detail); $i++) :?>
        <input type="hidden" name="set_data[detail][<?=$i?>][capacity]" value="<?=$room_detail[$i]['capacity']?>">
        <input type="hidden" name="set_data[detail][<?=$i?>][price]" value="<?=$room_detail[$i]['price']?>">
        <input type="hidden" name="set_data[detail][<?=$i?>][remarks]" value="<?=$room_detail[$i]['remarks']?>">

        <table class="roomedit_table">
            <tr>
                <th rowspan="3" class="confPlan">部屋[<?=$i + 1?>]</th>
                <th class="confcapa">人数</th>
                <td class="confcapa_detail"><?=$room_detail[$i]['capacity']?></td>
            </tr>
            <tr>
                <th class="confPrice">料金</th>
                <td class="confPrice_detail"><?=$room_detail[$i]['price']?></td>
            </tr>
            <tr>
                <th class="confCome">コメント</th>
                <td class="confCome_detail"><?=$room_detail[$i]['remarks']?></td>
            </tr>
        </table>
    <?php endfor;?>
    <div class="confDone">
        <p><input type="submit" value="確認" class="DoneBtn"></p>
        <p><input type="submit" value="キャンセル" formaction="room_edit.php?mode=<?=$_GET['mode']?>&id=<?=$_GET['id']?>" class="CancelBtn"></p>
    </div>
</form>
<!-- フッター部分読み込み -->
<?php require_once('parts/footer.parts.php');?>