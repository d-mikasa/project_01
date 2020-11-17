<?php

///////////////////////////////////////////////////
//Adminユーザーが部屋を操作する系の処理
//////////////////////////////////////////////////
class Room extends Model
{

	//画像保存先 本番環境
	const FULL_PATH = '/var/www/html/training/cicacu-mikasa/img';
	//最大表示領域
	const MAX_VIEW = 3;
	//画像保存先 ローカル環境
	const IMGS_PATH = '../img/';


	///////////////////////////////////////////////////////////////////////////
	/**
	 *DBから指定IDの部屋情報を削除する
	 *
	 *roomとroom_detailの中からroom_id(id)が引数と一致するものを削除する
	 *
	 *@param $id roomテーブルのid
	 *@return $message 処理が成功したかどうかのメッセージ
	 */

	public function deleteRoom($id)
	{
		//connectメソッドにアクセス
		parent::connect();
		try {
			//トランザクション開始
			$this->dbh->beginTransaction();

			$sql = 'UPDATE room SET delete_flg = TRUE WHERE id = ?';
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute([$id]);

            //SQLにて操作されたカラム数を取得する
			$count = $stmt->rowCount();

			//room_detailの中からroom_id(roomテーブルのidカラム)が一致するものを削除する
			$sql = 'DELETE FROM room_detail WHERE room_id = ?';
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute([$id]);
		} catch (PDOException $e) {
			//ロールバック処理
			$this->dbh->rollback();
		}

		//成功した場合はメッセージにその旨を代入
		$this->dbh->commit();

		if($count ==1){
			$message = '削除に成功しました';
		}else{
			$message = '削除に失敗しました';
		}
		return $message;
	}


	///////////////////////////////////////////////////////////////////////////
	/**
	 *DBから指定IDの部屋情報を取得する機能
	 *
	 *room_idが引数のidと一致するroom_detailを全て取得する
	 *
	 *@param $id roomテーブルのid
	 *@return $result idが一致するroomの名前とroom_detailを全て
	 */

