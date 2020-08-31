<?php
require_once('../class/Library.php');
const IMGS_PATH = '../img/';

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

if (!empty($_POST['add'])) {
    $view = $_COOKIE['count'] + 1;
}

if (!empty($_POST['del'])) {
    $id = $_POST['del'];
    $view = $_COOKIE['count'] - 1;
}

if (!empty($_FILES)) {

    // 権限変更
    exec('sudo chmod 0777 ' . IMGS_PATH);

    if ($_FILES['userfile']['error'] == UPLOAD_ERR_OK) {
        $name = $_FILES['userfile']['name'];
        $name = mb_convert_encoding($name, 'cp932', 'utf8');
        $temp = $_FILES['userfile']['tmp_name'];
        $result = move_uploaded_file($temp, IMGS_PATH . $name);
        if ($result == true) {
            $message = 'ファイルをアップロードしました';
            $pdo = new ImageUpdata;
            $pdo->image_update($name, $_SESSION['data_id']);
        } else {
            $message = 'ファイルの移動に失敗しました';
        }
    } elseif ($_FILES['userfile']['error'] == UPLOAD_ERR_NO_FILE) {
        $message = 'ファイルがアップロードされませんでした';
    } else {
        $message = 'ファイルのアップロードに失敗しました';
    }
    // 元の状態に戻す
    exec('sudo chmod 0755 ' . IMGS_PATH);
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

<!-- ヘッダー部分読み込み -->
<?php include('parts/top.parts.php'); ?>
<main>

    <form action="room_conf.php" method="post">

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
                    <td><input type="text" name="plan[<?= $i ?>][capacity]" value="<?php if (!empty($edit_detail[$i - 1]['capacity'])) echo $edit_detail[$i - 1]['capacity'] ?>"></td>
                    <td rowspan="3"><?php if ($view != 1) : ?> <button type="submit" name="del" value="<?= $id ?>" formaction="room_edit.php">削除</button><?php endif; ?></td>
                </tr>
                <tr>
                    <th>料金</th>
                    <td><input type="text" name="plan[<?= $i ?>][price]" value="<?php if (!empty($edit_detail[$i - 1]['capacity'])) echo $edit_detail[$i - 1]['price'] ?>"></td>
                </tr>
                <tr>
                    <th>コメント</th>
                    <td colspan=""><textarea name="plan[<?= $i ?>][remarks]" cols="30" rows="10"> <?php if (!empty($edit_detail[$i - 1]['remarks'])) echo $edit_detail[$i - 1]['remarks'] ?> </textarea></td>
                </tr>
            <?php endfor; ?>
            <?php if ($max > $view) : ?>
                <tr>
                    <th colspan="4"><button type="submit" name="add" value="<?= $id ?>" formaction="room_edit.php">プランを追加する</button></th>
                </tr>
            <?php endif; ?>
        </table>

        <p><input type="submit" value="更新する"></p>
    </form>

    <?php if ($_SESSION['mode'] === 'edit') : ?>
        <p>画像の編集</p>
        <form action="room_edit.php" method="post" enctype="multipart/form-data">
            <table>
                <tr>
                    <th>画像</th>
                    <td><input type="file" name="userfile"></td>
                </tr>
            </table>
            <p><input type="submit" value="画像を更新" onclick="return btn_check()"></p>
        </form>
    <?php endif; ?>

</main>
<!-- フッター部分読み込み -->
<?php include('parts/footer.parts.php'); ?>
<script>
    function btn_check(btn, value = null) {
        var res = confirm("画像をアップロードしますか？");
        if (res == false) {
            // 「いいえ」ならフォーム送信をやめる
            console.log('delete_none');
            return false;
        } else {
            console.log('delete_ok');
        }
    }
</script>

</html>