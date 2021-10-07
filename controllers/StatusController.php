<?php

class StatusController extends Controller
{
    /**
     * ユーザのホームページにアクセスしたときのアクション
     * 
     * @return string ユーザの投稿一覧レスポンス画面
     */
    public function indexAction()
    {
        // ユーザ情報取得
        $user = $this->session->get('user');
        // ユーザの投稿一覧取得
        $statuses = $this->db_manager->get('Status')
            ->fetchAllPersonalArchivesByUserId($user['id']);

        return $this->render(array(
            'statuses' => $statuses,
            'body'     => '',
            '_token'   => $this->generateCsrfToken('status/post'),
        ));
    }

    /**
     * 投稿処理アクション
     */
    public function postAction()
    {
        if (!$this->request->isPost()) {
            $this->foward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('status/post', $token)) {
            return $this->redirect('/');
        }

        // 画面に入力された投稿内容を取得
        $body = $this->request->getPost('body');

        $errors = array();

        // 投稿内容の入力チェック（バリデーション）
        if (!strlen($body)) {
            $errors[] = 'ひとことを入力してください';
        } else if (mb_strlen($body) > 200) {
            $errors[] = 'ひとことは200文字以内で入力してください';
        }

        // データベースに登録し、ホーム画面へ移動
        if (count($errors) === 0) {
            $user = $this->session->get('user');
            $this->db_manager->get('Status')->insert($user['id'], $body);

            return $this->redirect('/');
        }

        $user = $this->session->get('user');
        // エラー表示のために再度入力画面を表示するが、同一画面に一覧も表示しているため
        // 投稿一覧を取得
        $statuses = $this->db_manager->get('Status')
            ->fetchAllPersonalArchivesByUserId($user['id']);

        return $this->render(array(
            'errors'   => $errors,
            'body'     => $body,
            'statuses' => $statuses,
            '_token'   => $this->generateCsrfToken('status/post'),
        ), 'index');
    }
}