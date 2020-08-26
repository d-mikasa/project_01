<?php
//データベースの中にIDの一致するものを検索する
class EditList extends Model
{
    public function Edit_detail($id)
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
}
