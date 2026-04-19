<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Query;
use App\Models\AIResponse;

class QuerySeeder extends Seeder
{
    public function run(): void
    {
        Query::factory()
            ->count(50)
            ->create()
            ->each(function ($query) {

                // each query has 1–2 cached responses
                AIResponse::factory()
                    ->count(rand(1, 2))
                    ->create([
                        'query_id' => $query->id
                    ]);
            });
    }
}
