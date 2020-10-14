<?php
require_once('../class/Library.php');

//リダイレクト処理
if (empty($_SESSION['auth'])) {
    header('Location: login.php');
}
//roomテーブルの情報を全て取得
$a = new Room;
$room_list = $a->getRoomAll();
/*
押されたボタンの種類別に処理する
*/
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    switch (key($_POST)) {
        case 'create':
            //新規作成が押された場合
            //headerにGETをつけて飛ばす
            header('Location: room_edit.php?mode=create&id=0');
            break;

        case 'delete': //削除が押された場合
            $a = new Room();
            $a->deleteDetail($_POST['delete']);
            //重複削除が起きないようにリダイレクト
            header('Location: room_list.php');
            exit;
            break;

        case 'edit': //編集ボタンが押された場合
            $url = 'Location: room_edit.php?mode=edit&id=' . $_POST['edit'];

            //headerにGETをつけて飛ばす
            header($url);
            exit;
            break;

        case 'up_sort': //▲ボタンが押された場合
            $sort_main = array();
            foreach ($room_list as $key => $value) {
                if ($value[$_POST['up_sort']] == NULL) {
                    $value[$_POST['up_sort']]  = 'ー';
                }
                $sort_main[$key] = $value[$_POST['up_sort']];
                $sort_sub[$key] = $value['id'];
            }
            array_multisort($sort_main, SORT_ASC, $sort_sub, SORT_ASC, $room_list);
            break;

        case 'down_sort': //▼ボタンが押された場合
            $sort_main = array();
            foreach ($room_list as $key => $value) {
                if ($value[$_POST['down_sort']] == NULL) {
                    $value[$_POST['down_sort']]  = '0';
                }
                $sort_main[$key] = $value[$_POST['down_sort']];
                $sort_sub[$key] = $value['id'];
            }
            array_multisort($sort_main, SORT_DESC, $sort_sub, SORT_ASC, $room_list);
            break;

        default:
            //変な値が帰ってきたらとりあえずリダイレクト
            header('Location: room_list.php');
            break;
    }
}

?>

<!-- ヘッダー部分読み込み -->
<?php require_once('parts/top.parts.php'); ?>

<main>
    <form action="" name='btn_form' method="post">
        <table class="roomlist_table">
            <tr class="table_name">
                <td class="list_id">
                    ID
                    <button type="submit" value="id" name="up_sort" id="up">▲</button>
                    <button type="submit" value="id" name="down_sort" id="down">▼</button>
                </td>

                <td class="list_img">
                    画像
                </td>
                <td class="list_name">
                    部屋名
                    <button type="submit" value="name" name="up_sort" id="up">▲</button>
                    <button type="submit" value="name" name="down_sort" id="down">▼</button>
                </td>

                <td class="list_created_at">
                    登録日時
                    <button type="submit" value="created_at" name="up_sort" id="up">▲</button>
                    <button type="submit" value="created_at" name="down_sort" id="down">▼</button>
                </td>

                <td class="list_updated_at">
                    更新
                    <button type="submit" value="updated_at" name="up_sort" id="up">▲</button>
                    <button type="submit" value="updated_at" name="down_sort" id="down">▼</button>
                </td>
                <td class="list_create">
                    <button type="submit" name="create" value="新規作成">
                        <p>新規作成</p>
                    </button>
                </td>
            </tr>
            <?php foreach ($room_list as $list) : ?>
                <tr class="dataerea">
                    <td class="id_data"><?= $list['id'] ?></td>
                    <td class="img_data"><img src="../img/<?= $list['img'] ?>" alt="" class="listimage"></td>
                    <td class="name_data"><?= $list['name'] ?></td>
                    <td class="created_data"><?= $list['created_at'] ?></td>
                    <td class="updated_data"><?= $list['updated_at'] ?></td>
                    <td class="edit_group">
                        <p><button type="submit" name="edit" value="<?= $list['id'] ?>" class="editButten">編集</button></p>
                        <p><button type="submit" name="delete" value="<?= $list['id'] ?>" onclick="return btn_check()" class="deleteButten">削除</button></p>
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
<?php require_once('parts/footer.parts.php'); ?>