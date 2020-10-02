<?php

///////////////////////////////////////////////////
//Adminユーザーが部屋を操作する系の処理
//////////////////////////////////////////////////

class AdminRoom extends Model
{
    ///////////////////////////////////////////////////////////////////////////
    /**
     *DBから指定IDの部屋情報を削除する
     *
     *roomとroom_detailの中からroom_id(id)が引数と一致するものを削除する
     *
     *@param $id roomテーブルのid
     *@return なし
     */

    public function delete_detail($id)
    {
        //connectメソッドにアクセス
        parent::connect();

        //PDOを取得
        $pdo = $this->dbh;

        //roomテーブルの中から、idが一致するものを削除する
        $sql = 'DELETE FROM room WHERE id = ' . $id;
        $pdo->query($sql);

        //room_detailの中からroom_id(roomテーブルのidカラム)が一致するものを削除する
        $sql = 'DELETE FROM room_detail WHERE room_id = ' . $id;
        $pdo->query($sql);
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

    public function get_detail($id)
    {
        //connectメソッドにアクセス
        parent::connect();

        //PDOを取得
        $pdo = $this->dbh;

        //room_detailテーブルの中からidが一致するものを取得($resultに格納)
        $sql = 'SELECT * FROM room_detail WHERE room_id = ?';
        $detail = $pdo->prepare($sql);
        $detail->execute([$id]);
        $result = $detail->fetchAll();
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
     *@return null
     */

    public function room_update($id, $set_data, $room = NULL)
    {
        /*
        room_detailの初期化処理
        */
        //connectメソッドにアクセス
        parent::connect();

        //PDOを取得
        $pdo = $this->dbh;

        //room_detailから、引数とroom_idが一致するものを全て削除する
        $sql = 'DELETE FROM room_detail WHERE room_id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        /*
        モード：新規作成の処理
        */
        if ($_SESSION['mode'] == 'create') {
            //新規部屋情報の追加
            $sql = 'INSERT INTO room(name) VALUES (?)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$room]);

            //Auto_incrementの値を取得し、新規追加されたであろうid(room_id)の値を取得
            $sql = "SELECT  AUTO_INCREMENT
            FROM  INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = 'd_mikasa'
            AND   TABLE_NAME   = 'room'";
            $stmt = $pdo->query($sql)->fetch();
            $id = $stmt['AUTO_INCREMENT'] - 1;

            //room_detailの数だけforでINSERTする
            for ($i = 0; $i < count($set_data); $i++) {
                $sql = 'INSERT INTO room_detail(room_id,capacity,remarks,price) VALUES (?,?,?,?)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id, $set_data[$i]['capacity'], $set_data[$i]['remarks'], $set_data[$i]['price']]);
            }
        }

        /*
        モード：編集の処理
        */
        if ($_SESSION['mode'] == 'edit') {

            //roomテーブルの更新日を現在の日付に上書き
            $sql = 'UPDATE room SET updated_at = CURRENT_TIMESTAMP(6) WHERE id = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            //room_detailの数だけforでINSERTする
            for ($i = 0; $i < count($set_data); $i++) {
                $sql = 'INSERT INTO room_detail (room_id, capacity, remarks, price) VALUES (?,?,?,?)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id, $set_data[$i]['capacity'], $set_data[$i]['remarks'], $set_data[$i]['price']]);
            }
        }
    }


    ///////////////////////////////////////////////////////////////////////////
    /**
     *roomテーブルの情報を全て取得する
     *
     *@param null
     *@return roomテーブルの全カラム
     */

    public function get_room_all()
    {
        //connectメソッドにアクセス
        parent::connect();

        //PDOを取得
        $pdo = $this->dbh;

        //roomテーブルの情報を取得する
        $sql = 'SELECT * FROM room';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }


    ///////////////////////////////////////////////////////////////////////////
    /**
     *画像のアップデート（roomのimgカラムにパスを追加、画像のアップロード）を行う
     *
     *このクラスではroomテーブルのimgカラムのパスを追加することのみ行う
     *
     *@param $img 画像の名前
     *@param $id roomテーブルのid(room_id)のこと
     *@return null
     */

    public function room_img_update($img, $id)
    {
        //connectメソッドにアクセス
        parent::connect();

        //PDOを取得
        $pdo = $this->dbh;

        //roomテーブルのimgに画像名（拡張子付き）をUPDATE
        $sql = 'UPDATE room SET img = ? WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$img, $id]);

        //roomテーブルの更新日を現在の日付に上書き
        $sql = 'UPDATE room SET updated_at = CURRENT_TIMESTAMP(6) WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
    }
}
