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
        $sql = "SELECT a.id, a.user_id, a.body, a.created_at , u.user_name
                FROM status a
                    LEFT JOIN user u ON a.user_id = u.id
                WHERE u.id = :user_id
                ORDER BY a.created_at DESC;";
        
        return $this->fetchAll($sql, array(':user_id' => $user_id));
    }
}