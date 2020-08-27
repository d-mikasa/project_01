<?php
function getPage()
{
    $str = strrpos($_SERVER['REQUEST_URI'], '/');
    $url = substr($_SERVER['REQUEST_URI'], $str, strlen($_SERVER['REQUEST_URI']) - $str);
    switch ($url) {
        case '/top.php':
            echo '<button disabled>トップページ</button>';
            break;
        case '/room_list.php':
            echo '<button disabled>部屋一覧</button>';
            break;
        case '/room_edit.php':
            echo '<button disabled>部屋編集</button>';
            break;
        case '/room_conf.php':
            echo '<button disabled>編集内容の確認</button>';
            break;
        case '/room_done.php':
            echo '<button disabled>編集完了</button>';
            break;

        default:
            # code...
            break;
    }
}
