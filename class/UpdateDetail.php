<?php
class UpdateDetail extends Model
{
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
}
