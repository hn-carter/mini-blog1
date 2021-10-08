<?php

/**
 * データベースのユーザ操作クラス
 */
class UserRepository extends DbRepository
{
    /**
     * DBにユーザを新規登録
     * 
     * @param string $user_name ユーザ名
     * @param string $password パスワード
     */
    public function insert($user_name, $password)
    {
        // パスワードはハッシュ化してDBに登録
        $password = $this->hashPassword($password);
        $now = new DateTime();

        $sql = "INSERT INTO user(user_name, password, created_at)
                VALUES(:user_name, :password, :created_at);";

        $stmt = $this->execute($sql, array(
            ':user_name'  => $user_name,
            ':password'   => $password,
            ':created_at' => $now->format('Y-m-d H:i:s'),
        ));
    }

    /**
     * パスワードのハッシュを作る
     * 
     * @param string $password
     * @return string
     */
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * パスワードのハッシュを検証する
     * 
     * @param string $password
     * @param string $hash
     * @return boolean
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * ユーザ名からユーザ情報を取得
     * 
     * @param string $user_name 検索ユーザ名
     * @return array
     */
    public function fetchByUserName($user_name)
    {
        $sql = "SELECT id, user_name, password, created_at FROM user WHERE user_name = :user_name";

        return $this->fetch($sql, array(':user_name' => $user_name));
    }

    /**
     * 既に同じユーザ名が登録済みか判定
     * 
     * @param string $user_name 検索ユーザ名
     * @return bool
     */
    public function isUniqueUserName($user_name)
    {
        $sql = "SELECT COUNT(id) as count FROM user WHERE user_name = :user_name";

        $row = $this->fetch($sql, array(':user_name' => $user_name));
        if ($row['count'] == 0) {
            return true;
        }

        return false;
    }
}