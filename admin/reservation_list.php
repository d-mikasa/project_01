<?php
require_once('../class/Library.php');

//リダイレクト処理
if (empty($_SESSION['admin_auth'])) {
    header('Location: login.php');
    exit();
}

$Room = new Room();
$Reservation = new Reservation();

//年月を取得
$year_month = $Room->getDaysList();

//プルダウンの部屋情報を流用
$pull_down = $Reservation->getPullDownList();
$room_pull_down = $pull_down['room'];

//ソートのメソッドを呼び出して格納
if (!empty($_GET['detail_id'])) {
    $list = $Room->getReservationState($_GET['detail_id'], $_GET['date'] . '-01');
}

//書き出しのボタンが押されたらCSV書き出し処理
if (!empty($_GET['export'])) {
    $Room->export($list);
}
?>
<!-- ヘッダー部分読み込み -->
<?php require_once('parts/top.parts.php');?>
<main>
    <form action="" method="get">
        <div class="reservation_list_form">
            <div>
                日付選択
                <select name="date" id="target">
                    <?php foreach($year_month as $value):?>
                        <option value="<?=$value['date']?>"<?=(!empty($_GET['date']) && ($_GET['date']) == $value['date']) ? 'selected' : '';?>>
                            <?=$value['date']?>
                        </option>
                    <?php endforeach;?>
                </select>
            </div>
            <div>
                客室選択
                <select name="detail_id" id="target">
                    <?php foreach ($room_pull_down as $value) :?>
                        <option value="<?=$value['id']?>" <?=(!empty($_GET['detail_id']) && ($_GET['detail_id']) == $value['id']) ? 'selected' : '';?>>
                            <?=$value['name']?>(<?=$value['capacity']?>名様 ¥<?=number_format($value['price'])?>)
                        </option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="reservation_list_button">
            <div><input type="submit" value="表示する"></div>
            <div><button type="submit" value="export" name="export" class="csv_download">表示内容のCSVダウンロード</button></div>
        </div>
    </form>
    <?php if(!empty($list)):?>
        <table class="reservation_list_table">
            <tr>
                <th class="reservation_list_date">日付</th>
                <td class="reservation_list_name">予約者名</td>
                <td class="reservation_list_number">宿泊人数</td>
                <td class="reservation_list_price">合計金額</td>
            </tr>
            <?php foreach ($list as $value) :?>
                <tr>
                    <th class="reservation_list_date"><?=$value['date']?></th>
                    <td class="reservation_list_name"><?=$value['name']?></td>
                    <td class="reservation_list_number"><?=($value['number']!=NULL) ? $value['number'] . '名様' : '';?></td>
                    <td class="reservation_list_price"><?=($value['total_price']!=NULL) ? '¥' .number_format($value['total_price']) : '';?></td>
                </tr>
            <?php endforeach;?>
        </table>
    <?php endif;?>
</main>
</body>