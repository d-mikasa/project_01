<?php
function getPage()
{

    //URLのドメインより後ろを取得
    // '/'までの文字数を取得
    $str = strrpos($_SERVER['REQUEST_URI'], '/');

    // '/'までの文字列を取得
    $get_url = substr($_SERVER['REQUEST_URI'], $str + 1);

    //.phpを文字列から削除
    $str = strrpos($get_url, '.php');
    $url = substr($get_url, 0, $str);

    // ' _' から前の文字列を取得
    $str = strrpos($url, '_');

    //前半部分格納
    $genre = substr($url, 0, $str);

    //後半部分格納
    $content = substr($url, $str + 1);

    //urlの内容
    $name['room']['top'] = 'トップページ';
    $name['room']['list'] = '部屋一覧';
    $name['room']['conf'] = '確認画面';
    $name['room']['done'] = '完了画面';

    //新規作成か編集かを判断する
    if (!empty($_GET)) {
        switch ($_GET['mode']) {
        case 'edit':
            $name['room']['edit'] = '部屋編集';
            break;
        case 'create':
            $name['room']['edit'] = '新規作成';
            break;
        default:
            $name['room']['edit'] = '編集画面';
            break;
        }
    }else{
        $name['room']['edit'] = '編集画面';
    }

    $disp_page = $name[$genre][$content];

    echo '<button class = "title_btn" disabled>' . $disp_page . '</button>';
}
