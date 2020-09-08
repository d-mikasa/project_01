<?php
require_once('../class/Library.php');

//画像保存先
const IMGS_PATH = '../img/';

//最大表示領域
const MAX_VIEW = 3;

//リダイレクト処理
if (empty($_SESSION['auth'])) {
    header('Location: login.php');
}
if(empty($_SESSION['mode'])){
    header('Location: room_list.php');
}


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

///////////////////*画像ファイルを処理する*/////////////////////////
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
<?php require_once('parts/top.parts.php'); ?>
<main>

    <form action="room_conf.php" method="post">

        <!--新規作成モードなら新規部屋名を表示 -->
        <?php if ($_SESSION['mode'] == 'create') : ?>
            <table class = "newcreate">
            <tr>
            <th>新規部屋名</th>
            </tr>
                <tr>
                    <td><input type="text" name="plan[0][room_name]"></td>
                </tr>
            </table>
        <?php endif; ?>

        <!--テーブルの表示-->
        <table class="roomedit_table" id='table'>
            <tr class="roomedit_title">
                <td>
                    部屋[番号]
                </td>
                <td>
                    人数
                </td>
                <td>
                    料金
                </td>
                <td>
                    コメント
                </td>
            </tr>
            <?php for ($i = 1; $i <= $view; $i++) : ?>
                <tr>
                    <td>部屋[<?= $i ?>]</td>
                    <td>
                        <input type="text" name="plan[<?= $i ?>][capacity]" value="<?php if (!empty($edit_detail[$i - 1]['capacity'])) echo $edit_detail[$i - 1]['capacity'] ?>">名様
                    </td>

                    <td>
                        <input type="text" name="plan[<?= $i ?>][price]" value="<?php if (!empty($edit_detail[$i - 1]['capacity'])) echo $edit_detail[$i - 1]['price'] ?>">円
                    </td>

                    <td>
                        <textarea name="plan[<?= $i ?>][remarks]" cols="30" rows="10"> <?php if (!empty($edit_detail[$i - 1]['remarks'])) echo $edit_detail[$i - 1]['remarks'] ?> </textarea>
                    </td>
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
    <?php if ($_SESSION['mode'] === 'edit') : ?>

        <form action="room_edit.php" method="post" enctype="multipart/form-data">
            <div class="img_up">
                <h2>画像の編集</h2>
                <input type="file" name="userfile" id="sample1">
                </tr>
                </table>
                <p id = "doneImage">
                    <input type="submit" value="画像を更新" onclick="return btn_check()">
                </p>
            </div>
        </form>
    <?php endif; ?>

</main>

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
    const VIEW = "<?= MAX_VIEW ?>";

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

        // パーツのHTML
        var room = '<th>部屋[' + row_len + ']</th>';
        var capacity = '<td><input type="text" name="plan[' + row_len + '][capacity]">名様</td>';
        var price = ' <td><input type="text" name="plan[' + row_len + '][price]">円</td>';
        var remarks = '<td><textarea name="plan[' + row_len + '][remarks]" cols="30" rows="10"></textarea></td>';

        // セルの内容入力
        cell1.innerHTML = room;
        cell2.innerHTML = capacity;
        cell3.innerHTML = price;
        cell4.innerHTML = remarks;

        //ボタンの表示非表示を切り替える処理
        if (row_len >= VIEW) {
            document.getElementById('add_plan').style.visibility = "hidden";
        } else {
            document.getElementById('add_plan').style.visibility = "visible";
        }

        if (row_len == 1) {
            document.getElementById('del_plan').style.visibility = "hidden";
        } else {
            document.getElementById('del_plan').style.visibility = "visible";
        }
    }


    ////////////////////////////////*行を削除する処理*//////////////////////////////////
    function del_plan(obj) {
        var table = document.getElementById("table");
        // 0で先頭を削除。インデックスを指定する。
        var rows = table.deleteRow(-1);
        var row_len = table.rows.length - 1;

        //ボタンの表示非表示を切り替える処理
        if (row_len >= VIEW) {
            document.getElementById('add_plan').style.visibility = "hidden";
        } else {
            document.getElementById('add_plan').style.visibility = "visible";
        }

        if (row_len == 1) {
            document.getElementById('del_plan').style.visibility = "hidden";
        } else {
            document.getElementById('del_plan').style.visibility = "visible";
        }
    }

    window.onload = function() {
        var table = document.getElementById("table");
        var row_len = table.rows.length - 1;

        //ボタンの表示非表示を切り替える処理
        if (row_len >= VIEW) {
            document.getElementById('add_plan').style.visibility = "hidden";
        } else {
            document.getElementById('add_plan').style.visibility = "visible";
        }

        if (row_len == 1) {
            document.getElementById('del_plan').style.visibility = "hidden";
        } else {
            document.getElementById('del_plan').style.visibility = "visible";
        }
    }
</script>

<!-- フッター部分読み込み -->
<?php require_once('parts/footer.parts.php'); ?>