<?php
function getPage()
{

    //URLのドメインより後ろを取得
    $str = strrpos($_SERVER['REQUEST_URI'], '/');
    $get_url = substr($_SERVER['REQUEST_URI'], $str + 1);

    //パーツ分け
    $str = strrpos($get_url, '.php');
    $url = substr($get_url, 0, $str);

    //URLの_を削除する
    $url = str_replace('_', '', $url);

    //GETパラメータを取得
    if (!empty($_GET)) {
        $url = $url . $_GET['mode'];
    }

    //文字列置換コーナー
    //getの内容
    $url = str_replace('"create"', '[新規作成]', $url);
    $url = str_replace('"edit"', '[内容編集]', $url);

    //urlの内容
    $url = str_replace('room', '客室', $url);
    $url = str_replace('top', 'トップページ', $url);
    $url = str_replace('list', '一覧', $url);
    $url = str_replace('edit', '編集', $url);
    $url = str_replace('conf', '編集確認', $url);
    $url = str_replace('done', '編集完了', $url);
    echo '<button class = "title_btn" disabled>' . $url . '</button>';
}
