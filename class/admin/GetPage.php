<?php
function getPage()
{
//URIを取得する（hoge_hoge.php)
    $url = basename($_SERVER['REQUEST_URI']);

    // ' 拡張子' （＋GETの値）から前の文字列を取得
    $str = strrpos($url, '.php');
    $temp = substr($url, 0, $str);

    //配列内に_区切りで格納（最大要素数２つ）
    $content =  explode("_", $temp,2);

    //urlの内容
    $name['room'] = '部屋';
    $name['top'] = 'トップページ';
    $name['list'] = '一覧';
    $name['conf'] = '確認画面';
    $name['done'] = '完了画面';

    //新規作成か編集かを判断する
    if (!empty($_GET['mode'])) {
        switch ($_GET['mode']) {
            case 'edit':
                $name['edit'] = '編集';
                break;
            case 'create':
                $name['edit'] = '新規作成';
                break;
            default:
                $name['edit'] = '編集';
                break;
        }
    } else {
        $name['edit'] = '編集';
    }

    //top.phpがあるため、forでreplaceする
for ($i = 0; $i <  count($content); $i++) {
    $disp_page[$i] =  str_replace($content[$i],$name[$content[$i]],$content[$i]);
}

    //配列の結合
    $disp_page =  implode("", $disp_page);

    echo '<button class = "title_btn" disabled>' . $disp_page . '</button>';
}
