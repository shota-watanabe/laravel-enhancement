# ファクトリの編集

https://readouble.com/laravel/10.x/ja/eloquent-factories.html

> アプリケーションのテストやデータベースの初期値生成時に、データベースへレコードを挿入する必要がある場合があるでしょう。Laravelでは、各カラムの値を手作業で指定する代わりに、Eloquentモデルそれぞれに対して、モデルファクトリを使用し、デフォルト属性セットを定義できます。

Faker ライブラリが組み込まれているので、優先して使う。

`UserFactory`

```php
<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => function () {
                return Company::query()->inRandomOrder()->first()->id;
            }, // company_id 追加
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    // アドミン用のファクトリ定義を追加
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Kosuke Shibuya',
            'email' => 'admin@example.com',
        ]);
    }
}
```

`CompanyFactory`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
        ];
    }
}
```

# Seeder の編集

初期設定では、User モデルが存在しているが、UserSeeder は存在していないので、`artisan` コマンドを使って作成します。

```bash
$ vendor/bin/sail artisan make:seeder UserSeeder
```

`database/seeders/UserSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->admin()->create(); // admin 
        User::factory()->count(100)->create(); // その他 100 件作る
    }
}
```

`database/seeders/CompanySeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::factory()->count(5)->create();
    }
}
```

`database/seeders/DatabaseSeeder.php` に実行順を考慮して列挙する

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            UserSeeder::class,
        ]);
    }
}
```

# サンプルデータを投入する

以下は全てのテーブルを削除して、マイグレーションを実行したあと、シーダーを実行する。（本番環境では使ってはいけない）

```bash
$ vendor/bin/sail artisan migrate:fresh --seed
```

シーディングのみ行う場合は

```bash
$ vendor/bin/sail artisan db:seed
```

実行したあとで、データが投入されたかを目視しましょう。