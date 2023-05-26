<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->user = User::factory([
            'company_id' => $this->company->id,
        ])->create();
    }

    public function test_index()
    {
        $url = route('companies.index');

        // Guest のときは、login にリダイレクトされる
        $this->get($url)->assertRedirect(route('login'));

        $response = $this->actingAs($this->user)->get($url);

        $response->assertStatus(200);
    }

    public function test_create()
    {
        $url = route('companies.create');

        // Guest のときは、login にリダイレクトされる
        $this->get($url)->assertRedirect(route('login'));

        $response = $this->actingAs($this->user)
            ->get($url);

        $response->assertStatus(200);
    }

    public function test_store()
    {
        $company_name = $this->faker->company;

        $url = route('companies.store');

        // Guest のときは、login にリダイレクトされる
        $this->post($url, [
            'name' => $company_name,
        ])->assertRedirect(route('login'));

        $response = $this->actingAs($this->user)
            ->post($url, [
                'name' => $company_name,
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('companies', [
            'name' => $company_name,
        ]);

        $company = $this->user->refresh()->company;
        $this->assertEquals($company->id, $this->company->id);

        // バリデーション
        $response = $this->actingAs($this->user)
            ->post($url, [
                'name' => null,
            ]);

        $validation = 'nameは必ず指定してください。';
        $this->get(route('companies.create'))->assertSee($validation);
    }

    public function test_show()
    {
        $url = route('companies.show', $this->company->id);

        // Guest のときは、login にリダイレクトされる
        $this->get($url)->assertRedirect(route('login'));

        $this->actingAs($this->user)->get($url)->assertStatus(200);

        // 存在しない ID のときは、404 になる
        $this->actingAs($this->user)->get(route('companies.show', 9999))->assertStatus(404);
    }

    public function test_edit()
    {
        $company = $this->company;

        $url = route('companies.edit', $company->id);

        // Guest のときは、login にリダイレクトされる
        $this->get($url)->assertRedirect(route('login'));

        $response = $this->actingAs($this->user)
            ->get($url);

        $response->assertStatus(200);
    }

    public function test_update()
    {
        $company = $this->company;

        $company_name = $this->faker->company;

        $url = route('companies.update', $company->id);

        // Guest のときは、login にリダイレクトされる
        $this->put($url, [
            'name' => $company_name,
        ])->assertRedirect(route('login'));

        $response = $this->actingAs($this->user)
            ->put($url, [
                'name' => $company_name,
            ]);

        $response->assertStatus(302);

        // 更新後 companies.show にリダイレクトされる
        $response->assertRedirect(route('companies.show', compact('company')));

        $this->assertDatabaseHas('companies', [
            'name' => $company_name,
        ]);
    }

    public function test_destroy()
    {
        $company = $this->company;

        $url = route('companies.destroy', $company->id);

        // Guest のときは、login にリダイレクトされる
        $this->delete($url)->assertRedirect(route('login'));

        $response = $this->actingAs($this->user)->delete($url);

        $response->assertStatus(302);

        // 削除後 companies.index にリダイレクトされる
        $response->assertRedirect(route('companies.index'));

        $this->assertDatabaseMissing('companies', [
            'id' => $company->id,
        ]);
    }
}
