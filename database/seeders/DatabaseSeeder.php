<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ChessBasicsSeeder::class,
            OpeningPrinciplesSeeder::class,
            MiddlegamePrinciplesSeeder::class,
            EndgamePrinciplesSeeder::class,
            PuzzleSeeder::class,
        ]);
    }
}
