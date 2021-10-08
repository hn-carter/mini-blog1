<?php

class StatusController extends Controller
{
    protected $auth_actions = array('index', 'post');

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

    /**
     * ユーザの投稿一覧アクション
     * 
     * @param array $params
     * @return string 遷移先画面
     */
    public function userAction($params)
    {
        // ユーザの存在確認
        $user = $this->db_manager->get('User')
            ->fetchByUserName($params['user_name']);
        if (!$user) {
            $this->foward404();
        }

        // ユーザの投稿一覧取得
        $statuses = $this->db_manager->get('Status')
            ->fetchAllByUserId($user['id']);

        return $this->render(array(
            'user'     => $user,
            'statuses' => $statuses,
        ));
    }

    /**
     * 投稿詳細アクション
     * 
     * @param array $params
     * @return string 遷移先画面
     */
    public function showAction($params)
    {
        // 投稿情報取得
        $status = $this->db_manager->get('Status')
            ->fetchByIdAndUserName($params['id'], $params['user_name']);

        if (!$status) {
            $this->forward404();
        }

        return $this->render(array('status' => $status));
    }
}