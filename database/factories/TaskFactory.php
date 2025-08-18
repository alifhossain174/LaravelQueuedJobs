<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
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
            'name'          => "Simple Task", //$this->faker->sentence(3),
            'description'   => "Do the Task", //$this->faker->paragraph,
            'assigned_user' => $this->faker->email, // or user_id if you have users
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }
}
