<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostView>
 */
class PostViewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'view_count' => $this->faker->numberBetween(0, 1000),
            'last_viewed_at' => $this->faker->date(),
        ];
    }
}
