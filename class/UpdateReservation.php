<?php
class UpdateReservation extends Model
{
    public function into_reservation($detail_id, $check_in, $check_out, $capacity, $peyment, $price, $name = NULL, $room_id)
    {
        parent::connect();
        $pdo = $this->dbh;
        for ($i = date('Ymd', strtotime($check_in)); $i < date('Ymd', strtotime($check_out)); $i++) {
            $year = substr($i, 0, 4);
            $month = substr($i, 4, 2);
            $day = substr($i, 6, 2);
            if (checkdate($month, $day, $year)) {
                $days[] = date('Y-m-d H:i:s', strtotime($i));
            }
        }

        $total_price = intval($capacity) * intval($price);

        //reservationに追加する
        $sql = <<<EOD
        INSERT INTO reservation(user_id, room_id, room_detail_id, room_detail_name, number, total_price,status,created_at,updated_at,delete_flg)
        VALUES(?,?,?,?,?,?,?,?,?,?)
        EOD;
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id'],$room_id,$detail_id,$name,$capacity,$total_price,NULL,NULL,NULL,1]);

    //reservation_detailに追加する
    $sql ="SELECT  AUTO_INCREMENT
    FROM  INFORMATION_SCHEMA.TABLES
    WHERE TABLE_SCHEMA = 'd_mikasa'
    AND   TABLE_NAME   = 'reservation'";
    $stmt = $pdo -> query($sql) -> fetchAll();
    $id = $stmt;
    for ($i = 0; $i < count($days); $i++) {
        $sql = <<<EOD
        INSERT INTO reservation_detail(reservation_id,date,price)
        VALUES(?,?,?)
        EOD;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id[0]['AUTO_INCREMENT']-1,$days[$i],$price]);

    }
    }
}
