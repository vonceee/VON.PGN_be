<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CalculatePuzzleThemeCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'puzzles:calculate-themes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pre-calculate counts for all tactical puzzle themes and save them to a static JSON file';

    /**
     * Compute aggregated tactical puzzle theme counts using a high-performance database sweep.
     *
     * Architectural Choice:
     * - Disables SQL query logs and scales PHP execution limits.
     * - Sweeps the `puzzles` table using sequential primary-key ranges (`chunkById`) in batch windows of 50,000 records.
     * - Aggregates counts of space-separated themes in memory using a fast PHP associative array, bypassing SQL parsing.
     * - Serializes the result to a static local JSON cache file and populates the long-lived Laravel Cache.
     *
     * Alternatives Considered:
     * - Eloquent `chunk()`: Rejected due to severe memory overhead of hydrating 5.5M+ model instances.
     * - Standard `chunk()` with SQL OFFSET: Rejected due to O(N^2) complexity at deep database offsets.
     * - MySQL String Splitting: Rejected because MySQL lacks native, performant regex-splitting functions for space-separated data.
     *
     * @return int Exit code (0 for success).
     *
     * Assumptions & Edge Cases:
     * - Assumes the database contains records and the `id` column is a sequentially increasing autoincrement integer.
     * - Safely ignores puzzles that have empty or null themes.
     *
     * // CRITICAL: Must use chunkById instead of chunk. Standard chunk employs SQL OFFSET, which degrades exponentially to O(N^2) complexity on a 5.5M+ row dataset, locking database connections.
     * // TRADEOFF: Aggregates theme strings inside PHP RAM to shield the database from intensive text tokenization and parsing operations.
     */
    public function handle()
    {
        $this->info('Starting high-performance puzzle theme counts calculation...');
        $start = microtime(true);

        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        DB::disableQueryLog();

        $counts = [];
        $totalProcessed = 0;

        DB::table('puzzles')
            ->select('id', 'themes')
            ->chunkById(50000, function ($puzzles) use (&$counts, &$totalProcessed) {
                foreach ($puzzles as $puzzle) {
                    if (!$puzzle->themes) {
                        continue;
                    }

                    $themes = explode(' ', $puzzle->themes);
                    foreach ($themes as $theme) {
                        $theme = trim($theme);
                        if ($theme !== '') {
                            $counts[$theme] = ($counts[$theme] ?? 0) + 1;
                        }
                    }
                }

                $totalProcessed += count($puzzles);
                $this->info("Processed {$totalProcessed} puzzles...");
            });

        $jsonContent = json_encode($counts, JSON_PRETTY_PRINT);
        Storage::disk('local')->put('puzzle_theme_counts.json', $jsonContent);

        $duration = microtime(true) - $start;
        $this->info("✅ Successfully calculated theme counts!");
        $this->info("Processed {$totalProcessed} puzzles in " . round($duration, 2) . " seconds.");
        $this->info("Found " . count($counts) . " unique themes.");
        $this->info("Saved to: " . Storage::path('puzzle_theme_counts.json'));

        $cacheKey = 'puzzle_theme_counts:' . app()->environment();
        \Illuminate\Support\Facades\Cache::put($cacheKey, $counts, 86400 * 30);
        $this->info("Populated cache key: {$cacheKey}");

        return 0;
    }
}
