<?php
function checkLogin()
{
    if ($_SESSION['user_auth'] == false) {
        header('Location: login.php');
        exit();
    }

    function logout()
    {
        unset($_SESSION['user_auth']);
        header('Location: login.php');
        exit();
    }
}
