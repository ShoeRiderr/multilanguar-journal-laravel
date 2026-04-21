<?php

namespace Database\Factories;

use App\Models\Query;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiResponseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'query_id' => Query::factory(),

            'response_json' => [
                'topic' => $this->faker->word(),
                'summary' => $this->faker->sentence(),
                'details' => [
                    [
                        'title' => $this->faker->word(),
                        'description' => $this->faker->sentence(),
                    ],
                ],
                'examples' => [$this->faker->sentence()],
                'difficulty' => $this->faker->randomElement([
                    'beginner', 'intermediate', 'advanced'
                ]),
            ],

            'schema_version' => 1,
            'model_used' => 'gpt-5.3',
            'tokens_used' => $this->faker->numberBetween(50, 300),
            'prompt_version' => 1,

            'expires_at' => now()->addDays(30),
        ];
    }
}
