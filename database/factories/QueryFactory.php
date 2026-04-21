<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QueryFactory extends Factory
{
    public function definition(): array
    {
        $text = $this->faker->sentence(4);

        $normalized = strtolower(trim($text));
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        return [
            'query_text' => $text,
            'normalized_query' => $normalized,
            'query_hash' => hash('sha256', $normalized),
        ];
    }
}
