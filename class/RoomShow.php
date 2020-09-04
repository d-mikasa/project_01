<?php
class RoomShow extends Model
{
    public function room()
    {
        parent::connect();
        $sql = 'SELECT room_detail.id, room.name, room_detail.capacity, room_detail.price, room_detail.remarks FROM room_detail JOIN room ON room_detail.room_id = room.id';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([]);
        $result = $stmt->fetchAll();

        if (empty($result)) {
            return '部屋が存在しません。';
        }

        return $result;
    }

    public function room_select($id)
    {
        try {
            parent::connect();
            $sql = 'SELECT room_detail.id , room.name, room_detail.capacity, room_detail.price, room_detail.remarks FROM room_detail INNER JOIN room ON room_detail.room_id = room.id WHERE room_detail.id = ?';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            header('Content-Type: text/plain; charset=UTF-8', true, 500);
            exit($e->getMessage());
        }
    }

    
    public function room_reservation($id)
    {
        try {
            parent::connect();
            $sql = 'SELECT * FROM reservation WHERE room_detail_id = ?';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            header('Content-Type: text/plain; charset=UTF-8', true, 500);
            exit($e->getMessage());
        }
    }
}
