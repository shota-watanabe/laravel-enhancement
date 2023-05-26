# Laravel セットアップ

## 前提

- Mac に Git がインストールされていること
- Mac に Docker Desktop がインストールされていること

    https://www.docker.com/products/docker-desktop/


- Mac に php がインストールされていること

    ```bash
    $ brew install php       # homebrew で php をインストール
    $ php -v                 # 確認
    ```

- Mac に composer がインストールされていること

    ```bash
    $ brew install composer  # homebrew で php をインストール
    $ composer -V            # 確認
    ```

## Laravel のセットアップ

```bash
$ mkdir laravel-practis      # ワークディレクトリ作成
$ cd laravel-practis
```

```bash
$ composer global require laravel/installer
$ laravel new laravel-practis
```
