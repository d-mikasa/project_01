<?php
class ImageUpdata extends Model
{
    public function image_update($img, $id)
    {
        parent::connect();
        $pdo = $this->dbh;
        $sql = 'UPDATE room SET img = ? WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$img, $id]);
    }
}
