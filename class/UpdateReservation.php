<?php
class UpdateReservation extends Model
{
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
        $stmt->execute([$_SESSION['user_id'], $room_id, $detail_id, $name, $capacity, $total_price, 1, NULL, NULL, 1]);

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
        $stmt->execute([$_SESSION['user_id']]);
        $user_date = $stmt->fetch(PDO::FETCH_ASSOC);
        $return_list['user_mail'] = $user_date['mail'];
        $return_list['total_price'] = $total_price;
        $return_list['stay_total'] = $stay_total;
        return $return_list;
    }
}
