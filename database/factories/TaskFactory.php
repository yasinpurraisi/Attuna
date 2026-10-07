<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Tune;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tune_id' => Tune::factory(),
            'description' => fake()->sentence(),
            'completed' => false,
        ];
    }
}
