<?php
require_once('../class/Library.php');

if ($_SESSION['auth'] == false) {
    header('Location: login.php');
}

$a = new roomList();
$room_list = $a->room_get();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    switch (key($_POST)) {
        case 'create':
            $_SESSION['mode'] = 'create';
            header('Location: room_edit.php');
            break;

        case 'delete':
            // 削除処理
            $a = new Deletelist();
            $a->Delete_detail($_POST['delete']);

            //重複削除が起きないようにリダイレクト
            header('Location: room_list.php');
            exit;
            break;

        case 'edit':
            $_SESSION['mode'] = 'edit';
            $_SESSION['data_id'] = $_POST['edit'];
            header('Location: room_edit.php');
            exit;
            break;

        default:
            # code...
            break;
    }

    //POSTで帰ってきて、尚且つソートが選択されていた場合
    //昇順（▲）が押された場合
    if (!empty($_POST['up_sort'])) {
        $sort_main = array();
        $sort_main = array();
        foreach ($room_list as $key => $value) {
            if ($value['img'] == NULL or $value['name'] == NULL) {
                $value[$_POST['up_sort']]  = 'ー';
            }
            $sort_main[$key] = $value[$_POST['up_sort']];
            $sort_sub[$key] = $value['id'];
        }
        array_multisort($sort_main, SORT_ASC, $sort_sub, SORT_DESC, SORT_STRING, $room_list);
    }

    //降順（▲）が押された場合
    if (!empty($_POST['down_sort'])) {
        $sort_main = array();
        $sort_main = array();
        foreach ($room_list as $key => $value) {
            if ($value['img'] == NULL or $value['name'] == NULL) {
                $value[$_POST['down_sort']]  = '0';
            }
            $sort_main[$key] = $value[$_POST['down_sort']];
            $sort_sub[$key] = $value['id'];
        }
        array_multisort($sort_main, SORT_DESC, $sort_sub, SORT_DESC, SORT_STRING, $room_list);
    }
}

?>

<!-- ヘッダー部分読み込み -->
<?php include('parts/top.parts.php'); ?>

<main>
    <form action="" name='btn_form' method="post">

        <table class="roomlist_table">
            <tr class="table_name">
                <th class="list_id">
                    ID
                    <p>
                        <button type="submit" value="id" name="up_sort">▲</button>
                        <button type="submit" value="id" name="down_sort">▼</button>
                    </p>
                </th>
                <th class="list_img">画像　
                </th>
                <th class="list_name">部屋名
                    <p>
                        <button type="submit" value="name" name="up_sort">▲</button><br>
                        <button type="submit" value="name" name="down_sort">▼</button>
                    </p>
                </th>

                </th>
                <th class="list_created_at">登録日時　
                    <p>
                        <button type="submit" value="created_at" name="up_sort">▲</button><br>
                        <button type="submit" value="created_at" name="down_sort">▼</button>
                    </p>
                </th>
                <th class="list_updated_at">更新　
                    <p>
                        <button type="submit" value="updated_at" name="up_sort">▲</button><br>
                        <button type="submit" value="updated_at" name="down_sort">▼</button>
                    </p>
                </th>
                <th class="list_create">
                    <button type="submit" name="create" value="新規作成">新規作成</button>
                </th>
            </tr>
            <?php foreach ($room_list as $list) : ?>
                <tr>
                    <td class="id_data"><?= $list['id'] ?></td>
                    <td class="img_data"><img src="../img/<?= $list['img'] ?>" alt="" class="listimage"></td>
                    <td class="name_data"><?= $list['name'] ?></td>
                    <td class="created_data"><?= $list['created_at'] ?></td>
                    <td class="updated_data"><?= $list['updated_at'] ?></td>
                    <td class="edit_group">
                        <p><button type="submit" name="edit" value="<?= $list['id'] ?>">編集</button></p>
                        <p><button type="submit" name="delete" value="<?= $list['id'] ?>" onclick="return btn_check()">削除</button></p>
                    </td>
                </tr>
            <?php endforeach; ?>
    </form>
</main>
</table>

<script>
    function btn_check() {
        var res = confirm("削除してもよろしいですか？");
        if (res == false) {
            // 「いいえ」ならフォーム送信をやめる
            console.log('delete_none');
            return false;
        } else {
            console.log('delete_ok');
        }
    }
</script>

<!-- フッター部分読み込み -->
<?php include('parts/footer.parts.php'); ?>





</html>