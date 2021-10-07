<?php

class MiniBlogApplication extends Application
{
    protected $login_action = array('account', 'signin');

    /**
     * ルートディレクトリへのパスを取得
     * 
     * @return string
     */
    public function getRootDir()
    {
        return dirname(__FILE__);
    }

    /**
     * ルーティング定義配列を返す
     * 
     * @return array
     */
    protected function registerRoutes()
    {
        return array(
            '/'
                => array('controller' => 'status', 'action' => 'index'),
            '/status/post'
                => array('controller' => 'status', 'action' => 'post'),
            '/account'
                => array('controller' => 'account', 'action' => 'index'),
            '/account/:action'
                => array('controller' => 'account'),
        );
    }

    /**
     * アプリケーションを設定
     */
    protected function configure()
    {
        // データベースの接続設定
        $this->db_manager->connect('master', array(
            'dsn'      => 'sqlite:../db/blog.db',
            'user'     => null,
            'password' => null
        ));
    }
}