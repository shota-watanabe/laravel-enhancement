# 開発ツールのセットアップ

```bash
// On Mac
laravel-practis % cd laravel-practis
laravel-practis % composer install
```

# sail のインストール・起動

## インストール

```bash
laravel-practis % php artisan sail:install

 Which services would you like to install? [mysql]:
  [0] mysql             # MySQL 
  [1] pgsql             # PostgreSQL 
  [2] mariadb           # MariaDB
  [3] redis             # Redis セッション管理似つかられる KVストア
  [4] memcached         # Memcache セッション管理似つかられる KVストア
  [5] meilisearch       # 全文検索エンジン
  [6] minio             # Amazon S3クラウド・ストレージ・サービスと互換性のあるオブジェクト・ストレージ・サーバー
  [7] mailpit           # 開発環境テスト用メールサーバ
  [8] selenium          # ブラウザテストツール
  [9] soketi            # WebSocketサーバー
 > 0,3,7 # 0,3,7 で応答
 
 # Docker が実行されて、自動的に Docker 環境が構築される
 Sail scaffolding installed successfully.
 [+] Running 11/11

 （中略）
 
 => => writing image sha256:877f4a3b06437fba9251994beeca11fffdc7958eff2567acf34da16af6bb631a                                                                                                                                       0.0s
 => => naming to sail-8.2/app                                                                                                                                                                                                      0.0s
 Sail build successful.
```

## 起動

```bash
laravel-practis % vendor/bin/sail up -d
```

# Composer ライブラリをインストール

```bash
# デバッグバー表示
laravel-practis % vendor/bin/sail composer require --dev barryvdh/laravel-debugbar

# IDE 用のコードジャンプなどがしやすくなる
laravel-practis % vendor/bin/sail composer require --dev barryvdh/laravel-ide-helper
laravel-practis % vendor/bin/sail artisan ide-helper:generate
```
