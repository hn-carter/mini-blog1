<?php

/**
 * ユーザーアカウント
 */
class AccountController extends Controller
{
    // 認証を必要とするアクション
    protected $auth_actions = array('index', 'signout', 'follow');

    /**
     * アカウント登録
     * 
     * @return string 遷移先画面
     */
    public function signupAction()
    {
        return $this->render(array(
            'user_name' => "",
            'password'  => "",
            '_token'    => $this->generateCsrfToken('account/signup'),
        ));
    }

    /**
     * アカウントの入力チェックを行い、DBに新規登録
     * 
     * @return string 遷移先画面
     */
    public function registerAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        // CSRFトークンのチェック
        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/signup', $token)) {
            return $this->redirect('/account/signup');
        }

        $user_name = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');

        $errors = array();

        if (!strlen($user_name)) {
            $errors[] = 'ユーザIDを入力してください';
            // 正規表現は/で囲む ^先頭　\w半角英数アンダーバー {3,20}3〜20文字 $末尾
        } else if (!preg_match('/^\w{3,20}$/', $user_name)) {
            $errors[] = 'ユーザIDは半角英数字およびアンダースコアを3〜20文字以内で入力してください';
        } else if (!$this->db_manager->get('User')->isUniqueUserName($user_name)) {
            $errors[] = 'ユーザIDは既に使用されています';
        }

        if (!strlen($password)) {
            $errors[] = 'バスワードを入力してください';
        } else if (4 > strlen($password) || strlen($password) > 30) {
            $errors[] = 'パスワードは4〜30文字以内で入力してください';
        }

        // エラーがなければDBへユーザ新規登録
        if (count($errors) === 0) {
            $this->db_manager->get('User')->insert($user_name, $password);
            $this->session->setAuthenticated(true);

            $user = $this->db_manager->get('User')->fetchByUserName($user_name);
            $this->session->set('user', $user);

            return $this->redirect('/');
        }

        return $this->render(array(
            'user_name' => $user_name,
            'password'  => $password,
            'errors'    => $errors,
            '_token'    => $this->generateCsrfToken('account/signup'),
        ), 'signup');
    }

    /**
     * アカウント情報トップ
     * 
     * @return string 遷移先画面
     */
    public function indexAction()
    {
        $user = $this->session->get('user');
        $followings = $this->db_manager->get('User')
            ->fetchAllFollowingsByUserId($user['id']);

        return $this->render(array(
            'user'       => $user,
            'followings' => $followings,
        ));
    }

    /**
     * ログインアクション
     * 
     * @return string 遷移先画面
     */
    public function signinAction()
    {
        // 既にログインしている場合、アカウント情報トップへリダイレクト
        if ($this->session->isAuthenticated()) {
            return $this->redirect('/account');
        }

        // ログイン画面を返す
        return $this->render(array(
            'user_name' => '',
            'password'  => '',
            '_token'    => $this->generateCsrfToken('account/signin'),
        ));
    }

    /**
     * ログイン処理
     * 
     * @return string 遷移先画面
     */
    public function authenticateAction()
    {
        // 既にログインしている場合、アカウント情報トップへリダイレクト
        if ($this->session->isAuthenticated()) {
            return $this->redirect('/account');
        }

        if (!$this->request->isPost()) {
            $this->foward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/signin', $token)) {
            return $this->redirect('/account/signin');
        }

        $user_name = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');

        $errors = array();

        if (!strlen($user_name)) {
            $errors[] = 'ユーザIDを入力してください';
        }

        if (!strlen($password)) {
            $errors[] = 'パスワードを入力してください';
        }

        if (count($errors) === 0) {

            $user_repository = $this->db_manager->get('User');
            $user = $user_repository->fetchByUserName($user_name);

            // パスワードチェック
            if (!$user
                || (!$user_repository->verifyPassword($password, $user['password']))
            ) {
                $errors[] = 'ユーザIDかパスワードが不正です';
            } else {
                $this->session->setAuthenticated(true);
                $this->session->set('user', $user);

                return $this->redirect('/');
            }
        }

        return $this->render(array(
            'user_name' => $user_name,
            'password'  => $password,
            'errors'    => $errors,
            '_token'    => $this->generateCsrfToken('account/signin'),
        ), 'signin');
    }

    /**
     * サインアウト（ログアウト）アクション
     * 
     * @return string 遷移先画面
     */
    public function signoutAction()
    {
        // セッション変数をクリアし、認証フラグリセット
        $this->session->clear();
        $this->session->setAuthenticated(false);

        return $this->redirect('/account/signin');
    }

    /**
     * フォローアクション
     * 
     * @return string 遷移先画面
     */
    public function followAction()
    {
        if (!$this->request->isPost()) {
            $this->foward404();
        }

        $following_name = $this->request->getPost('following_name');
        if (!$following_name) {
            $this->foward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/follow', $token)) {
            return $this->redirect('/user/' . $following_name);
        }

        // フォローしたいユーザの存在確認
        $follow_user = $this->db_manager->get('User')
            ->fetchByUserName($following_name);
        if (!$follow_user) {
            $this->foward404();
        }

        $user = $this->session->get('user');

        $following_repository = $this->db_manager->get('Following');
        // 既にフォロー済みでないかチェック
        if ($user['id'] !== $follow_user['id']
            && !$following_repository->isFollowing($user['id'], $follow_user['id'])) {
            $following_repository->insert($user['id'], $follow_user['id']);
        }

        return $this->redirect('/account');
    }
}