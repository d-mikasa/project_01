<?php
require_once('../class/Library.php');

//リダイレクト処理
if (empty($_SESSION['admin_auth'])) {
    header('Location: login.php');
    exit();
}


$Room = new Room();
$Reservation = new Reservation();

$year_month = $Room->getDaysList();

$pull_down = $Reservation->getPullDownList();
$room_pull_down = $pull_down['room'];

//ソートのメソッドを呼び出して格納
if (!empty($_POST['detail_id'])) {
    $list = $Room->getReservationState($_POST['detail_id'], $_POST['date'] . '-01');
}

if (!empty($_POST['export'])) {
$Room->export($list);
}


?>
<!-- ヘッダー部分読み込み -->
<?php require_once('parts/top.parts.php'); ?>
<main>
    <form action="" method="post">
        <div>
            日付選択
            <select name="date" id="target">
                <?php foreach ($year_month as $value) : ?>
                    <option value="<?=$value['date']?>"  <?=(!empty($_POST['date']) && ($_POST['date']) == $value['date']) ? 'selected' : '';?>>
                        <?= $value['date'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            客室選択
            <select name="detail_id" id="target">
                                <?php foreach ($room_pull_down  as $value) :?>
                                    <option value="<?=$value['id']?>" <?=(!empty($_POST['detail_id']) && ($_POST['detail_id']) == $value['id']) ? 'selected' : '';?>>
                                        <?=$value['name']?> (<?=$value['capacity']?>名様 ¥<?=number_format($value['price'])?>)
                                    </option>
                                <?php endforeach;?>
                            </select>
        </div>
        <p><input type="submit" value="表示する"></p>
        <button type="submit" value="export" name="export">表示内容のCSVダウンロード</button>
    </form>

<?php if(!empty($list)):?>
    <table class="reservation_list_table">
    <tr>
                <th class="reservation_list_date">日付</th>
                <td class="reservation_list_name">予約者名</td>
                <td class="reservation_list_room">部屋名</td>
                <td class="reservation_list_price">合計金額</td>
                <td class="reservation_list_number">宿泊人数</td>
            </tr>
    <?php foreach ($list as $value) : ?>
            <tr>
                <th class="reservation_list_date"><?=$value['date']?></th>
                <td class="reservation_list_name"><?=$value['name']?></td>
                <td class="reservation_list_room"><?=$value['room_detail_name']?></td>
                <td class="reservation_list_price"><?=($value['total_price']!=NULL) ? '¥' .number_format($value['total_price']) : '';?></td>
                <td class="reservation_list_number"><?=($value['number']!=NULL) ? $value['number'] . '名様' : ''; ?></td>
            </tr>

    <?php endforeach; ?>
    </table>
<?php endif;?>

</main>
</body>