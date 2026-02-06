<?php

namespace Database\Factories;

use App\PostStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;
use App\Models\PostView;
use App\Models\Category;

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



    public function configure(): static
    {
        return $this->afterCreating(function (Post $post) {
            $post->postView()->save(
                PostView::factory()->make()
            );

            $post->categories()->attach(
                Category::inRandomOrder()->take(rand(1, 3))->pluck('id')
            );

            // Attach one media image to the post
            $post->media()->save(
                \Database\Factories\MediaFactory::new()->make()
            );
        });
    }
}
