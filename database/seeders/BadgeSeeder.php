<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $badges = [
            [
                'name' => 'First Steps',
                'description' => 'Completed your first lesson.',
                'image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Novice Player',
                'description' => 'Reached Level 2.',
                'image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Scholar',
                'description' => 'Completed 5 lessons.',
                'image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Intermediate Player',
                'description' => 'Reached Level 5.',
                'image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Advanced Player',
                'description' => 'Reached Level 10.',
                'image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Grandmaster Scholar',
                'description' => 'Completed 20 lessons.',
                'image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tactician',
                'description' => 'Solved 10 puzzles.',
                'image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($badges as $badge) {
            // Upsert or insert ignoring duplicates by name
            DB::table('badges')->updateOrInsert(
                ['name' => $badge['name']],
                $badge
            );
        }
    }
}
