<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Query;
use App\Models\UserRequest;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    { 
        $this->call([
            UserSeeder::class,
            QuerySeeder::class,
            LanguageSeeder::class,
            CategorySeeder::class,
            PostSeeder::class,
            PageSeeder::class,
        ]);

        $users = User::all();
        $queries = Query::all();

        // simulate real usage
        foreach ($users as $user) {
            UserRequest::factory()
                ->count(rand(5, 15))
                ->create([
                    'user_id' => $user->id,
                    'query_id' => $queries->random()->id,
                ]);
        }
    }
}
