<?php
require_once ('model.php');
class AdminUser extends Model
{
    public function checkUser($id, $pass)
    {
        parent::connect();
        $sql = 'SELECT * FROM admin_user WHERE login_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();



        if (empty($result)) {
            $result = 'IDが間違っています';
            return $result;
        }

        if($result['delete_flg'] == TRUE){
            $result = 'IDが見つかりませんでした。';
            return $result;
        }

        if ($result['login_pass'] === $pass) {
            $_SESSION['admin_name'] = $result['name'];
            $_SESSION['admin_login'] = 1;
            header('Location: top.php');
            exit;
        }

        $result = 'パスワードが間違っています';
        return $result;
    }
}
