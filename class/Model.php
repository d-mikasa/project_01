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

//////////////////////UserParmissionの処理をInclude//////////////////////
require_once('AdminUser.php');
require_once('RoomList.php');
require_once('DeleteList.php');
require_once('EditList.php');
require_once('UpdateDetail.php');
require_once('GetPage.php');
require_once('Hsc.php');
require_once('ShowList.php');
