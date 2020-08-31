<?php
require_once('../class/Library.php');

//画像保存先
const IMGS_PATH = '../img/';

//最大表示領域
const MAX_VIEW = 3;

if ($_SESSION['mode'] == 'edit') {
    //配列に値をいれる
    $a = new EditList();
    $edit_detail = $a->Edit_detail($_SESSION['data_id']);

    //配列の個数を表示領域に設定
    $view = count($edit_detail);
} else {
    $view = 1;
}

//最大表示領域を超えていた場合、表示領域を上書き
if ($view > MAX_VIEW) {
    $view = MAX_VIEW;
}

//表示領域がなかった場合、１を代入
if ($view == 0) {
    $view = 1;
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

        <table class="roomedit_table" id='table'>
            <?php for ($i = 1; $i <= $view; $i++) : ?>
                <tr>
                    <td>部屋[<?= $i ?>]</td>
                    <td>人数
                        <input type="text" name="plan[<?= $i ?>][capacity]" value="<?php if (!empty($edit_detail[$i - 1]['capacity'])) echo $edit_detail[$i - 1]['capacity'] ?>">
                    </td>

                    <td>料金
                        <input type="text" name="plan[<?= $i ?>][price]" value="<?php if (!empty($edit_detail[$i - 1]['capacity'])) echo $edit_detail[$i - 1]['price'] ?>">
                    </td>

                    <td>コメント
                        <textarea name="plan[<?= $i ?>][remarks]" cols="30" rows="10"> <?php if (!empty($edit_detail[$i - 1]['remarks'])) echo $edit_detail[$i - 1]['remarks'] ?> </textarea>
                    </td>
                </tr>
                <div id = "hogehoge">
                </div>
            <?php endfor; ?>
        </table>
        <button type="button" onclick="add_plan('table')">プランを追加する</button>
        <button type="button" onclick="deleteRow('table')">プランを削除する</button>

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

<script>
    /**
     * 列追加
     */
    function add_plan(id) {
        // テーブル取得
        var table = document.getElementById(id);
        // 行を行末に追加
        var row = table.insertRow(-1);

        // セルの挿入
        var cell1 = row.insertCell(-1);
        var cell2 = row.insertCell(-1);
        var cell3 = row.insertCell(-1);
        var cell4 = row.insertCell(-1);


        // 行数取得
        var row_len = table.rows.length;

        // パーツのHTML
        var room = '<th>部屋[' + row_len + ']</th>';
        var capacity = '<td>人数<input type="text" name="plan[' + row_len + '][capacity]"></td>';
        var price = ' <td>料金<input type="text" name="plan[' + row_len + '][price]"></td>';
        var remarks = '<td>コメント<textarea name="plan[' + row_len + '][remarks]" cols="30" rows="10"></textarea></td>';

        // セルの内容入力
        cell1.innerHTML = room;
        cell2.innerHTML = capacity;
        cell3.innerHTML = price;
        cell4.innerHTML = remarks;
        // cell5.innerHTML = button;

        if(row_len < 3 ){
        var row = table.insertRow(-1);
        var add = row.insertCell(-1);
        var addPlan = '<td><button type="button" onclick="add_plan("table")">プランを追加する</button></td>';
        add.innerHTML = addPlan;

    }
    if(row_len != 1){
    var row = table.insertRow(-1);
        var del = row.insertCell(-1);
        var delPlan = '<td><input type="button" value="行削除" onclick="deleteRow("table")"></td>';
        add.innerHTML = delPlan;

    }

    }


    /**
     * 行削除
     */
    function deleteRow(obj) {
        var table = document.getElementById("table");
        // 0で先頭を削除。インデックスを指定する。
        var rows = table.deleteRow(-1);
    }




</script>

</html>