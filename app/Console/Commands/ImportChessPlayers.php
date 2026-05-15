<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollectiblePlayer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportChessPlayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-chess-players 
                            {--limit=50 : Number of players to import} 
                            {--fide : Import using FIDE IDs}
                            {--ids= : Comma separated FIDE IDs}
                            {--dedupe : Remove duplicates and prefer entries with images}
                            {--prune : Remove players with no images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import chess players and their images from Wikipedia and Lichess FIDE API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting chess player import...');

        if ($this->option('prune')) {
            $this->prune();
            return;
        }

        if ($this->option('dedupe')) {
            $this->deduplicate();
            return;
        }

        if ($this->option('fide')) {
            $ids = $this->option('ids') ? explode(',', $this->option('ids')) : [
                '1503014', '2004887', '2020009', '35009192', '46616285', 
                '14204118', '12573981', '4168119', '8603677', '5202213',
                '24116068', '12502723', '4115325', '14103320', '8602980'
            ];

            foreach ($ids as $id) {
                $this->importFromFide($id);
            }
        } else {
            // 1. Historical Legends (Wikipedia Source)
            $legends = [
                'Magnus Carlsen', 'Garry Kasparov', 'Bobby Fischer', 'Mikhail Tal', 
                'Judit Polgar', 'Viswanathan Anand', 'Anatoly Karpov', 'Jose Raul Capablanca',
                'Alexander Alekhine', 'Paul Morphy', 'Mikhail Botvinnik', 'Tigran Petrosian',
                'Boris Spassky', 'Vladimir Kramnik', 'Veselin Topalov', 'Fabiano Caruana',
                'Hikaru Nakamura', 'Levon Aronian', 'Wesley So', 'Ding Liren', 'Anish Giri',
                'Ian Nepomniachtchi', 'Alireza Firouzja', 'Jan-Krzysztof Duda', 'Richard Rapport',
                'Shakhriyar Mamedyarov', 'Teimour Radjabov', 'Maxime Vachier-Lagrave', 'Vidit Gujrathi'
            ];

            foreach ($legends as $name) {
                $this->importFromWikipedia($name);
            }

            // Purge Lichess usernames that slipped in
            $this->purgeUsernames();
        }

        $this->info('Import completed!');
    }

    private function prune()
    {
        $count = CollectiblePlayer::whereNull('image_url')->orWhere('image_url', '')->delete();
        $this->info("Pruned {$count} players without images.");
    }

    private function deduplicate()
    {
        $players = CollectiblePlayer::all();
        $seen = [];
        $toDelete = [];

        foreach ($players as $player) {
            // Normalize name: "Carlsen, Magnus" -> "magnus carlsen"
            $normalized = $this->normalizeName($player->name);

            if (isset($seen[$normalized])) {
                $existing = $seen[$normalized];
                
                // Prefer the one with an image
                if (empty($existing->image_url) && !empty($player->image_url)) {
                    $toDelete[] = $existing->id;
                    $seen[$normalized] = $player;
                    $this->info("Found better version for {$normalized}, removing ID {$existing->id}");
                } else {
                    $toDelete[] = $player->id;
                    $this->info("Found duplicate for {$normalized}, removing ID {$player->id}");
                }
            } else {
                $seen[$normalized] = $player;
            }
        }

        if (!empty($toDelete)) {
            CollectiblePlayer::whereIn('id', $toDelete)->delete();
            $this->info("Successfully deleted " . count($toDelete) . " duplicates.");
        } else {
            $this->info("No duplicates found.");
        }
    }

    private function normalizeName($name)
    {
        $name = strtolower($name);
        if (str_contains($name, ',')) {
            $parts = explode(',', $name);
            return trim($parts[1]) . ' ' . trim($parts[0]);
        }
        return $name;
    }

    private function importFromFide($fideId)
    {
        $this->info("Fetching FIDE data for ID: {$fideId}");

        try {
            $response = Http::get("https://lichess.org/api/fide/player/{$fideId}");
            
            if ($response->successful()) {
                $data = $response->json();
                $name = $data['name'];
                
                // Get image - Lichess provides multiple sizes
                $imageUrl = $data['photo']['medium'] ?? $data['photo']['small'] ?? null;
                
                // Get best rating
                $rating = max(
                    $data['standard'] ?? 0,
                    $data['rapid'] ?? 0,
                    $data['blitz'] ?? 0
                );

                $rarity = 'Rare';
                if ($rating >= 2800) $rarity = 'Legendary';
                elseif ($rating >= 2730) $rarity = 'Epic';

                // Fetch bio from Wikipedia as fallback
                $bioResponse = Http::get("https://en.wikipedia.org/api/rest_v1/page/summary/" . urlencode($name));
                $bio = $bioResponse->successful() ? $bioResponse->json()['extract'] : "Official FIDE player: {$name}. Federation: {$data['federation']}.";

                CollectiblePlayer::updateOrCreate(
                    ['name' => $name],
                    [
                        'rarity' => $rarity,
                        'title' => $data['title'] ?? 'GM',
                        'bio' => $bio,
                        'image_url' => $imageUrl,
                        'peak_rating' => $rating,
                        'stats' => json_encode([
                            'fide_id' => $fideId,
                            'federation' => $data['federation'],
                            'birth_year' => $data['year'] ?? null,
                            'standard' => $data['standard'] ?? null,
                            'rapid' => $data['rapid'] ?? null,
                            'blitz' => $data['blitz'] ?? null,
                        ])
                    ]
                );

                $this->info("Successfully imported/updated FIDE player: {$name}");
            }
        } catch (\Exception $e) {
            $this->error("Failed to import FIDE ID {$fideId}: " . $e->getMessage());
        }
    }

    private function importFromWikipedia($name)
    {
        $this->info("Fetching Wikipedia data for: {$name}");

        try {
            // Fetch summary and image URL
            $response = Http::get("https://en.wikipedia.org/api/rest_v1/page/summary/" . urlencode($name));
            
            if ($response->successful()) {
                $data = $response->json();
                
                $rarity = $this->determineRarity($name);
                
                CollectiblePlayer::updateOrCreate(
                    ['name' => $name],
                    [
                        'rarity' => $rarity,
                        'title' => 'GM', // Most legends are GMs
                        'bio' => $data['extract'] ?? null,
                        'image_url' => $data['originalimage']['source'] ?? $data['thumbnail']['source'] ?? null,
                        'peak_rating' => $this->guessPeakRating($name),
                    ]
                );
                
                $this->info("Successfully imported/updated {$name}");
            }
        } catch (\Exception $e) {
            $this->error("Failed to import {$name}: " . $e->getMessage());
        }
    }

    private function purgeUsernames()
    {
        $this->info("Purging non-FIDE usernames...");
        
        // Remove players whose names don't have a space (common for usernames) 
        // OR have the Lichess avatar URL pattern
        $count = CollectiblePlayer::where('name', 'NOT LIKE', '% %')
            ->orWhere('image_url', 'LIKE', 'https://lichess1.org/avatar/%')
            ->delete();
            
        $this->info("Purged {$count} username-based entries.");
    }

    private function determineRarity($name)
    {
        $legendary = ['Magnus Carlsen', 'Garry Kasparov', 'Bobby Fischer', 'Mikhail Tal', 'Judit Polgar', 'Jose Raul Capablanca'];
        if (in_array($name, $legendary)) return 'Legendary';
        
        return 'Epic';
    }

    private function guessPeakRating($name)
    {
        $ratings = [
            'Magnus Carlsen' => 2882,
            'Garry Kasparov' => 2851,
            'Bobby Fischer' => 2785,
            'Judit Polgar' => 2735,
            'Viswanathan Anand' => 2817,
            'Fabiano Caruana' => 2844,
            'Hikaru Nakamura' => 2816,
        ];

        return $ratings[$name] ?? 2700;
    }
}
