<?php
function checkLogin()
{
    if (empty($_SESSION['user_auth'])) {
        header('Location: login.php');
        exit();
    }
}
