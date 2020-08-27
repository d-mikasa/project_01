<?php
class ShowList extends Model
{
    public function show_room()
    {
        parent::connect();
        $pdo = $this -> dbh;
        $sql = 'SELECT * FROM room';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function show_detail($id)
    {
        parent::connect();
        $pdo = $this -> dbh;
        $sql = 'SELECT * FROM room_detail WHERE room_id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetchAll();
        return $result;
    }
}
