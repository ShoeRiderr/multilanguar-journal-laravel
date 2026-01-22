<?php

namespace Database\Factories;

use App\PostStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
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
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->slug(),
            'content_md' => $this->faker->sentences(10, true),
            'status' => $this->faker->randomElement([
                PostStatus::DRAFT->value,
                PostStatus::PUBLISHED->value,
                PostStatus::ARCHIVED->value,
            ]),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
