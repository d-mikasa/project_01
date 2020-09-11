<?php
require_once('class/Library.php');
$error = [];

$pdo = new RoomShow();
//プルダウンの内容を取得する
$room = $pdo->room();

//選択した部屋の内容を取得する
$select = $pdo->room_select($_POST['room_name']);

//選択した部屋が予約されているかを取得する
$reservation = $pdo->room_reservation($_POST['room_name']);

echo 'POSTの値' . '<br>';
print_r('<pre>');
print_r($_POST);
print_r('</pre>');

// echo 'Selectの値' . '<br>';
// print_r('<pre>');
// print_r($select);
// print_r('</pre>');

echo 'reservationの値' . '<br>';
print_r('<pre>');
print_r($reservation);
print_r('</pre>');



/*
バリデーション群
*/
//部屋のバリデーション
// if (empty($_POST['room_name'])) { //部屋名が空欄になることはない？
//     $error['room'] = '部屋名が空欄です';
// }

////////////////////////////////////////////日付系のバリデーションまとめ////////////////////////////////////////////
//チェックインのバリデーション
if (empty($_POST['check_in'])) {
    $error['check_in'] = 'チェックイン日時が空欄です';
} else {
    if (strtotime($_POST['check_in']) < time()) {
        $error['check_in'] = 'チェックイン日時が過去を指定しています';
    }
}

//チェックアウトのバリデーション
if (empty($_POST['check_out'])) {
    $error['check_out'] = 'チェックアウト日時が空欄です';
} else {
    if (strtotime($_POST['check_out']) < time()) {
        $error['check_out'] = 'チェックアウト日時が過去を指定しています';
    }
}

//日付の整合性に関するバリデーション
if (empty($error['check_in']) and empty($error['check_out'])) {
    //チェックイン・チェックアウトが入力されていた場合

    if (strtotime($_POST['check_in']) > strtotime($_POST['check_out'])) {
        $error['check_in'] = 'チェックイン日時がチェックアウト日時より後に指定されています';
    }

    if (strtotime($_POST['check_in']) == strtotime($_POST['check_out'])) {
        $error['check_out'] = 'チェックイン日時とチェックアウト日時が同日に指定されています';
    }

    //予約が日付以内の物であるかどうかの確認
    if (strtotime($_POST['check_in']) >= (strtotime("+90 day")) or strtotime($_POST['check_out']) >= (strtotime("+90 day"))) {
        $error['check_out'] = '３ヶ月以内のご予約のみ承っております';
    }

    // 期間内の日付をすべて取得
    for ($i = date('Ymd', strtotime($_POST['check_in'])); $i <= date('Ymd', strtotime($_POST['check_out'])); $i++) {
        $year = substr($i, 0, 4);
        $month = substr($i, 4, 2);
        $day = substr($i, 6, 2);

        if (checkdate($month, $day, $year)) {
            $days[] = date('Y-m-d', strtotime($i));
        }
    }

		//予約情報がなければ「予約済みかチェックする」処理をしない
		if($reservation != 'not reservation room'){
		$ch_days = explode(',', $reservation['date']);
    //日付が予約済みかチェックする
    for ($i = 0; $i < count($days); $i++) {
        for ($k = 0; $k < count($ch_days); $k++) {
						if (date('d-m-Y',strtotime($ch_days[$k])) == date('d-m-Y',strtotime($days[$i]))) {
                $error['ather'] = 'すでに予約済みの日程が含まれます';
            }
        }
		}
	}

    // echo 'daysの値' . '<br>';
    // print_r('<pre>');
    // print_r($days);
		// print_r('</pre>');

		// echo 'DBの値' . '<br>';
    // print_r('<pre>');
    // print_r($ch_days);
    // print_r('</pre>');
}

//宿泊日数系のバリデーションチェック

//宿泊人数のバリデーション
if (empty($_POST['capacity'])) {
    $error['capacity'] = '宿泊人数が空欄です';
} else {
    //部屋詳細から該当の宿泊人数のプランがあるかを検索する
    if ($_POST['capacity'] != $select['capacity']) {
        if ($_POST['capacity'] > $select['capacity']) {
            $error['capacity'] = '宿泊人数が多いです。';
        }
    }
}


if (empty($error)) {
    echo '入力エラー無し！！！！！';
}

