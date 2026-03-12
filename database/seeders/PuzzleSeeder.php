<?php

namespace Database\Seeders;

use App\Models\Puzzle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PuzzleSeeder extends Seeder
{
    public function run()
    {
        Puzzle::create([
            'lichess_puzzle_id' => '00001',
            'fen' => 'r1bqkbnr/pppp1ppp/2n5/4p3/2B1P3/5Q2/PPPP1PPP/RNB1K1NR w KQkq - 0 1',
            'moves' => 'f3f7', // Scholar's Mate!
            'rating' => 900,
            'themes' => 'mate mateIn1 short',
        ]);

        Puzzle::create([
            'lichess_puzzle_id' => '00002',
            'fen' => 'r2q1rk1/1pp2ppp/p1np1n2/2b1p1B1/2B1P1b1/P1NP1N2/1PP2PPP/R2Q1RK1 w - - 1 9',
            'moves' => 'c3d5 c6d4 d5f6 g7f6',
            'rating' => 1200,
            'themes' => 'advantage middlegame short',
        ]);

        Puzzle::create([
            'lichess_puzzle_id' => '00003',
            'fen' => '8/8/8/8/8/6k1/6p1/6K1 w - - 0 1',
            'moves' => 'g1h1 g2h1q', // Promotion
            'rating' => 1500,
            'themes' => 'promotion endgame',
        ]);
    }
}
