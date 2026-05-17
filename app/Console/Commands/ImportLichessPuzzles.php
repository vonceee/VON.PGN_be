<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportLichessPuzzles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'puzzles:import {filepath} {--limit= : Limit the number of puzzles to import} {--truncate : Clear the puzzles table before importing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import puzzles from the official Lichess CSV database with full column mapping';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filepath = $this->argument('filepath');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $truncate = $this->option('truncate');

        if (!file_exists($filepath)) {
            $this->error("File not found at: {$filepath}");
            return 1;
        }

        // 1. Memory and Execution Time Optimizations
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        DB::disableQueryLog();

        // 2. Handle Table Truncation to Prevent Duplicates and Optimize Speed
        if ($truncate) {
            $this->warn("⚠️ Truncating the 'puzzles' table to start fresh...");
            DB::table('puzzles')->truncate();
            $this->info("Puzzles table truncated successfully.");
        }

        if ($limit !== null) {
            $this->info("Starting import of up to {$limit} puzzles from {$filepath}...");
        } else {
            $this->info("Starting import of ALL puzzles from {$filepath}...");
        }

        $file = fopen($filepath, 'r');
        $count = 0;
        $batch = [];
        $chunkSize = 3000; // Perfect sweet-spot below the 65,535 parameters limit

        // Skip the header row if it exists
        $firstLine = fgets($file);
        if (!str_contains($firstLine, 'PuzzleId')) {
            rewind($file); // no header, start from beginning
        }

        $now = now()->toDateTimeString();

        while (($data = fgetcsv($file)) !== FALSE) {
            if ($limit !== null && $count >= $limit) {
                break;
            }

            // Standard Lichess CSV columns: 
            // 0: PuzzleId
            // 1: FEN
            // 2: Moves
            // 3: Rating
            // 4: RatingDeviation
            // 5: Popularity
            // 6: NbPlays
            // 7: Themes
            // 8: GameUrl
            // 9: OpeningTags

            $batch[] = [
                'lichess_puzzle_id' => $data[0] ?? null,
                'fen'               => $data[1] ?? null,
                'moves'             => $data[2] ?? null,
                'rating'            => isset($data[3]) ? (int) $data[3] : 0,
                'rating_deviation'  => isset($data[4]) ? (int) $data[4] : null,
                'popularity'        => isset($data[5]) ? (int) $data[5] : null,
                'nb_plays'          => isset($data[6]) ? (int) $data[6] : null,
                'themes'            => $data[7] ?? null,
                'game_url'          => $data[8] ?? null,
                'opening_tags'      => $data[9] ?? null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];

            $count++;

            // Insert in chunks to optimize database index and query parsing speed
            if ($count % $chunkSize === 0) {
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
        return 0;
    }
}
