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
    $Room = new Room();
    $Room->deleteDetail($_POST['delete']);
    //重複削除が起きないようにリダイレクト
    header('Location: room_list.php');
}

if (!empty($_GET['sort'])) {

    switch (key($_GET)) {

        case strpos($_GET['sort'], 'asc') !== false: //sort_upが押された場合
            //_までの文字数カウント
            $str = strrpos($_GET['sort'], '-');

            //後半部分格納
            $content = substr($_GET['sort'], $str + 1);

            $getRoomAll = $Room->sortRoom('asc', $content);
            break;

        case strpos($_GET['sort'], 'desc') !== false: //sort_downが押された場合
            //_までの文字数カウント
            $str = strrpos($_GET['sort'], '-');

            //後半部分格納
            $content = substr($_GET['sort'], $str + 1);

            $getRoomAll = $Room->sortRoom('desc', $content);
            break;
    }
}
?>

<!-- ヘッダー部分読み込み -->
<?php require_once('parts/top.parts.php'); ?>

<main>

    <!--
    並び替えのボタン群
    -->
    <form action="room_list.php" name='sort_button' method="get">
        <table class="roomlist_table">
            <tr class="table_name">
                <td class="list_id">
                    ID
                    <button type="submit" value="asc-id" name="sort" id="up">▲</button>
                    <button type="submit" value="desc-id" name="sort" id="down">▼</button>
                </td>

                <td class="list_img">
                    画像
                </td>
                <td class="list_name">
                    部屋名
                    <button type="submit" value="asc-name" name="sort" id="up">▲</button>
                    <button type="submit" value="desc-name" name="sort" id="down">▼</button>
                </td>

                <td class="list_created_at">
                    登録日時
                    <button type="submit" value="asc-created_at" name="sort" id="up">▲</button>
                    <button type="submit" value="desc-created_at" name="sort" id="down">▼</button>
                </td>

                <td class="list_updated_at">
                    更新
                    <button type="submit" value="asc-updated_at" name="sort" id="up">▲</button>
                    <button type="submit" value="desc-updated_at" name="sort" id="down">▼</button>
                </td>
    </form>


    <!--
        リスト・新規作成ボタン・削除ボタン・編集ボタン
    -->
    <form action="" name='btn_form' method="post">
        <td class="list_create">
            <button type="button" onclick=" location.href= 'room_edit.php?mode=create&id=0' ">
                <p>新規作成</p>
            </button>
        </td>
        </tr>
        <?php foreach ($getRoomAll as $list) :?>
            <tr class="dataerea">
                <td class="id_data"><?=h($list['id'])?></td>
                <td class="img_data"><img src="../img/<?=h($list['img'])?>" alt="" class="listimage"></td>
                <td class="name_data"><?=h($list['name'])?></td>
                <td class="created_data"><?=h($list['created_at'])?></td>
                <td class="updated_data"><?=h($list['updated_at'])?></td>
                <td class="edit_group">
                    <p><button type="button" class="editButten" onclick=" location.href= 'room_edit.php?mode=edit&id=<?=$list['id']?>' ">編集</button></p>
                    <p><button type="submit" name="delete" value="<?=$list['id']?>" onclick="return btn_check()" class="deleteButten">削除</button></p>
                </td>
            </tr>
        <?php endforeach;?>
        </table>
    </form>
</main>

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
<?php require_once('parts/footer.parts.php');?>