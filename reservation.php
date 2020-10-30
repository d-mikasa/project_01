<?php
require_once('class/Library.php');
$Rsv = new Rsv();

$pull_down_list = $Rsv->getPullDownList();

if ($_SESSION['user_auth'] == false) {
    header('Location: login.php');
    exit();
}

if(!empty($_SESSION["csrf_token"])){
    unset($_SESSION["csrf_token"]);
}

?>

<!doctype html>
<html lang="ja">

<?php require_once('rsv_parts/head_info.php');?>

<body class="background_reservation">
    <?= getNav('reservation'); ?>
    <main class="reservation_main">

        <form action="reservation_conf.php" method="post">
            <div class="titles">情報入力欄</div>
            <table class="reservation_table">

                <tr class="reservation_room_name">
                    <th>部屋名</th>
                    <td>
                        <select name="detail_id" id="target">
                            <?php foreach ($pull_down_list as $value) : ?>

                            //POSTの値がある（confから戻ってきた場合）時は入力値を初期値にする
                            <?php if(empty($_POST)):?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?> (<?= $value['capacity'] ?>名様 ¥<?= number_format($value['price']) ?>)</option>
                            <?php else:?>
                                <option value="<?= $value['id'] ?>" <?= ($_POST['detail_id'] == $value['id'])?'selected':''?>><?= $value['name'] ?> (<?= $value['capacity'] ?>名様 ¥<?= number_format($value['price']) ?>)</option>
                            <?php endif;?>

                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <!-- dammy error message -->
                <tr class="room_name_error">
                    <td colspan="2" class = "error"></td>
                </tr>

                <tr class="reservation_check_in">
                    <th>チェックイン</th>
                    <td><input type="date" name="check_in" value="<?=!empty($_POST)?$_POST['check_in'] :''?>"></td>
                </tr>

                <!-- dammy error message -->
                <tr class="room_name_error">
                    <td colspan="2"  class="error"></td>
                </tr>

                <tr class="reservation_check_out">
                    <th>チェックアウト</th>
                    <td><input type="date" name="check_out" value="<?=!empty($_POST)?$_POST['check_out'] :''?>"></td>
                </tr>

                <!-- dammy error message -->
                <tr class="room_name_error">
                <td colspan="2"  class="error"> </td>
                </tr>

                <tr class="reservation_capacity">
                    <th>宿泊人数</th>
                    <td><input type="number" name="capacity" min="1" value="<?=!empty($_POST)?$_POST['capacity'] :'1'?>"></td>
                </tr>

                <!-- dammy error message -->
                <tr class="room_name_error">
                <td colspan="2"  class="error"></td>
                </tr>

                <tr class="reservation_payment">
                    <th>支払い方法</th>
                    <td>
                        <div> <input type="radio" name="peyment" value="1"  <?= ($_POST['peyment'] == 1)?'checked':''?>>現金（現地支払い）</div>
                        <div><input type="radio" name="peyment" value="2  <?= ($_POST['peyment'] == 2)?'checked':''?>">クレジットカード（オンライン決算）</div>
                        <div><input type="radio" name="peyment" value="3"  <?= ($_POST['peyment'] == 3)?'checked':''?>>クレジットカード（現地支払い）</div>
                    </td>
                </tr>
            </table>

        <!-- dammy error message -->
                <div class="date_error"></div>

            <p class="submit_form"><button type="submit" value="予約">予約</button></p>
        </form>
    </main>
</body>

</html>