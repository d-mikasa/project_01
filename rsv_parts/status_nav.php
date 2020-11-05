<?php

?>
<nav>
		<a href="index.php"><img src="img/logo.png" class="header_logo"></a>
		<div class="status">
			<div class= "status_none" >ログイン</div>
			<span class="triangle"></span>
			<div class="status_none status_now">情報入力</div>
			<span class="triangle"></span>
			<div class="status_none">内容確認</div>
			<span class="triangle"></span>
			<div class="status_none">予約完了</div>
        </div>

        <div class="user_info" >
            <div class= "user_name">
            <div>ログイン中</div>
                <div><?=$_SESSION['user_name']?> 様 </div>
                <div class="logout_link"><a href="login.php?logout=true">ログアウト</a></div>
            </div>
        </div>
</nav>