<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Query;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'query_id' => Query::factory(),

            'used_cached' => $this->faker->boolean(70),
            'tokens_used' => $this->faker->numberBetween(50, 300),
        ];
    }
}
