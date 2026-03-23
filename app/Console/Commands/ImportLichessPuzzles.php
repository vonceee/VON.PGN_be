<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Puzzle;
use Illuminate\Support\Facades\DB;

class ImportLichessPuzzles extends Command
{
    protected $signature = 'puzzles:import {filepath} {--limit=1000}';

    protected $description = 'Import puzzles from the official Lichess CSV database';

    public function handle()
    {
        $filepath = $this->argument('filepath');
        $limit = $this->option('limit');

        if (!file_exists($filepath)) {
            $this->error("File not found at: {$filepath}");
            return;
        }

        $this->info("Starting import of up to {$limit} puzzles...");

        $file = fopen($filepath, 'r');
        $count = 0;
        $batch = [];

        // Lichess CSV format: 
        // PuzzleId, FEN, Moves, Rating, RatingDeviation, Popularity, NbPlays, Themes, GameUrl, OpeningTags

        // skip the header row if it exists
        $firstLine = fgets($file);
        if (!str_contains($firstLine, 'PuzzleId')) {
            rewind($file); // no header, start from beginning
        }

        while (($data = fgetcsv($file)) !== FALSE) {
            if ($count >= $limit) break;

            // map the CSV columns to our database schema
            $batch[] = [
                'lichess_puzzle_id' => $data[0],
                'fen' => $data[1],
                'moves' => $data[2],
                'rating' => (int) $data[3],
                'themes' => $data[7],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $count++;

            // Insert in chunks of 500 so we don't crash the server's RAM
            if ($count % 500 === 0) {
                DB::table('puzzles')->insertOrIgnore($batch);
                $batch = [];
                $this->info("Imported {$count} puzzles...");
            }
        }

        // Insert any remaining puzzles
        if (!empty($batch)) {
            DB::table('puzzles')->insertOrIgnore($batch);
        }

        fclose($file);
        $this->info("✅ Successfully imported {$count} puzzles!");
    }
}
