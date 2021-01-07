<?php

$hoge = array(
    array(
        'id' => '1',
        'name' => 'aaa',
    ),
    array(
        'id' => '2',
        'name' => 'bbb',
    ),
    array(
        'id' => '3',
        'name' => 'ccc',
    )
);

echo '<pre>';
print_r($hoge);
echo '</pre>';

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <form action="test_copy.php" method="post">

        <input type="hidden" name="1[1][id]" value="<?= $hoge[0]['id'] ?>">
        <input type="hidden" name="1[1][name]" value="<?= $hoge[0]['name'] ?>">
        <input type="hidden" name="1[2][id]" value="<?= $hoge[1]['id'] ?>">
        <input type="hidden" name="1[2][name]" value="<?= $hoge[1]['name'] ?>">
        <input type="hidden" name="1[3][id]" value="<?= $hoge[2]['id'] ?>">
        <input type="hidden" name="1[3][name]" value="<?= $hoge[2]['name'] ?>">

        <input type="hidden" name="2[1][id]" value="<?= $hoge[0]['id'] ?>">
        <input type="hidden" name="2[1][name]" value="<?= $hoge[0]['name'] ?>">
        <input type="hidden" name="2[2][id]" value="<?= $hoge[1]['id'] ?>">
        <input type="hidden" name="2[2][name]" value="<?= $hoge[1]['name'] ?>">
        <input type="hidden" name="2[3][id]" value="<?= $hoge[2]['id'] ?>">
        <input type="hidden" name="2[3][name]" value="<?= $hoge[2]['name'] ?>">

        <p><input type="submit" value="送信"></p>
    </form>
</body>

</html>