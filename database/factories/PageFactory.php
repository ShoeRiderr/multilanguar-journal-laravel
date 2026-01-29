<?php

namespace Database\Factories;

use App\Models\PageTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use App\Models\Page;

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
            'is_active' => $this->faker->boolean(80),
        ];
    }


    public function configure(): static
    {
        return $this->afterCreating(function (Page $page) {
            $page->pageTranslations()->saveMany(
                PageTranslation::factory()
                    ->count(3)
                    ->state(new Sequence(
                        [
                            'language_id' => 1,
                            'page_id' => $page->id
                        ],
                        [
                            'language_id' => 2,
                            'page_id' => $page->id
                        ],
                        [
                            'language_id' => 3,
                            'page_id' => $page->id
                        ],
                    ))
                    ->make()
            );
        });
    }
}
