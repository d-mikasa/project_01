<?php
require_once('../class/Library.php');

//リダイレクト処理
if (empty($_SESSION['auth'])) {
    header('Location: login.php');
}
//roomテーブルの情報を全て取得
$Room = new Room;
$getRoomAll = $Room->getRoomAll();

/*
押されたボタンの種類別に処理する
*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch (key($_POST)) {
        case 'delete': //削除が押された場合

            $a = new Room();
            $a->deleteDetail($_POST['delete']);
            //重複削除が起きないようにリダイレクト
            header('Location: room_list.php');
            exit;
            break;

        }
}

if(!empty($_POST['up_sort'])){
    $getRoomAll = $Room ->sortRoom('asc',$_POST['up_sort']);

}else if(!empty($_POST['down_sort'])){
    $getRoomAll = $Room ->sortRoom('desc',$_POST['down_sort']);
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
                    <button type="button" onclick=" location.href= 'room_edit.php?mode=create&id=0' ">
                        <p>新規作成</p>
                    </button>
                </td>
            </tr>
            <?php foreach ($getRoomAll as $list) : ?>
                <tr class="dataerea">
                    <td class="id_data"><?= $list['id'] ?></td>
                    <td class="img_data"><img src="../img/<?= $list['img'] ?>" alt="" class="listimage"></td>
                    <td class="name_data"><?= $list['name'] ?></td>
                    <td class="created_data"><?= $list['created_at'] ?></td>
                    <td class="updated_data"><?= $list['updated_at'] ?></td>
                    <td class="edit_group">
                        <p><button type="button" class="editButten" onclick=" location.href= 'room_edit.php?mode=edit&id=<?= $list['id'] ?>' ">編集</button></p>
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