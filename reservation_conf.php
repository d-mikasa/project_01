<?php
require_once('class/Library.php');
checkLogin();

if(!isset($_POST['csrf_token']) OR $_POST['csrf_token'] != $_SESSION['csrf_token']){
    header('Location: login.php');
    exit();
}

$Reservation = new Reservation();

//Is the capacity value correct?
$Reservation->checkNumeric($_POST['capacity']);

$rsv_info = $Reservation->checkReservation($_POST['detail_id'], $_POST['check_in'], $_POST['check_out']);
if($rsv_info !== true){
    $error['other'] = $rsv_info;
}

$room_info = $Reservation->getReservationRoom($_POST['detail_id']);
if($room_info === false ){
    //値がないことは想定していないが、一応ログインに戻す処理を入れる
    header('Location: login.php');
    exit();
}

$payment = $Reservation->getPayment($_POST['payment']);

////////////////////////////////////////////日付系のバリデーションまとめ////////////////////////////////////////////
//チェックインのバリデーション
if(empty($_POST['check_in'])){
    $error['check_in'] = 'チェックイン日が空欄です';
}else{
    //Is the check_in value correct?
    $Reservation->validateDateFormat($_POST['check_in']);

    if(strtotime($_POST['check_in']) < strtotime('-1 day')){
        $error['check_in'] = 'チェックイン日が過去を指定しています';
    }
}

//チェックアウトのバリデーション
if(empty($_POST['check_out'])){
    $error['check_out'] = 'チェックアウト日が空欄です';
}else{
    //Is the check_out value correct?
    $Reservation->validateDateFormat($_POST['check_out']);

    if(strtotime($_POST['check_out']) < strtotime('-1 day')){
        $error['check_out'] = 'チェックアウト日が過去を指定しています';
    }
}

//日付の整合性に関するバリデーション
if(empty($error['check_in']) and empty($error['check_out'])){
    //チェックイン・チェックアウトが入力されていた場合

    if(strtotime($_POST['check_in']) > strtotime($_POST['check_out'])){
        $error['other'] = 'チェックインがチェックアウトより後に指定されています';
    }else if(strtotime($_POST['check_in']) == strtotime($_POST['check_out'])){
        $error['other'] = 'チェックインとチェックアウトが同日に指定されています';
    }else if(strtotime($_POST['check_in']) >= (strtotime('+90 day'))){
        $error['other'] = '３ヶ月以内のご予約のみ承っております';
    }
}

//宿泊人数のバリデーション
if(empty($_POST['capacity'])){
    $error['capacity'] = '宿泊人数が空欄です';
}else if($_POST['capacity'] != $room_info['capacity'] && $_POST['capacity'] > $room_info['capacity']){
    //部屋詳細から該当の宿泊人数のプランがあるかを検索する
    $error['capacity'] = '宿泊人数が上限を超えています';
}

if(!empty($error)){
    require_once('reservation.php');
    exit();
}

$check_out = new DateTime($_POST['check_out']);
$check_in  = new DateTime($_POST['check_in']);
$diff     = $check_in->diff($check_out);
$cnt_stay = $diff->days;
?>

<?php require_once('rsv_parts/head_info.php');?>
<body class="background_conf">
    <?php require_once('rsv_parts/status_nav.php')?>
    <!--
    エラーが無く、送信することが可能な画面
    -->
    <main class="reservation_main">
        <div class="hogehogester">
            <div class="titles">ご予約内容確認</div>
            <table class="conf_check_table">
                <tr>
                    <th>部屋名</th>
                    <td><?=h($room_info['name'])?></td>
                </tr>
                <tr>
                    <th>チェックイン</th>
                    <td><?=h($_POST['check_in'])?></td>
                </tr>
                <tr>
                    <th>チェックアウト</th>
                    <td><?=h($_POST['check_out'])?></td>
                </tr>
                <tr>
                    <th>宿泊人数</th>
                    <td><?=h($_POST['capacity'])?></td>
                </tr>
                <tr>
                    <th>支払い方法</th>
                    <td><?=h($payment['name'])?></td>
                </tr>
                <tr>
                    <th>合計金額</th>
                    <td>¥<?=h(number_format($room_info['price'] * $cnt_stay))?></td>
                </tr>
            </table>
            <div class="final_check">以上の内容でお間違い無いでしょうか？</div>
            <form action="reservation_done.php" method="post">
                <input type="hidden" name="csrf_token" value="<?=$_POST['csrf_token']?>"><!-- token -->
                <!--実際に送信する情報群-->
                <input type="hidden" name="detail_id" value="<?=$_POST['detail_id']?>">
                <input type="hidden" name="check_in" value="<?=$_POST['check_in']?>">
                <input type="hidden" name="check_out" value="<?=$_POST['check_out']?>">
                <input type="hidden" name="capacity" value="<?=$_POST['capacity']?>">
                <input type="hidden" name="payment" value="<?=$_POST['payment']?>">
                <p class="submit_form">
                    <button type="submit">予約する</button>
                    <button type="submit" formaction="reservation.php">キャンセル</button>
                </p>
            </form>
        </div>
    </main>
</body>
</html>