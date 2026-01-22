<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::factory()->count(3)->state(
            new Sequence(
                [
                    'code' => 'en',
                    'name' => 'English',
                    'native_name' => 'English',
                    'is_active' => true,
                    'is_default' => true,
                ],
                [
                    'code' => 'pl',
                    'name' => 'Polish',
                    'native_name' => 'Polski',
                    'is_active' => true,
                    'is_default' => false,
                ],
                [
                    'code' => 'de',
                    'name' => 'German',
                    'native_name' => 'Deutsch',
                    'is_active' => true,
                    'is_default' => false,
                ],
            )
         )->create();
    }
}
