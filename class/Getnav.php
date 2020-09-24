<?php
function getNav($state)
{

	$login = 'status_none';
	$reservation = 'status_none';
	$conf = 'status_none';
	$done = 'status_none';

    // $str = strrpos($_SERVER['REQUEST_URI'], '/');
		// $url = substr($_SERVER['REQUEST_URI'], $str, strlen($_SERVER['REQUEST_URI']) - $str);
		// echo $url;
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
		<img src="img/logo.png" class="header_logo">
		<div class="status">
			<div class= " $login " >ログイン</div>
			<span class="triangle"></span>
			<div class=" $reservation ">情報入力</div>
			<span class="triangle"></span>
			<div class=" $conf">内容確認</div>
			<span class="triangle"></span>
			<div class=" $done ">予約完了</div>
		</div>
	</nav>
	EOD;

	echo $parts;
}