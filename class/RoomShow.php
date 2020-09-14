<?php
class RoomShow extends Model
{
    public function room() //プルダウンリストに表示する部屋を検索する
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

    public function reservation_check($id) //選択した部屋の予約状況をチェックする
    {
        try {
            parent::connect();
            $sql = <<<EOD
            SELECT
            reservation.id,
             reservation.user_id,
            reservation.room_id,
            reservation.room_detail_id,
            reservation.room_detail_name,
            reservation.number,
            reservation.total_price,
            reservation.status,
            reservation.created_at,
            reservation.updated_at,
            reservation.delete_flg,
            reservation_detail.price AS "reservation_price",
            GROUP_CONCAT(reservation_detail.date) AS "date",
            room.name AS "room_name",
            room_detail.capacity,
            room_detail.price,
            room_detail.name
            FROM reservation
            INNER JOIN reservation_detail ON reservation.id = reservation_detail.reservation_id
            INNER JOIN room ON reservation.room_id = room.id
            INNER JOIN room_detail ON reservation.room_detail_id = room_detail.id
            WHERE  reservation.room_id = ?
            GROUP BY reservation.id
            EOD;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

            if (empty($result)) {
                return 'not reservation room';
            }

            return $result;
        } catch (PDOException $e) {
            header('Content-Type: text/plain; charset=UTF-8', true, 500);
            exit($e->getMessage());
        }
    }


    public function room_detail($id)//選択した部屋の情報を取得
    {
        try {
            parent::connect();
            $sql = <<<EOD
            SELECT
            room.id,
            room.name,
            room_detail.id AS "detail_id",
            room_detail.capacity,
            room_detail.remarks,
            room_detail.price,
            room_detail.name AS "detail_name"
            FROM room
            INNER JOIN room_detail
            ON room.id = room_detail.room_id
            WHERE room_detail.id = ?
            EOD;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($result)) {
                return 'not found room';
            }
            return $result;
        } catch (PDOException $e) {
            header('Content-Type: text/plain; charset=UTF-8', true, 500);
            exit($e->getMessage());
        }
    }
}
