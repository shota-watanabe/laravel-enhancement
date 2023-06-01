<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Company;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create([
            'name' => 'サンプル会社'
        ]);

        $this->user = User::factory([
            'company_id' => $this->company->id,
            'name' => 'サンプルユーザー'
        ])->create();

        $this->section = Section::factory([
            'company_id' => $this->company->id,
            'name' => 'サンプル部署'
        ])->create();

        $this->section->users()->attach($this->user->user_id);

        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_search_user_by_user(): void
    {
        $url = route('users.index');

        // Guest のときは、login にリダイレクトされる
        $this->get($url)->assertRedirect(route('login'));

        $this->actingAs($this->user)->get($url)->assertStatus(200);

        // ログイン状態で検索ワードを入力して検索
        $response = $this->actingAs($this->user)->get($url, [
            'search_type' => 'user',
            'search_keyword' => 'サンプルユーザー',
        ]);
        $response->assertStatus(200);
        $response->assertViewHas('users');
        $response->assertSee('サンプルユーザー');
    }

    public function test_index_search_company_by_user(): void
    {
        $url = route('users.index');

        // ログイン状態で検索ワードを入力して検索
        $response = $this->actingAs($this->user)->get($url, [
            'search_type' => 'company',
            'search_keyword' => 'サンプル会社',
        ]);
        $response->assertStatus(200);
        $response->assertViewHas('users');
        $response->assertSee('サンプル会社');
    }

    public function test_index_search_section_by_user(): void
    {
        $url = route('users.index');

        // ログイン状態で検索ワードを入力して検索
        $response = $this->actingAs($this->user)->get($url, [
            'search_type' => 'section',
            'search_keyword' => 'サンプル部署',
        ]);
        $response->assertStatus(200);
        $response->assertViewHas('users');
        // $response->assertSee('サンプル部署');
    }
}
