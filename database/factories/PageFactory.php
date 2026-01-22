<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'language_id' => $this->faker->randomElement([1, 2, 3]),
            'key' => $this->faker->word(),
            'content_md' => $this->faker->sentences(10, true),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
