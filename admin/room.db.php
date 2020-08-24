<?php
require_once ('model.php');

 //データベースの中にIDの一致するものを検索する
class roomList extends Model
{
    public function room_get()
    {
        parent::connect();
        $pdo = $this -> dbh;
        $sql = 'SELECT room.id, room.name, room.img, room.created_at, room.updated_at, room.delete_flg, room_detail.capacity, room_detail.remarks, room_detail.price, room_detail.id as room_id FROM room JOIN room_detail ON room.id = room_detail.room_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
}
