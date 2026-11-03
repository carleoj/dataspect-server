<?php

namespace Database\Factories;

use App\Models\Dataset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dataset>
 */
class DatasetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    $name = fake()->randomElement(['customer_orders', 'web_sessions', 'survey_q3', 'sensor_readings']) . '.csv';

    return [
        'name'              => $name,
        'original_filename' => $name,
        'path'              => 'datasets/seed/' . $name,
        'size_bytes'        => fake()->numberBetween(500_000, 300_000_000),
        'row_count'         => fake()->numberBetween(1_000, 2_500_000),
        'column_count'      => fake()->numberBetween(4, 40),
        'status'            => 'ready',
        'health_score'      => fake()->numberBetween(55, 98),
        'options'           => ['delimiter' => ',', 'header' => true],
    ];
}
}
