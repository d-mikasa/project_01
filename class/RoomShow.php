<?php
class RoomShow extends Model
{
    public function room()//プルダウンリストに表示する部屋を検索する
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

    public function room_select($id)//選択した部屋の内容を表示する
    {
        try {
            parent::connect();
            $sql = 'SELECT room_detail.id , room.name, room_detail.capacity, room_detail.price, room_detail.remarks FROM room_detail INNER JOIN room ON room_detail.room_id = room.id WHERE room_detail.id = ?';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            $sql = 'SELECT reservation.id, reservation.user_id, reservation.room_detail_id, reservation.number, reservation.total_price, reservation.status, reservation.created_at, reservation.updated_at, reservation.delete_flg, reservation_detail.price, GROUP_CONCAT(reservation_detail.date) AS "date" FROM reservation INNER JOIN reservation_detail ON reservation.id = reservation_detail.reservation_id GROUP BY reservation.id WHERE reservation.id = ?';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(empty($result)){
                return 'not reservation room';
            }
            return $result;
        } catch (PDOException $e) {
            header('Content-Type: text/plain; charset=UTF-8', true, 500);
            exit($e->getMessage());
        }
    }
}
