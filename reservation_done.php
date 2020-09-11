<?php
require_once('class/Library.php');
echo 'POSTの値' . '<br>';
print_r('<pre>');
print_r($_POST);
print_r('</pre>');
$pdo = new UpdateReservation();
$days = $pdo -> Reservation($_POST['room_name'], $_POST['check_in'], $_POST['check_out'], $_POST['capacity'], $_POST['peyment'], $_POST['price'],$_POST['room_name']);

echo 'DAYSの値' . '<br>';
print_r('<pre>');
print_r($days);
print_r('</pre>');
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body></body>

</html>