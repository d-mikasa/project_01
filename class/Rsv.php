<?php

class rsvUpdate extends Model
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



    /**
     *予約情報のアップデート
     *
     *チェックインとチェックアウトから宿泊日数を取得、それぞれreservationテーブルに追加、メールのためにユーザー情報を取得。
     *
     *@param $detail_id reservaion_detailのID
     *@param $check_in チェックインの日付
     *@param $check_out チェックアウトの日付
     *@param $capacity 宿泊人数
     *@param $peyment 支払い方法
     *@param $price 請求額
     *@param $name 部屋名
     *@param $room_id 部屋番号、room_id

     *@return $return_list メールを送信するための情報群
     */
    public function into_reservation($detail_id, $check_in, $check_out, $capacity, $peyment, $price, $name = NULL, $room_id)
    {
        parent::connect();
        $pdo = $this->dbh;

        //宿泊数をカウント
        for ($i = date('Ymd', strtotime($check_in)); $i < date('Ymd', strtotime($check_out)); $i++) {
            $year = substr($i, 0, 4);
            $month = substr($i, 4, 2);
            $day = substr($i, 6, 2);
            if (checkdate($month, $day, $year)) {
                $days[] = date('Y-m-d H:i:s', strtotime($i));
            }
        }

        //宿泊日数を定義
        $stay_total = count($days);

        //合計金額　宿泊人数＊料金＊宿泊日数
        $total_price = intval($capacity) * intval($price) * $stay_total;

        //reservationに追加する
        $sql = <<<EOD
        INSERT INTO reservation(user_id, room_id, room_detail_id, room_detail_name, number, total_price,status,created_at,updated_at,delete_flg)
        VALUES(?,?,?,?,?,?,?,?,?,?)
        EOD;
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_auth'], $room_id, $detail_id, $name, $capacity, $total_price, 1, NULL, NULL, 1]);

        //reservation_detailに追加する
        //AutoIncrementから番号を取得
        $sql = "SELECT  AUTO_INCREMENT
        FROM  INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = 'd_mikasa'
        AND   TABLE_NAME   = 'reservation'";
        $stmt = $pdo->query($sql)->fetchAll();
        $id = $stmt;

        //宿泊日数の数だけINSERTする
        for ($i = 0; $i < count($days); $i++) {
            $sql = <<<EOD
        INSERT INTO reservation_detail(reservation_id,date,price)
        VALUES(?,?,?)
        EOD;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id[0]['AUTO_INCREMENT'] - 1, $days[$i], $price]);
        }

        //支払い情報をテーブルに追加する
        $sql = <<<EOD
        INSERT INTO reservation_payment(reservation_id,payment_id)
        VALUES(?,?)
        EOD;
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id[0]['AUTO_INCREMENT'] - 1, $peyment]);

        //追加したユーザーを返す
        $sql = <<<EOD
        SELECT *
         FROM user
        WHERE id = ?
        EOD;
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_auth']]);
        $user_date = $stmt->fetch(PDO::FETCH_ASSOC);
        $return_list['user_mail'] = $user_date['mail'];
        $return_list['total_price'] = $total_price;
        $return_list['stay_total'] = $stay_total;
        return $return_list;
    }
}
