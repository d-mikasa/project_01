<?php
function getPage()
{
    //URIを取得する（hoge_hoge.php)
    $url = basename($_SERVER['REQUEST_URI']);

    // ' 拡張子' （＋GETの値）から前の文字列を取得
    $str = strrpos($url, '.php');
    $temp = substr($url, 0, $str);

    //URIを分割して格納。content['1']は、_が存在しなかった場合作成しない。
    $hoge  = strrpos($temp, '_');
    $content['0'] = strstr($temp, '_') ? substr($temp, 0, $hoge) : substr($temp, -$hoge);
    strstr($temp, '_') ? $content['1'] = substr($temp, $hoge - strlen($temp) + 1) : '';

    //ジャンルを格納
    $genre = array(
        'room' => '部屋',
        'top' => 'トップページ'
    );

    //内容を格納
    $name = array(
        'list' => '一覧',
        'conf' => '確認画面',
        'done' => '完了画面',
        'edit' => '画面'
    );

    //新規作成か編集かを判断する
    $get_para = array(
        'edit' => '編集',
        'create' => '作成'
    );
    
    $disp_page = $genre[$content['0']] . (isset($_GET['mode']) ? $get_para[$_GET['mode']] : '') . (isset($content['1']) ? $name[$content['1']] : '');

    echo '<button class = "title_btn" disabled>' . $disp_page . '</button>';
}
