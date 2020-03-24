# これはなに
WordPressは重すぎて移行不可避、でもコメント欄を外部サービスに頼るなんて嫌という人のためのプラグイン

# インストール
npmには登録してないのでディレクトリからインストール
```sh
npm install ../hexo-phpcomment --save
```
更に同梱のphpディレクトリを実行できる場所へ
```sh
cp -r ../hexo-phpcomment/php /var/www/php/
```

# 設定
2箇所必要です。
_config.yml
```yml
phpcomment:
  phpdir: /static/php 
  #PHPを置くディレクトリ
  #多分https://example.com/phpとかで別サーバーも参照可
```
php/config.ini
```ini
db_dir=/var/secret/db.db ;読めないところへ
password=testing123 ;管理画面ログイン用
```
.iniへのアクセスを拒否
```nginx
    location ~ \.ini$ {
        deny all;
    }
```