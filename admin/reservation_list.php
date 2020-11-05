<?php
require_once('../class/Library.php');

//リダイレクト処理
if (empty($_SESSION['admin_auth'])) {
    header('Location: login.php');
    exit();
}
$Room = new Room;

//ソートのメソッドを呼び出して格納
$sortRoomList = $Room->sortRoomList('asc', 'id');

$year_month = $Room->getDays();
echo '<pre>';
print_r($_POST);
echo '</pre>';

if (!empty($_POST['detail_id'])) {
    $reservation_list = $Room->getRsvInfo($_POST['detail_id'], $_POST['date']);
    $day_list = $Room->getDaysList($_POST['date']);
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
                    <option value="<?= sprintf('%02d', $value['year']) . '-' . sprintf('%02d', $value['Month']) ?>">
                        <?= $value['year'] . '-' . $value['Month'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            客室選択
            <select name="detail_id" id="target">
                <?php foreach ($sortRoomList as $value) : ?>
                    <option value="<?= $value['id'] ?>">
                        <?= $value['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <p><input type="submit" value="送信"></p>
    </form>

    <?php foreach ($day_list as $value2) : ?>
        <table>
            <tr>
                <th><?=$value2['date']?></th>
                <?php foreach ($reservation_list as  $value1) : ?>
                <td><?=($value2['date'] == $value2['date'])?$value1['reservation_id']:''?></td>
                <?php endforeach; ?>
            </tr>
        </table>

    <?php endforeach; ?>

</main>
</body>