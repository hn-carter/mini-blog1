<?php

/**
 * 投稿内容データ
 */
class StatusRepository extends DbRepository
{
    /**
     * 投稿をDBに登録
     * 
     * @param string $user_id ユーザID
     * @param string $body 投稿内容
     */
    public function insert($user_id, $body)
    {
        $now = new DateTime();

        $sql = "INSERT INTO status(user_id, body, created_at)
                VALUES (:user_id, :body, :created_at);";

        $stmt = $this->execute($sql, array(
            ':user_id'    => $user_id,
            ':body'       => $body,
            ':created_at' => $now->format('Y-m-d H:i:s'),
        ));
    }

    /**
     * 投稿情報の一覧を取得
     * 
     * @param string $user_id
     * @return array
     */
    public function fetchAllPersonalArchivesByUserId($user_id)
    {
        $sql = "SELECT a.id, a.user_id, a.body, a.created_at, u.user_name
                FROM status a
                    LEFT JOIN user u ON a.user_id = u.id
                    LEFT JOIN following f ON f.following_id = a.user_id
                        AND f.user_id = :user_id
                WHERE f.user_id = :user_id OR u.id = :user_id
                ORDER BY a.created_at DESC;";
        
        return $this->fetchAll($sql, array(':user_id' => $user_id));
    }

    /**
     * ユーザIDから投稿一覧を取得
     * 
     * @param string $user_id
     * @return array
     */
    public function fetchAllByUserId($user_id)
    {
        $sql = "SELECT a.id, a.user_id, a.body, a.created_at, u.user_name
                FROM status a
                    LEFT JOIN user u ON a.user_id = u.id
                WHERE u.id = :user_id
                ORDER BY a.created_at DESC;";

        return $this->fetchAll($sql, array(':user_id' => $user_id));
    }    

    /**
     * 投稿IDとユーザ名から投稿を取得
     * 
     * @param string $id
     * @param string $user_name
     * @return array
     */
    public function fetchByIdAndUserName($id, $user_name)
    {
        $sql = "SELECT a.id, a.user_id, a.body, a.created_at, u.user_name
                FROM status a
                    LEFT JOIN user u ON a.user_id = u.id
                WHERE a.id = :id
                  AND u.user_name = :user_name;";

        return $this->fetch($sql, array(
            ':id' => $id,
            ':user_name' => $user_name,
        ));
    }    
}