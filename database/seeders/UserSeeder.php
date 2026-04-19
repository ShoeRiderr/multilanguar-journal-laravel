<?php

namespace Database\Seeders;

use App\Models\User;
use App\UserRole;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(10)
            ->state(new Sequence(
                [
                    'role' => UserRole::ADMIN->value,
                    'is_paid' => rand(0, 1),
                    'free_trial_used' => rand(1, 10),
                ],
                ['role' => UserRole::USER->value],
            ))
            ->create();
        
    }
}
