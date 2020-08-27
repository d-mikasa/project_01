<?php
function getPage()
    {
        $str = strrpos($_SERVER['REQUEST_URI'], '/');
        $url = substr( $_SERVER['REQUEST_URI'] , $str , strlen($_SERVER['REQUEST_URI'])-$str );
        switch ($url) {
            case '/top.php':
                echo '<button disabled>トップページ</button>';
                break;

            default:
                # code...
                break;
        }
    }
