<?php
class Reservation extends Model
{
    /**
    *プルダウンの内容を取得する
    *
    *プルダウンの表示に必要な情報と、Paymentの情報を取得している
    *
    *@param null
    *@return $resule roomに部屋情報、paymentに支払い情報を格納している
    */
    public function getPullDownList()
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
        $result['room'] = $stmt->fetchAll();

        $sql =
        'SELECT '.
            '* '.
        'FROM '.
            'm_payment';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result['payment'] = $stmt->fetchAll();

        return $result;
    }

    /**
    *支払い情報がデータベース上にあれば取得する
    *
    *peymentをWhereで引っ張ってくる
    *
    *@param $id m_paymentのidカラム
    *@return $idと一致するカラムを返す
    */
    public function getPayment($id)
    {
        parent::connect();
        $sql =
        'SELECT '.
            '* '.
        'FROM '.
            'm_payment '.
        'WHERE '.
            'id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
    *予約状況を確認するための情報を取得する
    *
    *ユーザーが予約しているかどうかをチェックした後、
    *該当期間内に部屋が空いているかチェック。空いてたら
    *
    *@param $id 部屋情報(room_detail)のID
    *@param $check_in チェックイン日
    *@param $check_out チェックアウト日
    *@return 予約状況、またはエラー文
    */
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
                return '満室のため、他の日付か部屋を選択してください';
            }

            //ここまで引っ掛からなかったら、予約が無いと返す
            return 'Not reservation room';

        } catch (PDOException $e) {
            header('Content-Type: text/plain; charset=UTF-8', true, 500);
            exit($e->getMessage());
        }
    }

    /**
    *予約しようとしている部屋の情報を取得する
    *
    *指定したdetail_idと一致する情報をまとめて取得する
    *
    *@param $id 部屋詳細のID(room_detail_id)
    *@return $result 部屋が見つからなかった場合はエラー文を返す
    */

    public function getReservationRoom($id) //選択した部屋の情報を取得
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
     *@param $set_data[detail_id] reservaion_detailのID
     *@param $set_data[check_in] チェックインの日付
     *@param $set_data[check_out] チェックアウトの日付
     *@param $set_data[capacity] 宿泊人数
     *@param $set_data[payment] 支払い方法
     *@param $set_data[price] 請求額
     *@param $set_data[name] 部屋名
     *@param $set_data[room_id] 部屋番号、room_id

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
            $payment = $set_data['payment'];
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
            $stmt->execute([$id[0]['AUTO_INCREMENT'] - 1, $payment]);

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
        return 'Error';
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
