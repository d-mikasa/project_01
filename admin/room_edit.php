<?php
require_once('library.php');

//GETの値がなかった場合、リストにリダイレクト


//最大表示領域
$max = 3;

//表示領域
$view = 2;

// print_r('<pre>');
// var_dump($_POST[ 'plan'][0]);
// print_r('</pre>');



//表示領域をPOSTで受け取った際には値を上書き
if (!empty($_POST['add'])) {
    $id = $_POST['add'];
    $view++;
    //最大表示領域を超えていた場合、表示領域を上書き
    if ($view > $max) {
        $view = $max;
    }
} else {
    //GET通信時
    $id = $_GET['id'];
}

$a = new edit_List();
$edit_detail = $a->Edit_detail($id);

//表示できるコンテンツのIDを配列に保存
//配列にはIDの若い順に格納される
for ($i = 0; $i < $view; $i++) {
    if (empty($edit_detail[$i]['id'])) {
        $viewNo[$i] = NULL;
    } else {
        $viewNo[$i] = $edit_detail[$i];
    }
}
// print_r('<pre>');
// var_dump($viewNo);
// print_r('</pre>');



?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin_style.css">
    <title>内容編集</title>
</head>


<body>
    <!-- ヘッダー部分読み込み -->
    <?php include('parts/nav.parts.php'); ?>
    <main>

        <form action="room_conf.php" method="post" enctype="multipart/form-data">

            <table class="roomedit_table">
                <?php for ($i = 0; $i < count($viewNo); $i++) : ?>
                    <tr>
                        <th rowspan="3">プラン[<?= $i + 1 ?>]</th>
                        <th>人数</th>
                        <td><input type="text" name="plan[<?= $i ?>][capacity]" value="<?php if (!empty($viewNo[$i]['capacity'])) echo $viewNo[$i]['capacity'] ?>"></td>
                        <td rowspan="3"><button>削除</button></td>
                    </tr>
                    <tr>
                        <th>料金</th>
                        <td><input type="text" name="plan[<?= $i ?>][price]" value="<?php if (!empty($viewNo[$i]['capacity'])) echo $viewNo[$i]['price'] ?>"></td>
                    </tr>
                    <tr>
                        <th>コメント</th>
                        <td colspan=""><textarea name="plan[<?= $i ?>][remarks]" cols="30" rows="10"> <?php if (!empty($viewNo[$i]['remarks'])) echo $viewNo[$i]['remarks'] ?> </textarea></td>
                    </tr>
                <?php endfor; ?>
                <?php if ($max > $view) : ?>
                    <tr>
                        <th colspan="4"><button type="submit" name="add" value="<?= $id ?>" formaction="room_edit.php">プランを追加する</button></th>
                    </tr>
                <?php endif; ?>
            </table>

            <p>画像の削除</p>
            <table>
                <tr>
                    <th>画像</th>
                    <td><input type="file" name="userfile"></td>
                </tr>
            </table>


            <p><input type="submit" value="更新する"></p>
        </form>


    </main>
    <!-- フッター部分読み込み -->
    <?php include('parts/footer.parts.php'); ?>
    <script>
    </script>
</body>

</html>