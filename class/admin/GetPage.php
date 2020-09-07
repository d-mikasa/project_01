<?php
function getPage()
{
    $str = strrpos($_SERVER['REQUEST_URI'], '/');
    $url = substr($_SERVER['REQUEST_URI'], $str, strlen($_SERVER['REQUEST_URI']) - $str);
    switch ($url) {
        case '/top.php':
            echo '<button class = "title_btn" disabled>トップページ</button>';
            break;
        case '/room_list.php':
            echo '<button class = "title_btn"  disabled>部屋一覧</button>';
            break;
        case '/room_edit.php':
            echo '<button class = "title_btn"  disabled>部屋編集</button>';
            break;
        case '/room_conf.php':
            echo '<button class = "title_btn"  disabled>編集内容の確認</button>';
            break;
        case '/room_done.php':
            echo '<button class = "title_btn"  disabled>編集完了</button>';
            break;

        default:
        echo '<button class = "title_btn"  disabled>CICACU管理者ページ</button>';
            break;
    }
}