<?php
require_once('class/Library.php');
checkLogin();
?>
<?php require_once('rsv_parts/head_info.php');?>
<body class="background_reservation">
    <?php require_once('rsv_parts/status_nav.php')?>
    <main class="reservation_main">
        <div class="injustice">
            不正な値が入力されました。<br>
            再度初めからやり直してください。
            <div class="injustice_link">
                <a href="reservation.php">予約画面へ戻る</a>
            </div>
        </div>

    </main>