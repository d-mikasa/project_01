<?php
//////////////////////セッションをスタート//////////////////////
session_start();

//////////////////////サーバの接続設定//////////////////////
require_once('Const.php');

//////////////////////各種サーバ周りの処理//////////////////////
require_once('Model.php');

//////////////////////htmlspecialchars//////////////////////
require_once('Hsc.php');

//////////////////////コンソール表示用//////////////////////
require_once('Console_log.php');

//////////////////////ログイン状態をチェック//////////////////////
require_once('CheckLogin.php');
