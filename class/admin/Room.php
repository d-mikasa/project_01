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
     *@return なし
     */

    public function deleteDetail($id)
    {
        //connectメソッドにアクセス
        parent::connect();

        //roomテーブルの中から、idが一致するものを削除する
        $sql = 'DELETE FROM room WHERE id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);

        //room_detailの中からroom_id(roomテーブルのidカラム)が一致するものを削除する
        $sql = 'DELETE FROM room_detail WHERE room_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
    }


    ///////////////////////////////////////////////////////////////////////////
    /**
     *DBから指定IDの部屋情報を取得する機能
     *
     *room_idが引数のidと一致するroom_detailを全て取得する
     *
     *@param $id roomテーブルのid
     *@return $result idが一致するroom_detailを全て
     */

    public function getDetail($id)
    {
        //connectメソッドにアクセス
        parent::connect();

        //room_detailテーブルの中からidが一致するものを取得($resultに格納)
        $sql = 'SELECT * FROM room_detail WHERE room_id = ?';
        $detail = $this->dbh->prepare($sql);
        $detail->execute([$id]);
        $result['detail'] = $detail->fetchAll(PDO::FETCH_ASSOC);

        $sql = 'SELECT name FROM room WHERE id = ?';
        $detail = $this->dbh->prepare($sql);
        $detail->execute([$id]);
        $result['room'] = $detail->fetch(PDO::FETCH_ASSOC);
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
     *@param $room 更新しようとしている部屋の名前
     *@param $mode 編集なのか新規作成なのかのフラグ
     *@return $message 成功したか失敗したかを返す
     */

    public function roomUpdate($id, $set_data, $room = null, $mode)
    {
        /*
        room_detailの初期化処理
        */
        //connectメソッドにアクセス
        parent::connect();

<<<<<<< HEAD
        //room_detailから、引数とroom_idが一致するものを全て削除する
        $sql = 'DELETE FROM room_detail WHERE room_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);

        /*
        モード：新規作成の処理
        */
        if ($mode== 'create') {
            //新規部屋情報の追加
            $sql = 'INSERT INTO room(name) VALUES (?)';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$room]);

            //Auto_incrementの値を取得し、新規追加されたであろうid(room_id)の値を取得
            $sql = <<<EOD
            SELECT  AUTO_INCREMENT
            FROM  INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = 'd_mikasa'
            AND   TABLE_NAME   = 'room'
            EOD;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            $id = $result['AUTO_INCREMENT'] - 1;

            //room_detailの数だけforでINSERTする
            for ($i = 0; $i < count($set_data); $i++) {
                $sql = 'INSERT INTO room_detail(room_id,capacity,remarks,price) VALUES (?,?,?,?)';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute([$id, $set_data[$i]['capacity'], $set_data[$i]['remarks'], $set_data[$i]['price']]);
=======
        //空値にNULLを代入してあげないと、エラーが返ってきたので記載
        for ($i = 0; $i < count($set_data); $i++) {
            if ($set_data[$i]['capacity'] == '') {
                $set_data[$i]['capacity'] = null;
            }
            if ($set_data[$i]['remarks'] == '') {
                $set_data[$i]['remarks'] = null;
            }
            if ($set_data[$i]['price'] == '') {
                $set_data[$i]['price'] = null;
>>>>>>> 7e0f8fb447c1bb4ffd818e4e5da6b1d2a3f867d1
            }
        }

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
                $stmt->execute([$room]);

                //Auto_incrementの値を取得し、新規追加されたであろうid(room_id)の値を取得
                $sql = 'SELECT AUTO_INCREMENT';
                $sql .= ' FROM INFORMATION_SCHEMA.TABLES';
                $sql .= ' WHERE TABLE_SCHEMA = \'d_mikasa\'';
                $sql .= ' AND TABLE_NAME = \'room\'';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetch();
                $id = $result['AUTO_INCREMENT'] - 1;

                //room_detailの数だけforでINSERTする
                for ($i = 0; $i < count($set_data); $i++) {
                    $sql = 'INSERT INTO room_detail(room_id,capacity,remarks,price) VALUES (?,?,?,?)';
                    $stmt = $this->dbh->prepare($sql);
                    $stmt->execute([$id, $set_data[$i]['capacity'], $set_data[$i]['remarks'], $set_data[$i]['price']]);
                }
                //成功した場合はメッセージにその旨を代入
                $message = '内容を新規作成しました';
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
                $stmt->execute([$room, $id]);

                //room_detailの数だけforでINSERTする
                for ($i = 0; $i < count($set_data); $i++) {
                    $sql = 'INSERT INTO room_detail(room_id, capacity, remarks, price) VALUES (?,?,?,?)';
                    $stmt = $this->dbh->prepare($sql);
                    $stmt->execute([$id, $set_data[$i]['capacity'], $set_data[$i]['remarks'], $set_data[$i]['price']]);
                }
                //成功した場合はメッセージにその旨を代入
                $message = '内容の更新に成功しました';
            }
            } catch (PDOException $e) {//PDOエラーの場合
                //処理をロールバック
                $this->dbh->rollback();
                $message = '処理に失敗しました';
                return $message;
            }
          //コミットして代入されたメッセージを送信
        $this->dbh->commit();
        return $message;
    }


    ///////////////////////////////////////////////////////////////////////////
    /**
     *roomテーブルの情報を全て取得する
     *
     *@param null
     *@return roomテーブルの全カラム
     */

    public function getRoomAll()
    {
        //connectメソッドにアクセス
        parent::connect();

        //roomテーブルの情報を取得する
        $sql = 'SELECT * FROM room';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }


    /**
     *リストに表示する部屋をソートする
     *
     *何の値をソートするのかを受け取り、GETにある昇順・降順かを読み取る。
     *
     *@param $sort ソートが昇順か降順かを判定する。
     *@param $col 何の値をソートしようとしているのかを判別する。
     *@return $result 並び替え後の配列(roomテーブル)
     */

    public function sortRoom($sort, $col)
    {
        parent::connect();

        //roomテーブルの情報を取得する
        switch ($sort) {
            case 'desc': //降順（大きいもん順）
                $sql = 'SELECT * FROM room ORDER BY ' . $col . ' DESC';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll();
                return $result;
                break;

            default: //昇順（小さいもん順）
                $sql = 'SELECT * FROM room ORDER BY ' . $col . '  IS NULL ASC, ' . $col . ' ASC';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll();
                return $result;
                break;
        }
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
            $this->console_log($e);
            $error = 'ファイルのアップロードに失敗しました';
            return $error;
        } catch (Exception $e) { // ファイル送信時のエラー
            // 処理の巻き戻し
            $this->dbh->rollback();
            $this->console_log($e);
            $error = 'ファイルのアップロードに失敗しました';
            return $error;
        }
        $this->dbh->commit();
        $error = 'ファイルのアップロードに成功しました';
        return $error;
    }



    //コンソールログに表示する用のメソッド
    function console_log($data)
    {
        echo '<script>';
        echo 'console.log(' . json_encode($data) . ')';
        echo '</script>';
    }
}
