<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SectionControllerTest extends TestCase
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

    /**
     * A basic feature test example.
     */
    public function test_index(): void
    {
        $this->company->sections()->createMany(
            \App\Models\Section::factory()->count(5)->make()->toArray()
        );

        $response = $this->actingAs($this->user)->get(route('companies.sections.index', ['company' => $this->company]));

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     */
    public function test_create(): void
    {
        $response = $this->actingAs($this->user)->get(route('companies.sections.create', ['company' => $this->company]));

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     */
    public function test_store(): void
    {
        $section_name = $this->faker->word;

        $response = $this->actingAs($this->user)->post(route('companies.sections.store', ['company' => $this->company]), [
            'name' => $section_name,
        ]);

        $response->assertRedirect(route('companies.sections.index', ['company' => $this->company]));
        $this->assertDatabaseHas('sections', [
            'name' => $section_name,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('companies.sections.store', ['company' => $this->company]), [
            'name' => $section_name,
        ]);

        $validation = 'その部署名はすでに登録済みです。';
        $this->get(route('companies.sections.create', ['company' => $this->company]))->assertSee($validation);
    }

    public function test_show()
    {
        $section = $this->company->sections()->create(
            \App\Models\Section::factory()->make()->toArray()
        );

        $response = $this->actingAs($this->user)->get(route('companies.sections.show', ['company' => $this->company, 'section' => $section]));

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     */
    public function test_edit(): void
    {
        $section = $this->company->sections()->create(
            \App\Models\Section::factory()->make()->toArray()
        );

        $response = $this->actingAs($this->user)->get(route('companies.sections.edit', ['company' => $this->company, 'section' => $section]));

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     */
    public function test_update(): void
    {
        $section = $this->company->sections()->create(
            \App\Models\Section::factory()->make()->toArray()
        );

        $section_name = $this->faker->word;

        $response = $this->actingAs($this->user)->patch(route('companies.sections.update', ['company' => $this->company, 'section' => $section]), [
            'name' => $section_name,
        ]);

        $response->assertRedirect(route('companies.sections.show', ['company' => $this->company, 'section' => $section]));
        $this->assertDatabaseHas('sections', [
            'name' => $section_name,
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * A basic feature test example.
     */
    public function test_destroy(): void
    {
        $section = $this->company->sections()->create(
            \App\Models\Section::factory()->make()->toArray()
        );

        $response = $this->actingAs($this->user)->delete(route('companies.sections.destroy', ['company' => $this->company, 'section' => $section]));

        $response->assertRedirect(route('companies.sections.index', ['company' => $this->company]));

        $this->assertDatabaseMissing('sections', [
            'id' => $section->id,
            'deleted_at' => null,
        ]);
    }
}
