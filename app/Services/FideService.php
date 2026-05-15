<?php

namespace App\Services;

use App\Models\FideFederation;
use App\Models\FidePlayer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class FideService
{
    private const MIRROR_URLS = [
        'standard' => 'https://github.com/elozero/fide-ratings-csv/releases/download/2026-05-08/fide_standard.csv',
        'rapid'    => 'https://github.com/elozero/fide-ratings-csv/releases/download/2026-05-08/fide_rapid.csv',
        'blitz'    => 'https://github.com/elozero/fide-ratings-csv/releases/download/2026-05-08/fide_blitz.csv',
    ];

    public function syncFederations(): void
    {
        // ... (existing code remains same)
    }

    public function downloadAndSyncPlayers(): void
    {
        foreach (self::MIRROR_URLS as $type => $url) {
            $this->syncRatingType($type, $url);
        }

        $this->updateFederationStats();
    }

    private function syncRatingType(string $type, string $url): void
    {
        $csvPath = storage_path("app/fide_{$type}.csv");

        $response = Http::withOptions([
            'stream' => true,
            'timeout' => 300,
            'follow_redirects' => true
        ])->get($url);
        
        if (!$response->successful()) {
            Log::error("Failed to download FIDE {$type} mirror: " . $response->status());
            return;
        }

        file_put_contents($csvPath, $response->getBody()->getContents());

        $handle = fopen($csvPath, "r");
        fgetcsv($handle); // skip header

        $batch = [];
        $batchSize = 2000;
        $validCodes = FideFederation::pluck('code')->toArray();
        $column = 'rating_' . $type;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 13) continue;

            $id = (int)$row[0];
            $name = $row[1] ?? '';
            $fed = $row[2] ?? '';
            $title = $row[4] ?? null;
            $rating = (int)($row[8] ?? 0) ?: null;
            $year = (int)($row[11] ?? 0) ?: null;
            $isActive = !str_contains($row[12] ?? '', 'i');

            if (!$id || strlen($name) < 3) continue;
            if (!in_array($fed, $validCodes)) continue;

            $batch[] = [
                'fide_id' => $id,
                'name' => $name,
                'federation_code' => $fed,
                'title' => $title,
                $column => $rating,
                'birth_year' => $year,
                'is_active' => $isActive,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                FidePlayer::upsert($batch, ['fide_id'], [
                    'name', 'federation_code', 'title', $column, 
                    'birth_year', 'is_active', 'updated_at'
                ]);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            FidePlayer::upsert($batch, ['fide_id'], [
                'name', 'federation_code', 'title', $column, 
                'birth_year', 'is_active', 'updated_at'
            ]);
        }

        fclose($handle);
    }

    private function updateFederationStats(): void
    {
        // ... (existing code remains same)
    }
}
