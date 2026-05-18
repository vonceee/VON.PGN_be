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
     * Parse and stream the official Lichess puzzles CSV dataset into the local database.
     *
     * Architectural Choice:
     * - Disables SQL query logs and extends PHP execution & memory configurations for batch processing.
     * - Uses buffered streaming CSV parsing (`fgetcsv`) to keep a flat O(1) memory profile.
     * - Performs parameterized batch insertions using raw `DB::table()->insertOrIgnore` rather than Eloquent.
     * - Triggers static theme cache compilation at the end of execution to restore fast API loads.
     *
     * Alternatives Considered:
     * - Eloquent Model hydration: Rejected as creating millions of Model instances exhausts PHP RAM limits.
     * - Standard `DB::insert`: Rejected because `insertOrIgnore` is required to tolerate duplicate Lichess IDs safely.
     * - Laravel database seeding chunk methods: Rejected as CSV streaming is more memory-efficient.
     *
     * @return int Exit code (0 for success, 1 for failure).
     *
     * Assumptions & Edge Cases:
     * - Assumes the target CSV has the standard Lichess schema with at least 10 fields.
     * - Tolerates empty files, nonexistent file paths, and skips files with standard column headers gracefully.
     *
     * // CRITICAL: Database query logs must be disabled using DB::disableQueryLog() to prevent PHP memory exhaustion leaks during massive imports.
     * // TRADEOFF: A batch chunk size of 3000 is utilized. While larger batch sizes reduce transactional roundtrips, 3000 is the mathematical sweet spot to avoid the MySQL 65,535 statement placeholder parameter threshold (10 columns * 3000 rows = 30,000 placeholders).
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

        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        DB::disableQueryLog();

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
        $chunkSize = 3000;

        $firstLine = fgets($file);
        if (!str_contains($firstLine, 'PuzzleId')) {
            rewind($file);
        }

        $now = now()->toDateTimeString();

        while (($data = fgetcsv($file)) !== FALSE) {
            if ($limit !== null && $count >= $limit) {
                break;
            }

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

            if ($count % $chunkSize === 0) {
                DB::table('puzzles')->insertOrIgnore($batch);
                $batch = [];
                $this->info("Imported {$count} puzzles...");
            }
        }

        if (!empty($batch)) {
            DB::table('puzzles')->insertOrIgnore($batch);
        }

        fclose($file);

        $this->call('puzzles:calculate-themes');

        $this->info("✅ Successfully imported {$count} puzzles!");
        return 0;
    }
}