	public function getRoom($id)
	{
		//connectメソッドにアクセス
		parent::connect();

		//room_detailテーブルの中からidが一致するものを取得($resultに格納)
		$sql = 'SELECT * FROM room_detail WHERE room_id = ?';
		$detail = $this->dbh->prepare($sql);
		$detail->execute([$id]);
		$result['detail'] = $detail->fetchAll(PDO::FETCH_ASSOC);

		$sql = 'SELECT name FROM room WHERE id = ? AND delete_flg = FALSE';
		$detail = $this->dbh->prepare($sql);
		$detail->execute([$id]);
		$result['room'] = $detail->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

	public function getEditRoom($id)
	{
		//connectメソッドにアクセス
		parent::connect();
		$sql = 'SELECT name FROM room WHERE id = ? AND delete_flg = FALSE';
		$detail = $this->dbh->prepare($sql);
		$detail->execute([$id]);
		$result['name'] = $detail->fetch(PDO::FETCH_COLUMN);


		$sql = 'SELECT capacity,price,remarks FROM room_detail WHERE room_id = ?';
		$detail = $this->dbh->prepare($sql);
		$detail->execute([$id]);
		$result['detail'] = $detail->fetchAll(PDO::FETCH_ASSOC);


		return $result;
	}


	///////////////////////////////////////////////////////////////////////////
	/**
	 *部屋の情報を更新（追加）する機能
	 *
	 *新規作成か更新かで処理を変える。カラムを減らした時に対応できる様、どちらの場合でもidと一致するデータを削除する。
	 *[新規作成の場合]roomテーブルに部屋を追加後、追加したidを取得しroom_detailに情報を追加
	 *[更新の場合]引数になっているidと一致するroom_detailの内容を作成する。
	 *
	 *@param $id 検索するroomのid
	 *@param $set_data 追加する部屋の詳細情報を多次元配列に格納している
	 *@param $mode 編集なのか新規作成なのかのフラグ
	 *@return $message 成功したか失敗したかを返す
	 */

	public function updateRoom($id, $set_data, $mode)
	{
		/*
        room_detailの初期化処理
        */
		//connectメソッドにアクセス
        parent::connect();

		$name = $set_data['name'];
		$detail = $set_data['detail'];

		try {
			//トランザクション開始
			$this->dbh->beginTransaction();

			//room_detailから、引数とroom_idが一致するものを全て削除する
			$sql = 'DELETE FROM room_detail WHERE room_id = ?';
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute([$id]);

			/*--------モード：新規作成の処理--------*/
			if ($mode == 'create') {

				//新規部屋情報の追加
				$sql = 'INSERT INTO room(name) VALUES (?)';
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindValue(1, $name == '' ? NULL : $name, ($name == '') ? PDO::PARAM_NULL : PDO::PARAM_STR);

				$stmt->execute();

				//Auto_incrementの値を取得し、新規追加されたであろうid(room_id)の値を取得
				$sql =
					'SELECT AUTO_INCREMENT'
					. ' FROM INFORMATION_SCHEMA.TABLES'
					. ' WHERE TABLE_SCHEMA = \'d_mikasa\''
					. ' AND TABLE_NAME = \'room\'';
				$stmt = $this->dbh->prepare($sql);
				$stmt->execute();
				$result = $stmt->fetch();
				$id = $result['AUTO_INCREMENT'] - 1;

				//room_detailの数だけforでINSERTする
				for ($i = 0; $i < count($detail); $i++) {
					$sql = 'INSERT INTO room_detail(room_id,capacity,remarks,price) VALUES (?,?,?,?)';
					$stmt = $this->dbh->prepare($sql);

					$stmt->bindValue(1, $id == '' ? NULL : $id, ($id == '') ? PDO::PARAM_NULL : PDO::PARAM_INT);
					$stmt->bindValue(2, $detail[$i]['capacity'] == '' ? NULL : $detail[$i]['capacity'], ($detail[$i]['capacity'] == '') ? PDO::PARAM_NULL : PDO::PARAM_INT);
					$stmt->bindValue(3, $detail[$i]['remarks'] == '' ? NULL : $detail[$i]['remarks'], ($detail[$i]['remarks'] == '') ? PDO::PARAM_NULL : PDO::PARAM_STR);
					$stmt->bindValue(4, $detail[$i]['price'] == '' ? NULL : $detail[$i]['price'], ($detail[$i]['price'] == '') ? PDO::PARAM_NULL : PDO::PARAM_INT);

					$stmt->execute();
				}

				//成功した場合はメッセージにその旨を代入
				$this->dbh->commit();
				return '新規作成に成功しました。';
			}

			/*--------モード：編集の処理--------*/
			if ($mode == 'edit') {

				//roomテーブルの更新日を現在の日付に上書き
				$sql = 'UPDATE room SET updated_at = CURRENT_TIMESTAMP(6) WHERE id = ?';
				$stmt = $this->dbh->prepare($sql);
				$stmt->execute([$id]);

				//ルーム名の更新
				$sql = 'UPDATE room SET name = ? WHERE id = ?';
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindValue(1, $name == '' ? NULL : $name, ($name == '') ? PDO::PARAM_NULL : PDO::PARAM_STR);
				$stmt->bindValue(2, $id, PDO::PARAM_INT);
				$stmt->execute();

				//room_detailの数だけforでINSERTする
				for ($i = 0; $i < count($detail); $i++) {
					$sql = 'INSERT INTO room_detail(room_id, capacity, remarks, price) VALUES (?,?,?,?)';
					$stmt = $this->dbh->prepare($sql);

					$stmt->bindValue(1, $id == '' ? NULL : $id, ($id == '') ? PDO::PARAM_NULL : PDO::PARAM_INT);
					$stmt->bindValue(2, $detail[$i]['capacity'] == '' ? NULL : $detail[$i]['capacity'], ($detail[$i]['capacity'] == '') ? PDO::PARAM_NULL : PDO::PARAM_INT);
					$stmt->bindValue(3, $detail[$i]['remarks'] == '' ? NULL : $detail[$i]['remarks'], ($detail[$i]['remarks'] == '') ? PDO::PARAM_NULL : PDO::PARAM_STR);
					$stmt->bindValue(4, $detail[$i]['price'] == '' ? NULL : $detail[$i]['price'], ($detail[$i]['price'] == '') ? PDO::PARAM_NULL : PDO::PARAM_INT);

					$stmt->execute();
				}

				//成功した場合はメッセージにその旨を代入
				$this->dbh->commit();
				return '更新に成功しました';
			}
		} catch (PDOException $e) { //PDOエラーの場合
			//処理をロールバック
			$this->dbh->rollback();
			return 'サーバーとの接続に失敗しました。';
		}
	}


	///////////////////////////////////////////////////////////////////////////
	/**
	 *roomの情報をソートして表示する
	 *
	 *@param null
	 *@return roomテーブルの全カラム
	 */

	/**
	 *リストに表示する部屋をソートする
	 *
	 *何の値をソートするのかを受け取り、GETにある昇順・降順かを読み取る。
	 *
	 *@param $sort ソートが昇順か降順かを判定する。
	 *@param $col 何の値をソートしようとしているのかを判別する。
	 *@return $result 並び替え後の配列(roomテーブル)
	 */

	public function sortRoomList($sort , $col)
	{
		parent::connect();

		$sql =
			'SELECT ' .
                '* ' .
			'FROM ' .
                'room ' .
			'WHERE ' .
                'delete_flg = FALSE ' .
            'ORDER BY ' .
            'CASE ' .
                'WHEN ' . $col . ' IS NULL ' .
                    'THEN "2" ' .
                'WHEN ' . $col . ' = \'\' ' .
                    'THEN \'1\' ' .
            'ELSE \' 0\' ' .
			'END, ' .
			$col . ' ' . $sort;

		$stmt = $this->dbh->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}


	///////////////////////////////////////////////////////////////////////////
	/**
	 *画像のアップデート（roomのimgカラムにパスを追加、画像のアップロード）を行う
	 *
	 *このクラスではroomテーブルのimgカラムのパスを追加することのみ行う
	 *
	 *@param $img 画像の名前
	 *@param $id roomテーブルのid(room_id)のこと
	 *@return $message 成功か失敗かのメッセージを返す
	 */

	public function updateRoomImg($id)
	{
		//connectメソッドにアクセス
		parent::connect();

		try {
			//トランザクション開始
			$this->dbh->beginTransaction();

			// 権限変更
			exec('sudo chmod 777 ' . self::FULL_PATH);

			//FILESがアップロード成功していた場合の処理
			if ($_FILES['room_img']['error'] == UPLOAD_ERR_OK) {
				$name = $_FILES['room_img']['name'];
				$name = mb_convert_encoding($name, 'cp932', 'utf8');
				$temp = $_FILES['room_img']['tmp_name'];
				$result = move_uploaded_file($temp, self::FULL_PATH . $name);

				if ($result == true) {
					//データベースのやりとり
					//roomテーブルのimgに画像名（拡張子付き）をUPDATE
					$sql = 'UPDATE room SET img = ? ,updated_at = CURRENT_TIMESTAMP(6) WHERE id = ?';
					$stmt = $this->dbh->prepare($sql);
					$stmt->execute([$name, $id]);
				} else {
					//move_uploaded_fileが失敗していた（ファイル移動に失敗）場合
					throw new Exception('ファイルの移動に失敗しました');
				}
			} elseif ($_FILES['room_img']['error'] == UPLOAD_ERR_NO_FILE) {
				throw new Exception('ファイルがアップロードされませんでした');
			} else {
				//$_FILES['room_img']['error'] によくわからない値が入ってしまっていた場合
				throw new Exception('なぜか失敗しました');
			}

			// 元の状態に戻す
			exec('sudo chmod 755 ' . self::FULL_PATH);
		} catch (PDOException $e) { //DBの接続に失敗した場合
			$this->dbh->rollback();
			return 'ファイルのアップロードに失敗しました';
		} catch (Exception $e) { // ファイル送信時のエラー
			// 処理の巻き戻し
			$this->dbh->rollback();
			return 'ファイルのアップロードに失敗しました';
		}
		$this->dbh->commit();

		return  'ファイルのアップロードに成功しました';
    }

////////////////////////////////課題４対応//////////////////////////////////////////////////////
    function getDaysList()
    {
        parent::connect();
        $sql =
        'SELECT '.
        'DISTINCT '.
        'LEFT(date,7) AS date '.
        'FROM '.
        'calendar '.
        'WHERE '.
        'date <= date_format(now() + INTERVAL 3 MONTH, \'%y-%m-%d\') ';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getRsvInfo($detail_id,$date)
    {
        //connectメソッドにアクセス
        parent::connect();
        $sql =
        'SELECT ' .
            'reservation.id, ' .
            'reservation.room_detail_id, ' .
            'reservation_detail.reservation_id, ' .
            'reservation.user_id, ' .
            'reservation_detail.date ' .
        'FROM ' .
            'reservation ' .
                'INNER JOIN reservation_detail '.
                    'ON reservation.id = reservation_detail.reservation_id ' .
        'WHERE ' .
                ' reservation_detail.date Like \'' . $date . '%\' '.
                ' AND reservation.room_detail_id = :detail_id ' ;

                $stmt = $this->dbh->prepare($sql);
                $stmt->bindValue(':detail_id', $detail_id, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result;
    }


    /*
    課題４はここから下
    */
    function getReservationState($id,$date)
    {
        parent::connect();
        $sql =
        'SELECT '.
            'calendar.date, '.
            'rsv.room_detail_name, '.
            'rsv.name, '.
            'rsv.number, '.
            'rsv.total_price '.
            'FROM '.
            'calendar '.
        'LEFT JOIN'.
            '( '.
                'SELECT '.
                    'reservation.room_detail_id, '.
                    'reservation.room_detail_name, '.
                    'reservation.total_price, '.
                    'reservation.number, '.
                    'user.name, '.
                    'reservation_detail.date '.
                    'FROM '.
                    'reservation '.
                'INNER JOIN '.
                    'reservation_detail '.
                        'ON reservation.id = reservation_detail.reservation_id '.
                'INNER JOIN '.
                    'user '.
                        'ON reservation.user_id = user.id '.
                'WHERE '.
                    'reservation.room_detail_id = ? '.
            ') AS rsv '.
                'ON rsv.date = calendar.date '.
        'WHERE '.
            'calendar.date >= ? '.
            'AND calendar.date < ? + INTERVAL 1 MONTH ORDER BY calendar.date ';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id,$date,$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    function export($data)
    {
        try {
            //CSV形式で情報をファイルに出力のための準備
            $csvFileName = '/tmp/' . time() . rand() . '.csv';
            $fileName = '予約状況' . '.csv';
            $res = fopen($csvFileName, 'w');
            if ($res === FALSE) {
                throw new Exception('ファイルの書き込みに失敗しました。');
            }

            // 項目名先に出力
            $header = ['date', 'room_name', 'name', 'number', 'price'];
            fputcsv($res, $header);

            // ループしながら出力
            foreach($data as $dataInfo) {
                // 文字コード変換。エクセルで開けるようにする
                mb_convert_variables('SJIS', 'UTF-8', $dataInfo);

                // ファイルに書き出しをする
                fputcsv($res, $dataInfo);
            }

            // ファイルを閉じる
            fclose($res);

            // ファイルタイプ（csv）
            header('Content-Type: application/octet-stream');

            // ファイル名
            header('Content-Disposition: attachment; filename=' . $fileName);
            // ファイルのサイズ　ダウンロードの進捗状況が表示
            header('Content-Length: ' . filesize($csvFileName));
            header('Content-Transfer-Encoding: binary');
            // ファイルを出力する
            readfile($csvFileName);
        } catch(Exception $e) {
            // 例外処理をここに書きます
            echo $e->getMessage();
            exit();
        }
    }
}
