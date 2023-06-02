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

        $this->section->users()->attach($this->user->id);

        $this->adminCompany = Company::factory()->create([
            'name' => 'アドミン会社'
        ]);

        $this->admin = User::factory()->admin()->create([
            'company_id' => $this->adminCompany->id,
            'name' => 'サンプルアドミン'
        ]);

        $this->adminSection = Section::factory([
            'company_id' => $this->adminCompany->id,
            'name' => 'アドミン部署'
        ])->create();

        $this->adminSection->users()->attach($this->admin->id);
    }

    public function test_index_search_user_by_user(): void
    {
        $url = route('users.index', [
            'search_type' => 'user',
            'search_keyword' => 'サンプルユーザー',
        ]);

        // Guest のときは、login にリダイレクトされる
        $this->get($url)->assertRedirect(route('login'));

        $this->actingAs($this->user)->get($url)->assertStatus(200);

        $url = route('users.index', [
            'search_type' => 'user',
            'search_keyword' => '',
        ]);

        $response = $this->actingAs($this->user)->get($url);
        $response->assertStatus(200);

        // ログイン状態で検索ワードを入力して検索
        $response = $this->actingAs($this->user)->get($url);
        $response->assertStatus(200);
        $response->assertSee('サンプルユーザー');

        // 管理者は他社のユーザーを検索できる
        $response = $this->actingAs($this->admin)->get($url);
        $response->assertStatus(200);
        $response->assertSee('サンプルユーザー');

        $url = route('users.index', [
            'search_type' => 'user',
            'search_keyword' => 'サンプルアドミン',
        ]);

        $response = $this->actingAs($this->user)->get($url);
        // 一般ユーザーは他社のユーザーを検索できない
        $response->assertDontSee('サンプルアドミン');

        $response = $this->actingAs($this->admin)->get($url);
        $response->assertStatus(200);
        // 管理者は自社のユーザーを検索できる
        $response->assertSee('サンプルアドミン');
    }

    public function test_index_search_company_by_user(): void
    {
        $url = route('users.index', [
            'search_type' => 'company',
            'search_keyword' => 'サンプル会社',
        ]);

        // ログイン状態で検索ワードを入力して検索
        $response = $this->actingAs($this->user)->get($url);
        $response->assertStatus(200);
        $response->assertSee('サンプル会社');

        $url = route('users.index', [
            'search_type' => 'company',
            'search_keyword' => '',
        ]);

        $response = $this->actingAs($this->user)->get($url);
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get($url);
        $response->assertStatus(200);
        // 管理者は他社を検索できる
        $response->assertSee('サンプル会社');

        $url = route('users.index', [
            'search_type' => 'company',
            'search_keyword' => 'アドミン会社',
        ]);

        $response = $this->actingAs($this->admin)->get($url);
        $response->assertStatus(200);

        // 管理者は自社を検索できる
        $response->assertSee('アドミン会社');
    }

    public function test_index_search_section_by_user(): void
    {
        $url = route('users.index', [
            'search_type' => 'section',
            'search_keyword' => 'サンプル部署',
        ]);

        // ログイン状態で検索ワードを入力して検索
        $response = $this->actingAs($this->user)->get($url);
        $response->assertStatus(200);
        $response->assertSee('サンプル部署');

        $url = route('users.index', [
            'search_type' => 'section',
            'search_keyword' => '',
        ]);

        $response = $this->actingAs($this->user)->get($url);
        $response->assertStatus(200);

        // 管理者は、他社の部署を検索できる
        $response = $this->actingAs($this->admin)->get($url);
        $response->assertStatus(200);
        $response->assertSee('サンプル部署');

        $url = route('users.index', [
            'search_type' => 'section',
            'search_keyword' => 'アドミン部署',
        ]);

        $response = $this->actingAs($this->user)->get($url);

        // 一般ユーザーは他社の部署を検索できない
        $response->assertDontSee('アドミン部署');

        // 管理者は、自社の部署を検索できる
        $response = $this->actingAs($this->admin)->get($url);
        $response->assertStatus(200);
        $response->assertSee('アドミン部署');
    }
}
