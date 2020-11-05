<?php
function getNav($state)
{
    require_once('class/Library.php');

    $login = 'status_none';
    $reservation = 'status_none';
    $conf = 'status_none';
    $done = 'status_none';

    $user_login = new UserLogin();
    $name = $user_login ->getUserName($_SESSION['user_auth']);
    switch ($state) {
        case 'login':
            $login = 'status_now';
            break;
        case 'reservation':
            $reservation = 'status_now';
            break;
        case 'conf':
            $conf = 'status_now';
            break;
        case 'done':
            $done = 'status_now';
            break;
        default:
            break;
    }

    $parts = <<<EOD
		<nav>
		<a href="index.php"><img src="img/logo.png" class="header_logo"></a>
		<div class="status">
			<div class= " $login " >ログイン</div>
			<span class="triangle"></span>
			<div class=" $reservation ">情報入力</div>
			<span class="triangle"></span>
			<div class=" $conf">内容確認</div>
			<span class="triangle"></span>
			<div class=" $done ">予約完了</div>
        </div>
        <div class="user_info" >
            <div class= "user_name">
            <div>ログイン中</div>
                <div>$name 様 </div>
                <div class="logout_link"><a href="login.php?logout=true">ログアウト</a></div>
            </div>
        </div>
	</nav>
EOD;

    echo $parts;
}
