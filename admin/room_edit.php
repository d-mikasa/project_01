<?php
require_once('class/Library.php');

$view = 1;

//最大表示領域
$max = 3;

if ($_SESSION['mode'] == 'edit') {
    //配列に値をいれる
    $a = new EditList();
    $edit_detail = $a->Edit_detail($_SESSION['data_id']);


    //配列の個数を表示領域に設定
    if ($view < count($edit_detail)) {
        $view = count($edit_detail);
    }
}

//POSTで受け取った際にはidの値と$viewの値を上書き
if (!empty($_POST)) {
    if (!empty($_POST['add'])) {
        $view = $_COOKIE['count'] + 1;
    }

    if (!empty($_POST['del'])) {
        $id = $_POST['del'];
        $view = $_COOKIE['count'] - 1;
    }
}

//最大表示領域を超えていた場合、表示領域を上書き
if ($view > $max) {
    $view = $max;
}

//表示領域がなかった場合、１を代入
if ($view == 0) {
    $view = 1;
}

//現在の表示領域をCookieに保存する
setcookie('count', $view, time() + 60 * 60 * 24 * 7);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin_style.css">
    <title>内容編集</title>
</head>


<body>
    <!-- ヘッダー部分読み込み -->
    <?php include('parts/nav.parts.php'); ?>
    <main>

        <form action="room_conf.php" method="post" enctype="multipart/form-data">

            <!--新規作成モードなら新規部屋名を表示 -->
            <?php if ($_SESSION['mode'] == 'create') : ?>
                <table>
                    <tr>
                        <th>新規部屋名</th>
                        <td><input type="text" name="plan[0][room_name]"></td>
                    </tr>
                </table>
            <?php endif; ?>

            <table class="roomedit_table">
                <?php for ($i = 1; $i <= $view; $i++) : ?>
                    <tr>
                        <th rowspan="3">プラン[<?= $i  ?>]</th>
                        <th>人数</th>
                        <td><input type="text" name="plan[<?= $i ?>][capacity]" value="<?php if (!empty($edit_detail[$i -1]['capacity'])) echo $edit_detail[$i-1]['capacity'] ?>"></td>
                        <td rowspan="3"><?php if ($view != 1) : ?> <button type="submit" name="del" value="<?= $id ?>" formaction="room_edit.php">削除</button><?php endif; ?></td>
                    </tr>
                    <tr>
                        <th>料金</th>
                        <td><input type="text" name="plan[<?= $i ?>][price]" value="<?php if (!empty($edit_detail[$i-1]['capacity'])) echo $edit_detail[$i-1]['price'] ?>"></td>
                    </tr>
                    <tr>
                        <th>コメント</th>
                        <td colspan=""><textarea name="plan[<?= $i ?>][remarks]" cols="30" rows="10"> <?php if (!empty($edit_detail[$i-1]['remarks'])) echo $edit_detail[$i-1]['remarks'] ?> </textarea></td>
                    </tr>
                <?php endfor; ?>
                <?php if ($max > $view) : ?>
                    <tr>
                        <th colspan="4"><button type="submit" name="add" value="<?= $id ?>" formaction="room_edit.php">プランを追加する</button></th>
                    </tr>
                <?php endif; ?>
            </table>

            <?php if ($_SESSION['mode'] === 'edit') : ?>
                <p>画像の編集</p>
                <table>
                    <tr>
                        <th>画像</th>
                        <td><input type="file" name="userfile"></td>
                    </tr>
                </table>
            <?php endif; ?>

            <p><input type="submit" value="更新する"></p>
        </form>


    </main>
    <!-- フッター部分読み込み -->
    <?php include('parts/footer.parts.php'); ?>
    <script>
    </script>
</body>

</html>