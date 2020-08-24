<?php
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
            return 'IDが間違っています';
        }

        if($result['delete_flg'] == TRUE){
            return 'IDが見つかりませんでした。';
        }

        if ($result['login_pass'] === $pass) {
            $_SESSION['admin_name'] = $result['name'];
            $_SESSION['auth'] = 1;
            header('Location: top.php');
            exit;
        }

        return 'パスワードが間違っています';
    }
}
