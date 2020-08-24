<?php
require_once ('model.php');

//データベースの中にIDの一致するものを検索する
class Delete_list extends Model
{
   public function Delete_detail($id)
   {
       parent::connect();
       $pdo = $this -> dbh;
       $sql = 'DELETE FROM room_detail WHERE id = ' . $id;
       $pdo->query($sql);
   }
}
