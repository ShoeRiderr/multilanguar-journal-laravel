<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PageTranslation;
use App\Models\Page;
use App\Models\Language;

/**
 * @extends Factory<PageTranslation>
 */
class PageTranslationFactory extends Factory
{
    protected $model = PageTranslation::class;

    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'language_id' => $this->faker->randomElement([1, 2, 3]),
            'title' => $this->faker->words(2, true),
            'slug' => $this->faker->slug(2),
            'content_md' => $this->faker->paragraph(3),
        ];
    }
}
