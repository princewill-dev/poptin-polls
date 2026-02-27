<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Poll>
 */
class PollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'question' => fake()->sentence() . '?',
            'is_active' => true,
            'slug' => str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT),
        ];
    }
}
