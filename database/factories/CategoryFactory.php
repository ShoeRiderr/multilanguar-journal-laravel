<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Category $category) {
            $category->categoryTranslations()->saveMany(
                CategoryTranslation::factory()
                    ->count(3)
                    ->state(new Sequence(
                        ['language_id' => 1],
                        ['language_id' => 2],
                        ['language_id' => 3],
                    ))
                    ->make()
            );
        });
    }
}
