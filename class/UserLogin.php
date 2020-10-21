<?php
class UserLogin extends Model
{
    public function Login($id,$pass)
    {
        parent::connect();
        $sql = 'SELECT * FROM user WHERE login_id = ? AND status = 1';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        if (empty($result)) {
            return 'IDが間違っています';
        }

        if ($result['login_pass'] === $pass) {

            //ログイン状態にユーザーIDを入れて、T/Fを判断させる
            $_SESSION['user_auth'] = $result['id'];
            header('Location: reservation.php');
            exit;
        }

        return 'パスワードが間違っています';
    }
}
