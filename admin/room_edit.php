<?php
require_once('../class/Library.php');

//リダイレクト処理
if (empty($_SESSION['auth'])) {
    header('Location: login.php');
    exit();
}

/*
編集ボタンからこのページに遷移してきた場合
*/
if ($_GET['mode'] == 'edit') {
    //配列に値をいれる
    $Room = new Room();
    $room_info = $Room->getgetRoom($_GET['id']);

    $tmp = $_POST + $room_info;
    $name = $tmp['set_data']['name'];
    $detail = $tmp['set_data']['detail'];
    //配列の個数を表示領域に設定
    $view = count($detail);
} else {
    //新規作成が押された場合の初期表示数は１
    $view = 1;
}

//最大表示領域を超えていた場合、表示領域を上書き
//データベースの値がMAXVIEW以上あった場合に、無理やり３つに変更する
$Room = new Room();
if ($view > $Room::MAX_VIEW) {
    $view = $Room::MAX_VIEW;
}

//表示領域がなかった場合、１を代入
if ($view == 0) {
    $view = 1;
}

///////////////////*画像ファイルを処理する*/////////////////////////
if (!empty($_FILES)) {
    $error = $Room->updateRoomImg($_GET['id']);
} else {
    $error = '';
}
?>

<!-- ヘッダー部分読み込み -->
<?php require_once('parts/top.parts.php'); ?>
<main>
    <form action="room_conf.php?mode=<?= $_GET['mode'] ?>&id=<?= $_GET['id'] ?>" method="post">

        <!-- 部屋名を取得・表示する -->
        <table class="newcreate">
            <tr>
                <td>部屋名</td>
                <td><input type="text" name="set_data[name][0]" value="<?= !empty($name) ? $name : '' ?>"></td>
            </tr>
        </table>

        <!--テーブルの表示-->
        <table class="roomedit_table" id='table'>
            <tr class="roomedit_title">
                <td>部屋[番号]</td>
                <td>人数</td>
                <td>料金</td>
                <td>コメント</td>
            </tr>

            <?php for ($i = 1; $i <= $view; $i++) : ?>
                <tr>
                    <td>部屋[<?= $i ?>]</td>
                    <td><input type="text" name="set_data[detail][<?= $i - 1 ?>][capacity]" value="<?= !empty($detail[$i - 1]['capacity']) ? $detail[$i - 1]['capacity'] : '' ?>">名様</td>
                    <td><input type="text" name="set_data[detail][<?= $i - 1 ?>][price]" value="<?= !empty($detail[$i - 1]['capacity']) ? $detail[$i - 1]['price'] : '' ?>">円</td>
                    <td><textarea name="set_data[detail][<?= $i - 1 ?>][remarks]" cols="30" rows="10"><?= !empty($detail[$i - 1]['remarks']) ? $detail[$i - 1]['remarks'] : '' ?></textarea></td>
                </tr>
            <?php endfor; ?>

        </table>
        <div class="changebutton_group">
            <div id="add_plan">
                <button type="button" onclick="add_plan('table')">プランを追加する</button>
            </div>

            <div id="del_plan">
                <button type="button" onclick="del_plan('table')">プランを削除する</button>
            </div>
        </div>
        <p class="doneButton"><input type="submit" value="更新する"></p>
    </form>

    <div class="borderLine"></div>

    <!--編集を押した時のみ画像編集を表示する-->
    <?php if ($_GET['mode'] === 'edit') : ?>

        <form action="room_edit.php?mode=<?= $_GET['mode'] ?>&id=<?= $_GET['id'] ?>" method="post" enctype="multipart/form-data">
            <div class="img_up">
                <h2>画像の編集</h2>
                <div><?= $error ?></div>
                <input type="file" name="room_img" id="sample1">
                </table>
                <p id="doneImage"><input type="submit" value="画像を更新" onclick="return btn_check()"></p>
            </div>
        </form>

    <?php endif; ?>

</main>
<!-- フッター部分読み込み -->
<?php require_once('parts/footer.parts.php'); ?>

<script>
    ////////////////////////////////*画像をアップロードするかの確認*//////////////////////////////////
    function btn_check(btn, value = null) {
        var res = confirm("画像をアップロードしますか？");
        if (res == false) {
            // 「いいえ」ならフォーム送信をやめる
            return false;
        }
    }
</script>
<script>
    ////////////////////////////////*行を追加する処理*//////////////////////////////////
    const VIEW = "<?= $Room::MAX_VIEW ?>";

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
        var row_len = table.rows.length - 1;

        console.log('row_count =' + row_len);

        // パーツのHTML
        var room = '<th>部屋[' + row_len + ']</th>';
        var capacity = '<td><input type="text" name="set_data[detail][<?= $i - 1 ?>][capacity]">名様</td>';
        var price = ' <td><input type="text" name="set_data[detail][<?= $i - 1 ?>][price]">円</td>';
        var remarks = '<td><textarea name="set_data[detail][<?= $i - 1 ?>][remarks]" cols="30" rows="10"></textarea></td>';

        // セルの内容入力
        cell1.innerHTML = room;
        cell2.innerHTML = capacity;
        cell3.innerHTML = price;
        cell4.innerHTML = remarks;

        //ボタンの表示非表示を切り替える処理
        if (row_len == 1) {
            document.getElementById('del_plan').style.visibility = "hidden";
            document.getElementById('add_plan').style.visibility = "visible";
        } else if (row_len < VIEW) {
            document.getElementById('del_plan').style.visibility = "visible";
            document.getElementById('add_plan').style.visibility = "visible";
        } else {
            document.getElementById('del_plan').style.visibility = "visible";
            document.getElementById('add_plan').style.visibility = "hidden";
        }

    }


    ////////////////////////////////*行を削除する処理*//////////////////////////////////
    function del_plan(obj) {

        var table = document.getElementById("table");
        // 0で先頭を削除。インデックスを指定する。
        var rows = table.deleteRow(-1);
        var row_len = table.rows.length - 1;

        console.log('row_count =' + row_len);

        //ボタンの表示非表示を切り替える処理
        if (row_len == 1) {
            document.getElementById('del_plan').style.visibility = "hidden";
            document.getElementById('add_plan').style.visibility = "visible";
        } else if (row_len < VIEW) {
            document.getElementById('del_plan').style.visibility = "visible";
            document.getElementById('add_plan').style.visibility = "visible";
        } else {
            document.getElementById('del_plan').style.visibility = "visible";
            document.getElementById('add_plan').style.visibility = "hidden";
        }
    }

    ////////////////////////////////*ページに初めて飛んできたときのボタンの有無*//////////////////////////////////
    window.onload = function() {
        var table = document.getElementById("table");
        var row_len = table.rows.length - 1;
        console.log('first_contact');
        console.log('row_count =' + row_len);

        //ボタンの表示非表示を切り替える処理
        if (row_len == 1) {
            console.log('MOST_MIN');
            document.getElementById('del_plan').style.visibility = "hidden";
        }
        if (row_len == VIEW) {
            console.log('MOST_MAX');
            document.getElementById('add_plan').style.visibility = "hidden";
        }
    }
</script>