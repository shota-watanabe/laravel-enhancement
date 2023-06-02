<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Company;
use App\Models\CsvExportHistory;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CsvExportHistoryControllerTest extends TestCase
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
    }

    public function test_index(): void
    {
        $url = route('csv_export_histories.index');

        // Guest のときは、login にリダイレクトされる
        $this->get($url)->assertRedirect(route('login'));

        $this->actingAs($this->user)->get($url)->assertStatus(200);
    }

    public function test_store(): void
    {
        $url = route('users.csv_export_histories.store');

        // Guest のときは、login にリダイレクトされる
        $this->get($url)->assertRedirect(route('login'));

        $response = $this->actingAs($this->user)->post($url);
        $response->assertStatus(200);
        $response->assertDownload();
    }

    public function test_show(): void
    {
        $url = route('users.csv_export_histories.store');

        $this->actingAs($this->user)->post($url);

        $csvExportHistory = CsvExportHistory::query()->latest()->first();

        $url = route('csv_export_histories.show', $csvExportHistory);

        $response = $this->actingAs($this->user)->get($url);
        $response->assertStatus(200);
        $response->assertDownload();

        $this->csvExportHistory = CsvExportHistory::factory()->create();
        $url = route('csv_export_histories.show', $this->csvExportHistory);
        $response = $this->actingAs($this->user)->get($url);
        $response->assertStatus(500);
    }
}
