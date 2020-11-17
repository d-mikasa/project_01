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
                    'ON room_detail.room_id = room.id '
        ;

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result['room'] = $stmt->fetchAll();

        $sql =
            'SELECT '.
                'id, '.
                'name '.
            'FROM '.
                'm_payment'
        ;

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result['payment'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

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
                'id = ?'
        ;

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        $return = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->checkNumeric($id);
        if($id != $return['id']){
            header('Location: reservation_error.php');
            exit();
        }
        return $return;
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
        parent::connect();
        /*
        Userが予約している日があるかどうか
        */
        $sql =
            'SELECT ' .
                'reservation.id, ' .
                'reservation_detail.reservation_id, ' .
                'GROUP_CONCAT(distinct(reservation.user_id)) AS user_id, ' .
                'GROUP_CONCAT(reservation_detail.date) AS date ' .
            'FROM ' .
                'reservation ' .
                    'INNER JOIN reservation_detail '.
                        'ON reservation.id = reservation_detail.reservation_id ' .
            'WHERE ' .
                ' reservation.user_id = ? '.
                'AND reservation_detail.date >= ? ' .
                'AND reservation_detail.date <= ? ' .
            'GROUP BY  reservation.user_id; '
        ;

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$_SESSION['user_id'] , $check_in, $check_out]);
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
                'GROUP_CONCAT(distinct(reservation.user_id)) AS user_id, ' .
                'reservation.room_id, ' .
                'reservation.room_detail_id, ' .
                'reservation.room_detail_name, ' .
                'GROUP_CONCAT(reservation_detail.date) AS date ' .
            'FROM ' .
                'reservation ' .
                    'INNER JOIN reservation_detail '.
                        'ON reservation.id = reservation_detail.reservation_id ' .
            'WHERE ' .
                'reservation.room_detail_id = ? ' .
                'AND reservation_detail.date >= ? ' .
                'AND reservation_detail.date <= ? ' .
            'GROUP BY  reservation.user_id; '
        ;

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id, $check_in, $check_out]);
        $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

        //レコードが無いならリターンで返す
        if(!empty($result)){
            return '満室のため、他の日付か部屋を選択してください';
        }

        //ここまで引っ掛からなかったら、予約が無いと返す
        return true;
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
        parent::connect();
        $this->checkNumeric($id);
        $sql =
            'SELECT '.
                '* '.
            'FROM '.
                'room_detail '.
            'WHERE '.
                'id = ? '
        ;

        $stmt =$this->dbh->prepare($sql);
        $stmt->execute([$id]);
        $check = $stmt->fetch(PDO::FETCH_ASSOC);

        if(empty($check)){
            header('Location: reservation_error.php');
            exit();
        }

        $sql =
            'SELECT '.
                'room.id, '.
                'room.name, '.
                'room_detail.id AS detail_id, '.
                'room_detail.capacity, '.
                'room_detail.remarks, '.
                'room_detail.price, '.
                'room_detail.name AS detail_name '.
            ' FROM '.
                'room '.
            'INNER JOIN '.
                'room_detail '.
                    'ON room.id = room_detail.room_id '.
            'WHERE room_detail.id = ?'
        ;

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if(empty($result)){
            return false;
        }
        return $result;
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

     *@return $return_list メールを送信するための情報群
     */
    public function updateReservation($set_data)
    {
        parent::connect();
        $pdo = $this->dbh;
        $room_info = $this->getReservationRoom($set_data['detail_id']);
        try {
            //トランザクション開始
			$this->dbh->beginTransaction();

            //変数へ落とし込む
            $detail_id = $set_data['detail_id'];
            $check_in = $set_data['check_in'];
            $check_out = $set_data['check_out'];
            $capacity = $set_data['capacity'];
            $payment = $set_data['payment'];

            $return_list['room_id'] = $room_info['id'];
            $return_list['name']= $room_info['name'];
            $return_list['price']= $room_info['price'];

            //宿泊数をカウント
            //宿泊日数を定義 $total_stay
            $check_out_temp = new DateTime($_POST['check_out']);
            $check_in_temp  = new DateTime($_POST['check_in']);
            $diff = $check_in_temp->diff($check_out_temp);
            $return_list['total_stay'] = $diff->days;

            //合計金額=料金＊宿泊日数
            $return_list['total_price'] = intval($return_list['price']) * $return_list['total_stay'];

            //reservationに追加する
            $sql =
                'INSERT INTO '.
                'reservation'.
                '( '.
                    'user_id, '.
                    'room_id, '.
                    'room_detail_id, '.
                    'room_detail_name, '.
                    'number, '.
                    'total_price, '.
                    'status, '.
                    'created_at, '.
                    'updated_at, '.
                    'delete_flg'.
                ') '.
                'VALUES'.
                '('.
                    '?, '.
                    '?, '.
                    '?, '.
                    '?, '.
                    '?, '.
                    '?, '.
                    '?, '.
                    '?, '.
                    '?, '.
                    '? '.
                '); '
            ;

            $stmt = $pdo->prepare($sql);
            $temp = array(
                $_SESSION['user_id'],
                $return_list['room_id'],
                $detail_id,
                $return_list['name'],
                $capacity,
                $return_list['total_price'],
                1,
                NULL,
                NULL,
                1
            );
            $stmt->execute($temp);

            //reservation_detailに追加する
            //AutoIncrementから番号を取得
            $sql =
                'SELECT '.
                    'AUTO_INCREMENT '.
                'FROM '.
                    'INFORMATION_SCHEMA.TABLES '.
                'WHERE '.
                    'TABLE_SCHEMA = \'d_mikasa\' '.
                    'AND TABLE_NAME = \'reservation\' '
            ;

            $temp = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
            $return_list['reservation_id'] = $temp['AUTO_INCREMENT'] - 1;

            //宿泊日数の数だけINSERTする
            for ($i = $check_in; $i < $check_out; $i = date('Y-m-d', strtotime($i . '+1 day'))){
                $sql =
                    'INSERT INTO '.
                    'reservation_detail'.
                    '( '.
                        'reservation_id, '.
                        'date, '.
                        'price'.
                    ') '.
                    'VALUES'.
                    '('.
                        '?, '.
                        '?, '.
                        '? '.
                '); '
                ;

                $stmt = $pdo->prepare($sql);
                $stmt->execute([$return_list['reservation_id'], $i, $return_list['price']]);
            }

            //支払い情報をテーブルに追加する
            $sql =
                'INSERT INTO '.
                'reservation_payment'.
                '( '.
                    'reservation_id, '.
                    'payment_id
                ) '.
                'VALUES'.
                '('.
                    '?, '.
                    '? '.
                '); '
            ;

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$return_list['reservation_id'], $payment]);

            //追加したユーザーを返す
            $sql =
                'SELECT '.
                    '* '.
                'FROM '.
                    'user '.
                'WHERE '.
                    'id = ? '
            ;

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_SESSION['user_id']]);
            $user_info = $stmt->fetch(PDO::FETCH_ASSOC);

            //返す変数をまとめる
            $return_list['user_mail'] = $user_info['mail'];
            $return_list['user_name'] = $user_info['name'];
            $this->dbh->commit();

            return $return_list;

        } catch (PDOException $e){
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

    //dateの型をチェックする
    function validateDateFormat($value)
    {
        if(preg_match('/\A\d{4}-\d{1,2}-\d{1,2}\z/', $value) === false){
            header('Location: reservation_error.php');
            exit();
        }

        list($year, $month, $day) = explode('-', $value);

        if(checkdate($month, $day, $year) === false){
            header('Location: reservation_error.php');
            exit();
        }
    }

    //数値が正しいかどうかをチェックする（整数、１以上）
    function checkNumeric($value){
        $options = ['options' => ['min_range' => 1]];
        if(is_int(filter_var($value, \FILTER_VALIDATE_INT, $options)) === false){
            header('Location: reservation_error.php');
            exit();
        }
    }
}
