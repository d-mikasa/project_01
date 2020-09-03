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

//////////////////////処理を読み込んでいる/////////////////////
require_once('admin/AdminUser.php');
require_once('admin/RoomList.php');
require_once('admin/DeleteList.php');
require_once('admin/EditList.php');
require_once('admin/UpdateDetail.php');
require_once('admin/GetPage.php');
require_once('admin/ShowList.php');
require_once('admin/ImageUpdata.php');
