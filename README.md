# 勤怠管理アプリ

従業員の出勤・退勤・休憩・勤怠申請を管理するWebアプリです。

## 環境構築

#### リポジトリをクローン

```
git clone git@github.com:yuki8293/attendance-app.git
```

#### Laravelのビルド

```
docker-compose up -d --build
```

#### Laravel パッケージのダウンロード

```
docker-compose exec php bash
composer install
exit

```

#### .env ファイルの作成

```
cp .env.example .env
```

#### .env ファイルの修正

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

```

#### コンテナに入る

```
docker-compose exec php bash
```

#### キー生成

```
php artisan key:generate
```

#### マイグレーション・シーディングを実行

```
php artisan migrate:fresh --seed
```

### 管理者

email: admin@test.com  
password: password

### 一般ユーザー

email: user@test.com  
password: password

## 使用技術（実行環境）

フレームワーク：Laravel 8.x

言語：PHP 8.x

Webサーバー：Nginx 1.21.1

データベース：MySQL 8.0.26

## ER図

![ER図](attendance.drawio.png)

## URL

- アプリケーション：http://localhost
- phpMyAdmin：http://localhost:8080
