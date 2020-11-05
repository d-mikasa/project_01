<?php
function getPage()
{
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
        $name = substr($temp, $str_cnt - strlen($temp) + 1);
    }

    //ジャンルを格納
    $genre_list = array(
        'room' => '部屋',
        'top' => 'トップページ',
        'reservation' => '客室情報'
    );

    //内容を格納
    $name_list = array(
        'list' => '一覧',
        'conf' => '確認画面',
        'done' => '完了画面',
        'edit' => '画面'
    );

    //新規作成か編集かを判断する
    $param_list = array(
        'edit' => '編集',
        'create' => '作成'
    );

    echo '<button class="title_btn" disabled>' . $genre_list[$genre] . (isset($_GET['mode']) ? $param_list[$_GET['mode']] : '') . (isset($name) ? $name_list[$name] : '') . '</button>';
}
