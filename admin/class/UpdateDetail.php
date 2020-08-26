<?php
class UpdateDetail extends Model
{
    public function update($id, $list)
    {
        parent::connect();
        $pdo = $this->dbh;
        $sql = 'DELETE FROM room_detail WHERE room_id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        for ($i = 0; $i < count($list) ; $i++) {
        $sql = 'INSERT INTO room_detail(room_id,capacity,remarks,price) VALUES (?,?,?,?)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $list[$i]['capacity'], $list[$i]['remarks'], $list[$i]['price']]);
        }
    }
}
