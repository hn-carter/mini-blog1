# ミニブログアプリケーション

書籍「パーフェクト PHP」のミニブログアプリケーションを参考に作成しています。

動作環境
PHP 7.4.21

データベース
SQLite3


## 機能

以下の機能があります。

* ユーザーアカウント (account)
  - アカウント登録 (signup)
  - ログイン (signin)
  - ログアウト (signout)
  - アカウント情報トップ (index)
  - フォロー (follow)
* ユーザの投稿 (status)
  - ホームページ (index)
  - 投稿 (post)
  - ユーザの投稿一覧 (user)
  - 個別の投稿 (show)

コントローラとそのアクション
* AccountController
  - index
  - signup
  - register
  - signin
  - authenticate
  - signout
  - follow

* StatusController
  - index
  - post
  - user
  - show


## ディレクトリ構成

```
www
├ controllers コントローラを配置
├ core フレームワークを構成するクラスを配置
├ db データベースファイル
├ models モデルを配置
├ views ビュー(HTMLファイル)を配置
└ web ドキュメントルート
```

## CSSのアドレス指定

'views/layout.php'ファイルからCSSを参照しています。

```html
<head>
    ...
    <link href="/web/css/style.css" rel="stylesheet" >
</head>
```

リンクにはルートからの絶対バスを指定するため、ぞれぞれの環境に合わせて変更してください。


## 書籍「パーフェクト PHP」との違い

使用するデータベースをMySQLからSQLite3に変更しています。

パスワードのハッシュ作成を`sha1`から`password_hash`に変更しています。

生成するHTMLは"HTML Living Standard"に準拠しています。

## ライセンス

「MIT ライセンス」です。

