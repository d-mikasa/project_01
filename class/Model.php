<?php
class Model
{
    protected $dbh;

    public function connect()
    {
        try {
            $this->dbh = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME, DBUSER, DBPASS);
            $this->dbh->exec('set names utf8');
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}

//////////////////////Adminの処理を読み込んでいる/////////////////////
require_once('admin/AdminUser.php');
require_once('admin/Room.php');
require_once('admin/GetPage.php');



//////////////////////User側の処理を読み込んでいる/////////////////////
require_once('UserLogin.php');
require_once('RoomShow.php');
require_once('UpdateReservation.php');
require_once('Getnav.php');
