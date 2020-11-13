<?php
//URIを取得する（hoge_hoge.php)
$url = basename($_SERVER['PHP_SELF']);

// ' 拡張子' から前の文字列を取得
$str = strrpos($url, '.php');
$state = substr($url, 0, $str);

?>
<nav>
	<a href="index.php">
        <img src="img/logo.png" class="header_logo">
    </a>
	<div class="status">
		<div class= "status_none <?=$state == 'login' ? 'status_now' : ''?>" >ログイン</div>
		<span class="triangle"></span>
		<div class="status_none <?=$state == 'reservation' ? 'status_now' : ''?>">情報入力</div>
		<span class="triangle"></span>
		<div class="status_none <?=$state == 'reservation_conf' ? 'status_now' : ''?>">内容確認</div>
		<span class="triangle"></span>
		<div class="status_none <?=$state == 'reservation_done' ? 'status_now' : ''?>">予約完了</div>
    </div>
    <?php if($state != 'login'):?>
        <div class="user_info" >
            <div class= "user_name">
                <div>ログイン中</div>
                <div><?=$_SESSION['user_name']?> 様 </div>
                <div class="logout_link"><a href="logout.php">ログアウト</a></div>
            </div>
        </div>
    <?php endif;?>
</nav>