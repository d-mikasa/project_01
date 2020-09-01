<?php
//データベースの中にIDの一致するものを検索する
class DeleteList extends Model
{
    public function Delete_detail($id)
    {
        parent::connect();
        $pdo = $this->dbh;
        $sql = 'DELETE FROM room WHERE id = ' . $id;
        $pdo->query($sql);

        $sql = 'DELETE FROM room_detail WHERE room_id = ' . $id;
        $pdo->query($sql);
    }
}
