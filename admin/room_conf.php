<?php
const IMGS_PATH = '../img/images';
// アクセスを許可する
exec('sudo chmod 0777 ' . IMGS_PATH);

if (!empty($_FILES)) {
    if ($_FILES['userfile']['error'] == UPLOAD_ERR_OK) {
        $name = $_FILES['userfile']['name'];
        $name = mb_convert_encoding($name, 'cp932', 'utf8');
        $temp = $_FILES['userfile']['tmp_name'];
        $result = move_uploaded_file($temp, IMGS_PATH . $name);
        if ($result == true) {
            $message = 'ファイルをアップロードしました';
        } else {
            $message = 'ファイルの移動に失敗しました';
        }
    } elseif ($_FILES['userfile']['error'] == UPLOAD_ERR_NO_FILE) {
        $message = 'ファイルがアップロードされませんでした';
    } else {
        $message = 'ファイルのアップロードに失敗しました';
    }
}
// 元の状態に戻す
exec('sudo chmod 0755 ' . IMGS_PATH);


print_r('<pre>');
var_dump($_POST['plan']);
print_r('</pre>');
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    DONEだYOU！
    \テレレレっ♪/
</body>

</html>