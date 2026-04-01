<?php

namespace Database\Seeders;

use App\Models\Puzzle;
use Illuminate\Database\Seeder;

class PuzzleSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('puzzles_selected.json');
        $csvPath  = storage_path('app/lichess_db_puzzle.csv');

        if (file_exists($jsonPath)) {
            $this->seedFromJson($jsonPath);
        } elseif (file_exists($csvPath)) {
            $this->seedFromCsv($csvPath);
        } else {
            $this->command?->warn('No puzzle data found. Seeding 3 fallback puzzles.');
            $this->seedFallback();
        }
    }

    private function seedFromJson(string $path): void
    {
        $puzzles = json_decode(file_get_contents($path), true);

        if (empty($puzzles)) {
            $this->command?->warn('puzzles_selected.json is empty. Using fallback.');
            $this->seedFallback();
            return;
        }

        $this->command?->info('Importing ' . count($puzzles) . ' puzzles from JSON...');

        $imported = 0;
        foreach ($puzzles as $p) {
            Puzzle::updateOrCreate(
                ['lichess_puzzle_id' => $p['id']],
                [
                    'fen'    => $p['fen'],
                    'moves'  => $p['moves'],
                    'rating' => $p['rating'],
                    'themes' => $p['themes'],
                ]
            );
            $imported++;
        }

        $this->command?->info("Puzzles seeded: {$imported} (idempotent).");
    }

    private function seedFromCsv(string $csvPath): void
    {
        $this->command?->info('Importing puzzles from Lichess CSV...');

        $bands = [
            ['min' => 0,    'max' => 600,   'count' => 150],
            ['min' => 600,  'max' => 800,   'count' => 200],
            ['min' => 800,  'max' => 1000,  'count' => 250],
            ['min' => 1000, 'max' => 1200,  'count' => 300],
            ['min' => 1200, 'max' => 1400,  'count' => 300],
            ['min' => 1400, 'max' => 1600,  'count' => 250],
            ['min' => 1600, 'max' => 1800,  'count' => 200],
            ['min' => 1800, 'max' => 2000,  'count' => 150],
            ['min' => 2000, 'max' => 2300,  'count' => 120],
            ['min' => 2300, 'max' => 9999,  'count' => 80],
        ];

        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle);

        $colIndex  = array_flip($header);
        $idIdx     = $colIndex['PuzzleId'];
        $fenIdx    = $colIndex['FEN'];
        $movesIdx  = $colIndex['Moves'];
        $ratingIdx = $colIndex['Rating'];
        $popIdx    = $colIndex['Popularity'] ?? null;
        $playsIdx  = $colIndex['NbPlays'];
        $themesIdx = $colIndex['Themes'];

        $candidates = [];
        foreach ($bands as $i => $band) {
            $candidates[$i] = [];
        }

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 7) continue;

            $rating = (int) $row[$ratingIdx];
            $plays  = (int) $row[$playsIdx];

            if ($plays < 10) continue;

            foreach ($bands as $i => $band) {
                if ($rating >= $band['min'] && $rating < $band['max']) {
                    $score = $plays * 100 + (int) ($row[$popIdx ?? 5] ?? 50);
                    $puzzle = [
                        'id'     => $row[$idIdx],
                        'fen'    => $row[$fenIdx],
                        'moves'  => $row[$movesIdx],
                        'rating' => $rating,
                        'themes' => $row[$themesIdx],
                        'score'  => $score,
                    ];

                    if (count($candidates[$i]) < $band['count']) {
                        $candidates[$i][] = $puzzle;
                    } else {
                        $minIdx = 0;
                        $minScore = PHP_INT_MAX;
                        foreach ($candidates[$i] as $ci => $c) {
                            if ($c['score'] < $minScore) {
                                $minScore = $c['score'];
                                $minIdx = $ci;
                            }
                        }
                        if ($score > $minScore) {
                            $candidates[$i][$minIdx] = $puzzle;
                        }
                    }
                    break;
                }
            }
        }

        fclose($handle);

        $imported = 0;
        foreach ($candidates as $bandPuzzles) {
            foreach ($bandPuzzles as $p) {
                Puzzle::updateOrCreate(
                    ['lichess_puzzle_id' => $p['id']],
                    [
                        'fen'    => $p['fen'],
                        'moves'  => $p['moves'],
                        'rating' => $p['rating'],
                        'themes' => $p['themes'],
                    ]
                );
                $imported++;
            }
        }

        $this->command?->info("Puzzles seeded: {$imported} from CSV (idempotent).");
    }

    private function seedFallback(): void
    {
        $puzzles = [
            [
                'lichess_puzzle_id' => '00001',
                'fen'    => 'r1bqkbnr/pppp1ppp/2n5/4p3/2B1P3/5Q2/PPPP1PPP/RNB1K1NR w KQkq - 0 1',
                'moves'  => 'f3f7',
                'rating' => 900,
                'themes' => 'mate mateIn1 short',
            ],
            [
                'lichess_puzzle_id' => '00002',
                'fen'    => 'r2q1rk1/1pp2ppp/p1np1n2/2b1p1B1/2B1P1b1/P1NP1N2/1PP2PPP/R2Q1RK1 w - - 1 9',
                'moves'  => 'c3d5 c6d4 d5f6 g7f6',
                'rating' => 1200,
                'themes' => 'advantage middlegame short',
            ],
            [
                'lichess_puzzle_id' => '00003',
                'fen'    => '8/8/8/8/8/6k1/6p1/6K1 w - - 0 1',
                'moves'  => 'g1h1 g2h1q',
                'rating' => 1500,
                'themes' => 'promotion endgame',
            ],
        ];

        foreach ($puzzles as $p) {
            Puzzle::updateOrCreate(
                ['lichess_puzzle_id' => $p['lichess_puzzle_id']],
                $p
            );
        }

        $this->command?->info('Seeded 3 fallback puzzles.');
    }
}
