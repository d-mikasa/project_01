<?php
function checkLogin()
{
    if ($_SESSION['user_auth'] == false) {
        header('Location: login.php');
        exit();
    }
}
