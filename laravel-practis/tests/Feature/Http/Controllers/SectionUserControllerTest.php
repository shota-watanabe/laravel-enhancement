<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Company;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SectionUserControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();

        $this->section = Section::factory([
            'company_id' => $this->company->id,
        ])->create();

        $this->user = User::factory([
            'company_id' => $this->company->id,
        ])->create();
    }

    public function test_store()
    {
        $response = $this->actingAs($this->user)->post(route('sections.users.store', ['company' => $this->company, 'section' => $this->section]), [
            'user_id' => $this->user->id,
        ]);

        $response->assertStatus(302);
    }


    public function test_destroy()
    {
        $this->section->users()->attach($this->user->id);

        $response = $this->actingAs($this->user)->delete(route('sections.users.destroy', ['company' => $this->company, 'section' => $this->section, 'user' => $this->user]));

        $response->assertStatus(302);
    }
}
