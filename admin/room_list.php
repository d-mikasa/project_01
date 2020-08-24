<?php
require_once('room.db.php');
require_once('delete.list.php');
session_start();

if ($_SESSION['admin_login'] == false) {
    header('Location: login.php');
}
$a = new roomList();
$room_list = $a->room_get();



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    print_r($_POST);
    if (!empty($_POST['delete'])) {
        echo 'hogehogehoge';
        //削除処理
        // $a = new Delete_list();
        // $a->Delete_detail($_POST['delete']);

        //重複削除が起きないようにリダイレクト
        // header('Location: room_list.php');
    }
}


?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin_style.css">

    <!-- JavaScript読み込み -->
    <script src="../js/admin/delete_check.js"></script>

    <title>ROOM_LIST</title>
</head>

<body>
    <!-- ヘッダー部分読み込み -->
    <?php include('parts/nav.parts.php'); ?>

    <main>
        <form action="" name='btn_form' method="post" >

            <table class="roomlist_table">
                <tr class="table_name">
                    <th class="list_id">ID</th>
                    <th class="list_img">画像</th>
                    <th class="list_name">部屋名</th>
                    <th class="list_paracity">人数</th>
                    <th class="list_created_at">登録日時</th>
                    <th class="list_updated_at">更新</th>
                    <th class="list_create">
                        <button type="button" value="新規作成" name="create" onclick="btn_check(this.name)">新規作成</button>
                    </th>
                </tr>
                <?php foreach ($room_list as $list) : ?>
                    <tr>
                        <td><?= $list['room_id'] ?></td>
                        <td><img src="../img/<?= $list['img'] ?>" alt=""></td>
                        <td><?= $list['name'] ?></td>
                        <td><?= $list['capacity'] ?></td>
                        <td><?= $list['created_at'] ?></td>
                        <td><?= $list['updated_at'] ?></td>
                        <td>
                            <p><button type="submit" name="edit" onclick="btn_check(this.name)">編集</button></p>
                            <p><button type="submit" name="delete" value="<?= $list['room_id'] ?>" onclick="btn_check(this.name, this.value)">削除</button></p>
                        </td>
                    </tr>
                <?php endforeach; ?>
        </form>
    </main>
    </table>


    <!-- フッター部分読み込み -->
    <?php include('parts/footer.parts.php'); ?>


    <script>
        function btn_check(btn,vle) {
            alert(btn);
            alert(vle);
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
                            return false;
                        } else {
                            // document.btn_form.submit();
                        }
                    }
                    break;

                default:
                    console.log('何かしらのエラーが起きてます。');

            }
        }
    </script>

</body>

</html>