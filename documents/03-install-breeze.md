# laravel-breeze

https://readouble.com/laravel/10.x/ja/starter-kits.html#laravel-breeze

> Laravel Breezeは、ログイン、ユーザー登録、パスワードリセット、メール確認、パスワード確認など、すべての認証機能を最小かつシンプルにLaravelへ実装したものです。さらに、Breezeには、ユーザーが名前、電子メールアドレス、パスワードを更新できるシンプルな「プロファイル」ページが含まれています。 
> 
> Laravel Breezeのデフォルトのビュー層は、Tailwind CSSでスタイルした、シンプルなBladeテンプレートで構成しています。また、VueやReactとInertiaを使用したアプリケーションのスカフォールドを作ることも可能です。

## laravel-breeze のインストール

```bash
$ cd laravel-practis
$ vendor/bin/sail composer require laravel/breeze --dev
$ vendor/bin/sail artisan breeze:install

  Which stack would you like to install?
  blade .......................................................................................................................................... 0  
  react .......................................................................................................................................... 1  
  vue ............................................................................................................................................ 2  
  api ............................................................................................................................................ 3  
❯ 0         # 0 で応答

  Would you like to install dark mode support? (yes/no) [no]
❯ no        # no で応答

  Would you prefer Pest tests instead of PHPUnit? (yes/no) [no]
❯ no        # no で応答

  （中略）

   INFO  Breeze scaffolding installed successfully.  
```

http://localhost にアクセスすると、右上に 「Log in」「Register」のリンクが出現する