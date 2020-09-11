<?php
class UpdateReservation extends Model
{
    public function Reservation($room_no, $check_in, $check_out, $capacity, $peyment, $price,$name)
    {
        parent::connect();
        for ($i = date('Ymd', strtotime($check_in)); $i < date('Ymd', strtotime($check_out)); $i++) {
            $year = substr($i, 0, 4);
            $month = substr($i, 4, 2);
            $day = substr($i, 6, 2);
            if (checkdate($month, $day, $year)) {
                $days[] = date('Y-m-d', strtotime($i));
            }
        }

        $total_price = intval($capacity) * intval($price);
        echo 'iuhasuhflashufaksdhfa' . '<br>';
        echo $_SESSION['user_id'] . 'user_id'. '<br>';
        echo $room_no  . 'room_no'. '<br>';
        echo $name .'name ' .'<br>';
        echo $capacity . '<br>';
        echo $total_price;
        $sql = <<<EOD
        'INSERT INTO reservation(user_id, room_detail_id, room_detail_name, number, total_price)
        VALUES(?, ?, ?, ?, ?)
        EOD;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$_SESSION['user_id'],$room_no, $name, $capacity, $total_price]);
    }
}
