<?php
class AdminUser extends Model
{
    public function userLogin($id, $pass)
    {
        parent::connect();
        $sql = 'SELECT * FROM admin_user WHERE login_id = ? AND delete_flg = FALSE';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        if (empty($result)) {
            return 'IDが間違っています';
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
