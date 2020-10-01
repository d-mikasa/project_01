<?php

/*
【部屋削除機能】
DBから指定IDの部屋情報を削除する
*/
class AdminRoom extends Model
{
    public function Delete_detail($id)
    {
        parent::connect();
        $pdo = $this->dbh;
        $sql = 'DELETE FROM room WHERE id = ' . $id;
        $pdo->query($sql);

        $sql = 'DELETE FROM room_detail WHERE room_id = ' . $id;
        $pdo->query($sql);
    }


/*
【部屋詳細情報取得】
DBから指定IDの部屋情報を取得する機能
*/
    public function Read_detail($id)
    {
        try {
            parent::connect();
            $pdo = $this->dbh;
            $sql = 'SELECT * FROM room_detail WHERE room_id = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetchAll();
            return $result;
        } catch (PDOException $e) {
            header("Content-Type: text/plain; charset=UTF-8", true, 500);
            exit($e->getMessage());
        }
    }


/*
【部屋編集機能】
DBから指定IDの部屋情報を編集する
*/
    public function update($id , $list, $room = NULL)
    {
        //まずは該当のIDデータを全て削除する
        parent::connect();
        $pdo = $this->dbh;
        $sql = 'DELETE FROM room_detail WHERE room_id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

             //登録モードにより処理を帰る
             //モード：新規作成の処理
        if ($_SESSION['mode'] == 'create') {
            $sql = 'INSERT INTO room(name) VALUES (?)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$room]);
            $sql ="SELECT  AUTO_INCREMENT
            FROM  INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = 'd_mikasa'
            AND   TABLE_NAME   = 'room'";
            $stmt = $pdo -> query($sql) -> fetchAll();
            $id = $stmt;

            $sql = 'SELECT * FROM room ORDER BY created_at DESC LIMIT 1';
            $stmt = $pdo -> query($sql) -> fetch();
            $id = $stmt['id'];

            for ($i = 0; $i < count($list); $i++) {
                $sql = 'INSERT INTO room_detail(room_id,capacity,remarks,price) VALUES (?,?,?,?)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id, $list[$i]['capacity'], $list[$i]['remarks'], $list[$i]['price']]);
            }
        }

        //モード：編集の処理
        if ($_SESSION['mode'] == 'edit') {
            $sql = 'UPDATE room SET updated_at = CURRENT_TIMESTAMP(6) WHERE id = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            for ($i = 0; $i < count($list); $i++) {
                $sql = 'INSERT INTO room_detail (room_id, capacity, remarks, price) VALUES (?,?,?,?)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id, $list[$i]['capacity'], $list[$i]['remarks'], $list[$i]['price']]);
            }
        }
    }



    public function show_room()
    {
        parent::connect();
        $pdo = $this -> dbh;
        $sql = 'SELECT * FROM room';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function show_detail($id)
    {
        parent::connect();
        $pdo = $this -> dbh;
        $sql = 'SELECT * FROM room_detail WHERE room_id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetchAll();
        return $result;
    }



 //データベースの中にIDの一致するものを検索する

     public function room_get()
     {
         parent::connect();
         $pdo = $this -> dbh;
         $sql = 'SELECT * FROM room';
         $stmt = $pdo->prepare($sql);
         $stmt->execute();
         $result = $stmt->fetchAll();
         return $result;
     }



    public function image_update($img, $id)
    {
        parent::connect();
        $pdo = $this->dbh;
        $sql = 'UPDATE room SET img = ? WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$img, $id]);
    }
	}
