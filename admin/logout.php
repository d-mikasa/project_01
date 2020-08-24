<?php
session_start();
$_SESSION['admin_login'] = false;
header('Location: login.php');