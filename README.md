# flea-market-app

## 環境構築
**Dockerビルド**
1. `git clone git@github.com:tommy311111/flea-market-app.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. .env.example ファイルをコピーして .env ファイルを作成

```bash
cp .env.example .env
```

4. .env ファイルの一部を以下のように編集
``` text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成
``` bash
php artisan key:generate
```

6. マイグレーションの実行
``` bash
php artisan migrate
```

7. シーディングの実行
``` bash
php artisan db:seed
```


## メール認証とMailtrap設定

本アプリでは、会員登録後にメール認証を行います。開発環境では [Mailtrap](https://mailtrap.io/) を使用して、送信メールの確認を行います。

### Mailtrapの使用方法

1. [Mailtrap](https://mailtrap.io/) にサインアップ（無料プランで可）
2. ダッシュボードから Inbox を作成
3. 「SMTP Settings」→「Laravel」を選択し、右上の "Copy" ボタンで設定をすべてコピーしてください。
4. コピーした内容を .env に貼り付け、 MAIL_FROM_ADDRESS と MAIL_FROM_NAME を書き加えてください。
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=あなたのMailtrapユーザー名
MAIL_PASSWORD=あなたのMailtrapパスワード
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Flea Market App"
```
 パスワードは一部しか表示されないため、「Copy」ボタンで全体をコピーしないと正しく取得できません。

## Stripe について

本アプリでは **カード決済** と **コンビニ支払い** に対応しています。
ただし、現状の実装では **コンビニ支払いを選択するとレシート印刷画面に遷移** するため、
意図した画面遷移を確認できるのは **カード決済成功時** となります。

### APIキーの設定方法
Stripe の APIキーを `.env` に設定してください。

```env
STRIPE_PUBLIC_KEY=pk_test_xxxxxxxxxxxxx
STRIPE_SECRET_KEY=sk_test_xxxxxxxxxxxxx
```

- **公開鍵 (`pk_...`)** : フロントエンドで使用
- **秘密鍵 (`sk_...`)** : サーバー側（Laravel）で使用

※ APIキーは Stripe ダッシュボードの「開発者 → APIキー」から取得できます。

### テスト環境について

開発環境では Stripe の **テストモード** を利用してください。
本番用のカードやコンビニ決済を登録する必要はありません。

#### テストカード番号

| 種類           | 番号                  | 有効期限   | CVC  |
|----------------|---------------------|-----------|------|
| 成功するカード | 4242 4242 4242 4242 | 任意の未来 | 任意 |
| 失敗するカード | 4000 0000 0000 0002 | 任意の未来 | 任意 |

👉 その他のテストカード番号一覧: [Stripe Docs - Testing](https://stripe.com/docs/testing)

#### テスト用コンビニ支払い

コンビニ支払いもテスト可能です。決済画面に遷移するとコンビニ選択画面が表示されます。
※ 実際の支払い処理は行われません。


### 参考リンク
- [Stripe公式ドキュメント: Checkout](https://docs.stripe.com/payments/checkout?locale=ja-JP)


## テスト環境のセットアップ手順

このプロジェクトでは、テスト実行に専用のテスト用データベース `demo_test` を使用します。以下の手順に従って準備をしてください。

---

### 🔹 1. テスト用データベースの作成（MySQL）

```bash
docker-compose exec mysql bash
mysql -u root -p
```
※ パスワードは docker-compose.yml 内の MYSQL_ROOT_PASSWORD に記載されている値です。
```sql
CREATE DATABASE demo_test;
SHOW DATABASES;
```
demo_test が一覧に表示されれば作成完了です。

### 🔹 2. テスト用 .env.testing ファイルの作成
```bash
docker-compose exec php bash
cp .env.testing.example .env.testing
```
`.env.testing`ファイルの以下の2項目だけ、自分のMailtrap情報に書き換えてください。
```env
MAIL_USERNAME=あなたのMailtrapユーザー名
MAIL_PASSWORD=あなたのMailtrapパスワード
```
その他のメール設定（MAIL_HOST や MAIL_PORT など）は .env.testing.example にすでに記載されています。

### 🔹 3. テスト環境用のセットアップ
```bash
php artisan key:generate --env=testing
php artisan config:clear
php artisan migrate --env=testing
```
### 🔹 4. テストの実行方法
以下のコマンドで、Feature テストを実行できます
```bash
php artisan test --env=testing
```
補足:
テストでは demo_test データベースが使用されます。本番・開発用DBとは異なります。


## テストユーザー情報（初期データ）

開発環境またはテスト環境でログイン確認するためのテストユーザーがあらかじめ用意されています。
※ 全ユーザーのパスワードは共通で `password` です

| 名前     | メールアドレス             | パスワード | 出品           | 購入 | コメント | いいね | 役割                     |
| -------- | -------------------------- | ---------- | -------------- | ---- | -------- | ------ | ------------------------ |
| 佐藤 美咲 | misaki@example.com         | password   | 5件（未販売）  | なし | 5件      | 5件    | CO01～CO05の商品を出品    |
| 鈴木 大輔 | daisuke@example.com        | password   | 5件（未販売）  | なし | 5件      | 5件    | CO06～CO10の商品を出品    |
| 高橋 結衣 | yui@example.com            | password   | なし           | なし | 0件      | 0件    | 何も紐付けられていないユーザー |

> セキュリティ上、本番環境には **このテストユーザーを残さないようにしてください**。


## 使用技術(実行環境)
- PHP7.4.9
- Laravel8.83.3
- MySQL8.0.26


## テーブル仕様
### usersテーブル
| カラム名          | 型           | primary key | unique key | not null | foreign key |
| ----------------- | ------------ | ----------- | ---------- | -------- | ----------- |
| id                | bigint       | ◯           |            | ◯        |             |
| name              | varchar(255) |             |            | ◯        |             |
| email             | varchar(255) |             | ◯          | ◯        |             |
| email_verified_at | timestamp    |             |            |          |             |
| password          | varchar(255) |             |            | ◯        |             |
| remember_token    | varchar(100) |             |            |          |             |
| created_at        | timestamp    |             |            |          |             |
| updated_at        | timestamp    |             |            |          |             |

---

### user_profilesテーブル
| カラム名   | 型           | primary key | unique key | not null | foreign key |
| ---------- | ------------ | ----------- | ---------- | -------- | ----------- |
| id         | bigint       | ◯           |            | ◯        |             |
| user_id    | bigint       |             | ◯          | ◯        | users.id    |
| postcode   | varchar(8)   |             |            | ◯        |             |
| address    | varchar(255) |             |            | ◯        |             |
| building   | varchar(255) |             |            | ◯        |             |
| image      | varchar(255) |             |            |          |             |
| created_at | timestamp    |             |            |          |             |
| updated_at | timestamp    |             |            |          |             |

---

### categoriesテーブル
| カラム名   | 型           | primary key | unique key | not null | foreign key |
| ---------- | ------------ | ----------- | ---------- | -------- | ----------- |
| id         | bigint       | ◯           |            | ◯        |             |
| name       | varchar(255) |             | ◯          | ◯        |             |
| created_at | timestamp    |             |            |          |             |
| updated_at | timestamp    |             |            |          |             |

---

### itemsテーブル
| カラム名    | 型                                 | primary key | unique key | not null | foreign key |
| ----------- | ---------------------------------- | ----------- | ---------- | -------- | ----------- |
| id          | bigint                             | ◯           |            | ◯        |             |
| name        | varchar(255)                       |             |            | ◯        |             |
| user_id     | bigint                             |             |            | ◯        | users.id    |
| condition   | enum(良好,目立った傷や汚れなし,やや傷や汚れあり,状態が悪い) |             |            | ◯        |             |
| price       | int                                |             |            | ◯        |             |
| brand_name  | varchar(255)                       |             |            |          |             |
| image       | varchar(255)                       |             |            | ◯        |             |
| description | text                               |             |            | ◯        |             |
| created_at  | timestamp                          |             |            |          |             |
| updated_at  | timestamp                          |             |            |          |             |

---

### category_itemテーブル（中間）
| カラム名   | 型     | primary key | unique key          | not null | foreign key   |
| ---------- | ------ | ----------- | ------------------- | -------- | ------------- |
| id         | bigint | ◯           |                     | ◯        |               |
| item_id    | bigint |             | ◯ (with category_id)| ◯        | items.id      |
| category_id| bigint |             | ◯ (with item_id)    | ◯        | categories.id |
| created_at | timestamp |         |                     |          |               |
| updated_at | timestamp |         |                     |          |               |

---

### likesテーブル
| カラム名   | 型     | primary key | unique key | not null | foreign key |
| ---------- | ------ | ----------- | ---------- | -------- | ----------- |
| id         | bigint | ◯           |            | ◯        |             |
| user_id    | bigint |             |            | ◯        | users.id    |
| item_id    | bigint |             |            | ◯        | items.id    |
| deleted_at | timestamp |          |            |          |             |
| created_at | timestamp |          |            |          |             |
| updated_at | timestamp |          |            |          |             |

---

### commentsテーブル
| カラム名   | 型     | primary key | unique key | not null | foreign key |
| ---------- | ------ | ----------- | ---------- | -------- | ----------- |
| id         | bigint | ◯           |            | ◯        |             |
| user_id    | bigint |             |            | ◯        | users.id    |
| item_id    | bigint |             |            | ◯        | items.id    |
| body       | text   |             |            | ◯        |             |
| created_at | timestamp |          |            |          |             |
| updated_at | timestamp |          |            |          |             |

---

### ordersテーブル
| カラム名          | 型           | primary key | unique key | not null | foreign key |
| ----------------- | ------------ | ----------- | ---------- | -------- | ----------- |
| id                | bigint       | ◯          |            | ◯        |             |
| buyer_id          | bigint       |             |            | ◯        | users.id    |
| seller_id         | bigint       |             |            | ◯        | users.id    |
| item_id           | bigint       |             |            | ◯        | items.id    |
| payment_method    | enum(コンビニ払い,カード支払い) | | | ◯ | |
| status            | enum('pending','completed') | | | ◯ | |
| sending_postcode  | varchar(8)   |             |            | ◯        |             |
| sending_address   | varchar(255) |             |            | ◯        |             |
| sending_building  | varchar(255) |             |            | ◯        |             |
| created_at        | timestamp    |             |            |          |             |
| updated_at        | timestamp    |             |            |          |             |

---

### ratingsテーブル
| カラム名   | 型     | primary key | unique key                          | not null | foreign key |
| ---------- | ------ | ----------- | ----------------------------------- | -------- | ----------- |
| id         | bigint | ◯           |                                     | ◯        |             |
| order_id   | bigint |             | ◯ (with rater_id, rated_id)         | ◯        | orders.id   |
| rater_id   | bigint |             | ◯ (with order_id, rated_id)         | ◯        | users.id    |
| rated_id   | bigint |             | ◯ (with order_id, rater_id)         | ◯        | users.id    |
| score      | tinyint|             |                                     | ◯        |             |
| created_at | timestamp |          |                                     |          |             |
| updated_at | timestamp |          |                                     |          |             |

---

### chatsテーブル
| カラム名   | 型     | primary key | unique key | not null | foreign key |
| ---------- | ------ | ----------- | ---------- | -------- | ----------- |
| id         | bigint | ◯          |            | ◯        |             |
| order_id   | bigint |             |            | ◯        | orders.id   |
| sender_id  | bigint |             |            | ◯        | users.id    |
| message    | text   |             |            | ◯        |             |
| image      | varchar(255) |       |            |          |             |
| is_read    | boolean |            |            | ◯       |             |
| created_at | timestamp |          |            |          |             |
| updated_at | timestamp |          |            |          |             |


## ER図
![ER図](./er_diagram_v2.png)

## URL
- 開発環境：http://localhost
- phpMyAdmin：http://localhost:8080/
