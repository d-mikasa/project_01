<?php
require_once('class/Library.php');
$Reservation = new Reservation();

$pull_down_list = $Reservation->getPullDownList();

checkLogin();

if (!empty($_SESSION["csrf_token"])) {
    unset($_SESSION["csrf_token"]);
}

?>
<?php require_once('rsv_parts/head_info.php');?>
<body class="background_reservation">
    <?php require_once('rsv_parts/status_nav.php')?>
        <main class="reservation_main">
            <!--情報を入力する場所-->
            <form action="reservation_conf.php" method="post">
                <div class="titles">情報入力欄</div>
                <table class="reservation_table">
                    <!--部屋名を入力する場所-->
                    <tr class="reservation_room_name">
                        <th>部屋名</th>
                        <td>
                            <select name="detail_id" id="target">
                                <?php foreach ($pull_down_list as $value) :?>
                                    <option value="<?=$value['id']?>" <?=(!empty($_POST['detail_id']) && ($_POST['detail_id']) == $value['id']) ? 'selected' : '';?>>
                                        <?=$value['name']?> (<?=$value['capacity']?>名様 ¥<?=number_format($value['price'])?>)
                                    </option>
                                <?php endforeach;?>
                            </select>
                        </td>
                    </tr>
                    <!--dummy-->
                    <tr class="room_name_error">
                        <td colspan="2" class="error"></td>
                    </tr>
                    <!--チェックインを入力する場所-->
                    <tr class="reservation_check_in">
                        <th>チェックイン</th>
                        <td>
                            <input type="date" name="check_in" value="<?=!empty($_POST['check_in']) ? $_POST['check_in'] : ''?>">
                        </td>
                    </tr>
                    <!--チェックインエラー表示-->
                    <!-- dammy error message -->
                    <tr class="room_name_error">
                        <td colspan="2" class="error"><?=!empty($error['check_in']) ? $error['check_in'] : ''?></td>
                    </tr>
                    <tr>
                        <th>チェックアウト </th>
                        <td>
                            <input type="date" name="check_out" value="<?=!empty($_POST['check_out']) ? $_POST['check_out'] : ''?>">
                        </td>
                    </tr>
                    <!-- チェックアウトのエラー -->
                    <tr class="room_name_error">
                        <td colspan="2" class="error"><?=!empty($error['check_out']) ? $error['check_out'] : ''?></td>
                    </tr>
                    <tr>
                        <th>宿泊人数 </th>
                        <td>
                            <input type="number" name="capacity" min="1" value="<?=!empty($_POST['capacity']) ? $_POST['capacity'] : '1'?>">
                        </td>
                    </tr>
                    <tr class="room_name_error">
                        <td colspan="2" class="error"> <?=!empty($error['capacity']) ? $error['capacity'] : ''?> </td>
                    </tr>
                    <tr>
                        <th>支払い方法 <br><span class="error"><?=!empty($error['payment']) ? $error['peyment'] : ''?></span></th>
                        <td>
                            <div> <input type="radio" name="peyment" value="1" <?=(empty($_POST['peyment']) OR $_POST['peyment'] == '1') ? 'checked' : '';?>>現金（現地支払い）</div>
                            <div><input type="radio" name="peyment" value="2" <?=(!empty($_POST['peyment']) AND $_POST['peyment'] == '2') ? 'checked' : '';?>>クレジットカード（オンライン決算）</div>
                            <div><input type="radio" name="peyment" value="3" <?=(!empty($_POST['peyment']) AND $_POST['peyment'] == '3') ? 'checked' : '';?>>クレジットカード（現地支払い）</div>
                        </td>
                    </tr>
                </table>
                <div class="date_error"><?=!empty($error['ather']) ? $error['ather'] : ''?></div>
                <p class="submit_form"><button type="submit">予約</p>
            </form>
        </main>
    </body>
</html>