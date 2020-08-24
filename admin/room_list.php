<?php
require_once('library.php');

if ($_SESSION['auth'] == false) {
    header('Location: login.php');
}

$a = new roomList();
$room_list = $a->room_get();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    print_r($_POST);
    if (!empty($_POST['delete'])) {
        // 削除処理

        $a = new Delete_list();
        $a->Delete_detail($_POST['delete']);

        //重複削除が起きないようにリダイレクト
        header('Location: room_list.php');
    }

    //POSTで帰ってきて、尚且つソートが選択されていた場合
    //昇順（▲）が押された場合
    if (!empty($_POST['up_sort'])) {
        foreach ($room_list as $key => $value) {
            $sort_keys[$key] = $value[$_POST['up_sort']];
        }
        array_multisort($sort_keys, SORT_ASC, $room_list);
    }

        //降順（▲）が押された場合
    if (!empty($_POST['down_sort'])) {
        foreach ($room_list as $key => $value) {
            $sort_keys[$key] = $value[$_POST['down_sort']];
        }
        array_multisort($sort_keys, SORT_DESC, $room_list);
    }
}





?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin_style.css">
    <title>ROOM_LIST</title>
</head>

<body>
    <!-- ヘッダー部分読み込み -->
    <?php include('parts/nav.parts.php'); ?>

    <main>
        <form action="" name='btn_form' method="post">

            <table class="roomlist_table">
                <tr class="table_name">
                    <th class="list_id">ID　
                        <p>
                            <button type="submit" value="id" name="up_sort">▲</button>
                            <button type="submit" value="id" name="down_sort">▼</button>
                        </p>
                    </th>
                    <th class="list_img">画像　
                        <p>
                            <button type="submit" value="img" name="up_sort">▲</button>
                            <button type="submit" value="img" name="down_sort">▼</button>
                        </p>
                    </th>
                    <th class="list_name">部屋名
                        <p>
                            <button type="submit" value="name" name="up_sort">▲</button>
                            <button type="submit" value="name" name="down_sort">▼</button>
                        </p>
                    </th>

                    </th>
                    <th class="list_created_at">登録日時　
                        <p>
                            <button type="submit" value="created_at" name="up_sort">▲</button>
                            <button type="submit" value="created_at" name="down_sort">▼</button>
                        </p>
                    </th>
                    <th class="list_updated_at">更新　
                        <p>
                            <button type="submit" value="updated_at" name="up_sort">▲</button>
                            <button type="submit" value="updated_at" name="down_sort">▼</button>
                        </p>
                    </th>
                    <th class="list_create">
                        <button type="button" value="新規作成" name="create" onclick="return btn_check(this.name)">新規作成</button>
                    </th>
                </tr>
                <?php foreach ($room_list as $list) : ?>
                    <tr>
                        <td><?= $list['id'] ?></td>
                        <td><img src="../img/<?= $list['img'] ?>" alt=""></td>
                        <td><?= $list['name'] ?></td>
                        <td><?= $list['created_at'] ?></td>
                        <td><?= $list['updated_at'] ?></td>
                        <td>
                            <p><button type="submit" name="edit" onclick="return btn_check(this.name)">編集</button></p>
                            <p><button type="submit" name="delete" value="<?= $list['id'] ?>" onclick="return btn_check(this.name)">削除</button></p>
                        </td>
                    </tr>
                <?php endforeach; ?>
        </form>
    </main>
    </table>


    <!-- フッター部分読み込み -->
    <?php include('parts/footer.parts.php'); ?>


    <script>
        function btn_check(btn) {
            switch (btn) {

                case 'edit':
                    console.log('edit');

                    // location.href = 'room_edit.php';
                    return false;
                    break;

                case 'create':

                    console.log('create');
                    return false;
                    break;

                case 'delete':
                    if (btn == 'delete') {
                        var res = confirm("削除してもよろしいですか？");
                        if (res == false) {
                            // 「いいえ」ならフォーム送信をやめる
                            console.log('delete_none');
                            return false;
                        } else {
                            console.log('delete_ok');
                        }
                    }
                    break;

                default:
                    console.log('そのほかの動作');

            }
        }
    </script>

</body>

</html>