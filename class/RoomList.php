<?php

 //データベースの中にIDの一致するものを検索する
class RoomList extends Model
{
    public function room_get()
    {
        parent::connect();
        $pdo = $this -> dbh;
        $sql = 'SELECT * FROM room';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
}
