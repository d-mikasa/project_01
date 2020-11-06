<?php
    //URIを取得する（hoge_hoge.php)
    $url = basename($_SERVER['PHP_SELF']);

    // ' 拡張子' から前の文字列を取得
    $str = strrpos($url, '.php');
    $temp = substr($url, 0, $str);

    //URIを分割して格納。$nameは、_が存在しなかった場合作成しない。
    //_までの文字数を獲得
    $str_cnt  = strrpos($temp, '_');

    //genreの値を取得する
    $genre = strstr($temp, '_') ? substr($temp, 0, $str_cnt) : substr($temp, - $str_cnt);

    //tempに_があれば処理を行う
    if (strstr($temp, '_') != FALSE) {
        $state = substr($temp, $str_cnt - strlen($temp) + 1);
    }else{
        $state = $genre;
    }

?>
<nav>
		<a href="index.php"><img src="img/logo.png" class="header_logo"></a>
		<div class="status">
			<div class= "status_none <?=$state == 'login' ? 'status_now' : ''?>" >ログイン</div>
			<span class="triangle"></span>
			<div class="status_none <?=$state == 'reservation' ? 'status_now' : ''?>">情報入力</div>
			<span class="triangle"></span>
			<div class="status_none <?=$state == 'conf' ? 'status_now' : ''?>">内容確認</div>
			<span class="triangle"></span>
			<div class="status_none <?=$state == 'done' ? 'status_now' : ''?>">予約完了</div>
        </div>

        <div class="user_info" >
            <div class= "user_name">
            <div>ログイン中</div>
                <div><?=$_SESSION['user_name']?> 様 </div>
                <div class="logout_link"><a href="login.php?logout=true">ログアウト</a></div>
            </div>
        </div>
</nav>