?>
<!doctype html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>CICACU</title>
	<meta name="description" content="CICACU(シカク)">
	<meta name="keywords" content="CICACU,cafe饗茶庵,鹿沼,ゲストハウス,民宿">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<!--スマホ用に見れるように-->
	<meta name="robots" content="noindex,nofollow,noarchive">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap-reboot.min.css">
	<link rel="stylesheet" href="./css/reservation_style.css">
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>
<body class="reservation">
	<header>
		<h1>CICACU</h1>
		<h2>予約ページ</h2>
	</header>
    <?php if (empty($error)) : ?> <!--エラーが無く、送信することが可能な画面-->
    <contaner class="step_group">
		<div class="step_conf">入力</div>
		<p>→</p>
		<div class="step_input">確認</div>
		<p>→</p>
		<div class="step_done">完了</div>
	</contaner>
	<main class="reservation_main">
		<form action="reservation_done.php" method="post">
        <input type="hidden" name = "room_name" value = "<?=$_POST['room_name']?>">
        <input type="hidden" name = "check_in" value = "<?=$_POST['check_in']?>">
        <input type="hidden" name = "check_out" value = "<?=$_POST['check_out']?>">
        <input type="hidden" name = "capacity" value = "<?=$_POST['capacity']?>">
        <input type="hidden" name = "peyment" value = "<?=$_POST['peyment']?>">
        <input type="hidden" name = "price" value = "<?=$reservation['price']?>">
        <input type="hidden" name = "room_name" value = "<?=$select['name']?>">

			<div class="titles">情報入力欄</div>
			<table>
				<tr>
					<th>部屋名</th>
					<td> <?= $_POST['room_name'] ?> </select>
					</td>
				</tr>
				<tr>
					<th>チェックイン</th>
					<td> <?= $_POST['check_in'] ?> </td>
				</tr>
				<tr>
					<th>チェックアウト</th>
					<td> <?= $_POST['check_out'] ?> </td>
				</tr>
				<tr>
					<th>宿泊人数</th>
					<td> <?= $_POST['capacity'] ?> </td>
				</tr>
				<tr>
					<th>支払い方法</th>
					<td> <?php
                            switch ($_POST['peyment']) {
                                case 'card_after':
                                    echo 'クレジットカード（オンライン決算）';
                                    break;
                                case 'card_now':
                                    echo 'クレジットカード（現地支払い）';
                                    break;
                                default:
                                    echo '現金（現地支払い）';
                                    break;
                            }

                            ?> </td>
				</tr>
			</table>
			<p class="submit_form">
                    以上の内容でお間違い無いでしょうか？
                    <input type="submit" value="確認">
                </p>
		</form>
	</main>
    <?php else : ?><!--エラーがあって、もう一度フォームを送信する-->
     <contaner class="step_group">
		<div class="step_input">入力</div>
		<p>→</p>
		<div class="step_conf">確認</div>
		<p>→</p>
		<div class="step_done">完了</div>
	</contaner>
	<main class="reservation_main">
		<form action="reservation_conf.php" method="post">
			<div class="titles">情報入力欄</div>
			<table>
				<tr>
					<th>部屋名<br><span class="error"><?php if (!empty($error['room'])) echo $error['room'] ?></span></th>
					<td>
						<select name="room_name" id="target"> <?php foreach ($room as $value) : ?> <option value="<?= $value['id'] ?>" <?php if (($_POST['room_name']) == $value['id']) echo 'selected' ?>><?= $value['name'] ?> (<?= $value['capacity'] ?>名様 ¥<?= number_format($value['price']) ?>)</option> <?php endforeach; ?> </select>
					</td>
				</tr>
				<tr>
					<th>チェックイン <br><span class="error"><?php if (!empty($error['check_in'])) echo $error['check_in'] ?></span></th>
					<td>
						<input type="date" name="check_in" value="<?php if (!empty($_POST['check_in'])) echo $_POST['check_in'] ?>">
					</td>
				</tr>
				<tr>
					<th>チェックアウト <br><span class="error"><?php if (!empty($error['check_out'])) echo $error['check_out'] ?></span></th>
					<td>
						<input type="date" name="check_out" value="<?php if (!empty($_POST['check_out'])) echo $_POST['check_out'] ?>">
					</td>
				</tr>
				<tr>
					<th>宿泊人数 <br><span class="error"><?php if (!empty($error['capacity'])) echo $error['capacity'] ?></span></th>
					<td>
						<input type="number" name="capacity" min="1" value="<?php if (!empty($_POST['capacity'])) echo $_POST['capacity'] ?>">
					</td>
				</tr>
				<tr>
					<th>支払い方法 <br><span class="error"><?php if (!empty($error['payment'])) echo $error['peyment'] ?></span></th>
					<td>
						<div> <input type="radio" name="peyment" value="cash_after" <?php if ($_POST['peyment'] == 'cash_after') echo 'checked' ?>>現金（現地支払い）</div>
						<div><input type="radio" name="peyment" value="card_now" <?php if ($_POST['peyment'] == 'card_now') echo 'checked' ?>>クレジットカード（オンライン決算）</div>
						<div><input type="radio" name="peyment" value="card_after" <?php if ($_POST['peyment'] == 'card_after') echo 'checked' ?>>クレジットカード（現地支払い）</div>
					</td>
				</tr>
			</table>
			<span class="error"><?php if (!empty($error['ather'])) echo $error['ather'] ?></span>
			<p class="submit_form"><input type="submit" value="予約"></p>
		</form>
	</main> <?php endif; ?>
</body>
</html>