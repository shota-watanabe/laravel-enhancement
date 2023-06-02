<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CsvExportHistory>
 */
class CsvExportHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'download_user_id' => function () {
                return User::query()->inRandomOrder()->first()->id;
            },
            'file_name' => $this->faker->unique()->word . '.csv',
        ];
    }
}
