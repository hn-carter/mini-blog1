<?php

/**
 * ユーザーアカウント
 */
class AccountController extends Controller
{
    /**
     * アカウント登録
     * 
     * @return string
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
}