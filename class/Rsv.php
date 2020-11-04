<?php
class Rsv extends Model
{
    public function getPullDownList() //プルダウンリストに表示する部屋を検索する
    {
        parent::connect();
        $sql =
        'SELECT '.
            'room_detail.id, '.
            'room.name, '.
            'room_detail.capacity, '.
            'room_detail.price, '.
            'room_detail.remarks '.
        'FROM '.
            'room_detail '.
        'JOIN '.
            'room '.
                'ON room_detail.room_id = room.id ';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (empty($result)) {
            return '部屋が存在しません。';
        }
        return $result;
    }



    public function checkReservation($id, $check_in, $check_out) //選択した部屋の予約状況をチェックする
    {
        try {
            parent::connect();
            /*
            Userが予約している日があるかどうか
            */
            $sql =
            'SELECT ' .
                'reservation.id, ' .
                'reservation_detail.reservation_id, ' .
                'GROUP_CONCAT(distinct(reservation.user_id)) as \'user_id\', ' .
                'GROUP_CONCAT(reservation_detail.date) AS \'date\' ' .
            'FROM ' .
                'reservation ' .
                    'INNER JOIN reservation_detail '.
                        'ON reservation.id = reservation_detail.reservation_id ' .
            'WHERE ' .
                    ' reservation.user_id = ? '.
                    'AND reservation_detail.date >=? ' .
                    'AND reservation_detail.date <=? ' .
            'GROUP BY  reservation.user_id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$_SESSION['user_auth'] , $check_in, $check_out]);
            $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

            //レコードが無いならリターンで返す
            if(!empty($result)){
                return 'お客様はすでに該当日に予約されています';
            }

            /*
            該当の部屋が予約されているかどうか
            */
                $sql =
                'SELECT ' .
                    'reservation.id, ' .
                    'GROUP_CONCAT(distinct(reservation.user_id)) as \'user_id\', ' .
                    'reservation.room_id, ' .
                    'reservation.room_detail_id, ' .
                    'reservation.room_detail_name, ' .
                    'GROUP_CONCAT(reservation_detail.date) AS \'date\' ' .
                'FROM ' .
                    'reservation ' .
                        'INNER JOIN reservation_detail '.
                            'ON reservation.id = reservation_detail.reservation_id ' .
                'WHERE ' .
                    'reservation.room_detail_id = ? ' .
                        'AND reservation_detail.date >=? ' .
                        'AND reservation_detail.date <=? ' .
                'GROUP BY  reservation.user_id; ';

                $stmt = $this->dbh->prepare($sql);
                $stmt->execute([$id, $check_in, $check_out]);
                $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

                //レコードが無いならリターンで返す
                if (!empty($result)){
                    return '満室のため、他の日付か部屋を選択してください。';
                }

            //ここまで引っ掛からなかったら、予約が無いと返す
            return 'Not reservation room';

        } catch (PDOException $e) {
            header('Content-Type: text/plain; charset=UTF-8', true, 500);
            exit($e->getMessage());
        }
    }


    public function rsvGetRoom($id) //選択した部屋の情報を取得
    {
        try {
            parent::connect();
            $sql =
            'SELECT '.
                'room.id, '.
                'room.name, '.
                'room_detail.id AS "detail_id", '.
                'room_detail.capacity, '.
                'room_detail.remarks, '.
                'room_detail.price, '.
                'room_detail.name AS "detail_name" '.
            ' FROM '.
                'room '.
            'INNER JOIN '.
                'room_detail '.
                    'ON room.id = room_detail.room_id '.
            'WHERE room_detail.id = ? ';

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
    public function updateReservation($set_data)
    {

        parent::connect();
        $pdo = $this->dbh;

        try {
            //トランザクション開始
			$this->dbh->beginTransaction();

        //変数へ落とし込む
        $detail_id = $set_data['detail_id'];
        $check_in = $set_data['check_in'];
        $check_out = $set_data['check_out'];
        $capacity = $set_data['capacity'];
        $peyment = $set_data['peyment'];
        $room_id = $set_data['room_id'];
        $name = $set_data['name'];
        $price = $set_data['price'];

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
        $total_stay = count($days);

        //合計金額=宿泊人数＊料金＊宿泊日数
        $total_price = intval($capacity) * intval($price) * $total_stay;

        //reservationに追加する
        $sql =
        'INSERT INTO '.
            'reservation( '.
                'user_id, '.
                'room_id, '.
                'room_detail_id, '.
                'room_detail_name, '.
                'number, '.
                'total_price, '.
                'status, '.
                'created_at, '.
                'updated_at, '.
                'delete_flg) '.
        'VALUES(?,?,?,?,?,?,?,?,?,?); ';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_auth'], $room_id, $detail_id, $name, $capacity, $total_price, 1, NULL, NULL, 1]);

        //reservation_detailに追加する
        //AutoIncrementから番号を取得
        $sql =
        'SELECT '.
            'AUTO_INCREMENT '.
        'FROM '.
            'INFORMATION_SCHEMA.TABLES '.
        'WHERE '.
            'TABLE_SCHEMA = \'d_mikasa\' '.
                'AND TABLE_NAME   = \'reservation\' ';
        $stmt = $pdo->query($sql)->fetchAll();
        $id = $stmt;

        //宿泊日数の数だけINSERTする
        for ($i = 0; $i < count($days); $i++) {
            $sql =
            'INSERT INTO '.
                'reservation_detail( '.
                'reservation_id, '.
                'date, '.
                'price) '.
            'VALUES(?,?,?); ';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id[0]['AUTO_INCREMENT'] - 1, $days[$i], $price]);
        }

        //支払い情報をテーブルに追加する
        $sql =
        'INSERT INTO '.
            'reservation_payment( '.
            'reservation_id, '.
            'payment_id) '.
        'VALUES(?,?); ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id[0]['AUTO_INCREMENT'] - 1, $peyment]);

        //追加したユーザーを返す
        $sql =
        'SELECT '.
            '* '.
        'FROM '.
            'user '.
        'WHERE '.
            'id = ? ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_auth']]);
        $user_name = $stmt->fetch(PDO::FETCH_ASSOC);

        //返す変数をまとめる
        $return_list['user_name'] = $user_name['mail'];
        $return_list['total_price'] = $total_price;
        $return_list['total_stay'] = $total_stay;
        $this->dbh->commit();
        return $return_list;

        } catch (PDOException $e) {
		//ロールバック処理
        $this->dbh->rollback();
        return $e;
      }

    }



    public function getToken()
    {
        //トークンの生成
        $toke_byte = openssl_random_pseudo_bytes(16);
        $csrf_token = bin2hex($toke_byte);
        // 生成したトークンをセッションに保存します
        $_SESSION['csrf_token'] = $csrf_token;
        return $csrf_token;
    }
}
