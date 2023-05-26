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
        Company::factory()->count(5)->create()
            ->each(function (Company $company) {
                $company->sections()->createMany(
                    \App\Models\Section::factory()->count(10)->make()->toArray()
                );
            });
    }
}
