<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class LichessApiService
{
    private const LICHESS_API_BASE = 'https://lichess.org/api';
    private const TIMEOUT = 6;
    private const RATE_LIMIT_BACKOFF = 60; // seconds

    /**
     * Get list of ongoing broadcasts
     */
    public function getOngoingBroadcasts(): array
    {
        return Cache::remember('lichess_broadcasts_ongoing', 30, function () {
            try {
                $response = Http::timeout(self::TIMEOUT)
                    ->get(self::LICHESS_API_BASE . '/broadcast');

                if ($response->failed()) {
                    Log::warning('Lichess API failed to fetch broadcasts', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    return [];
                }

                $body = $response->body();
                $broadcasts = [];
                
                // Parse NDJSON (newline-delimited JSON)
                $lines = explode("\n", trim($body));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    
                    $data = json_decode($line, true);
                    if (isset($data['tour'])) {
                        $tour = $data['tour'];
                        
                        // Determine status based on dates and rounds
                        $status = 'upcoming';
                        $now = now()->timestamp * 1000;
                        
                        if (!empty($tour['dates']) && isset($tour['dates'][1])) {
                            $endDate = $tour['dates'][1];
                            if ($now > $endDate) {
                                $status = 'finished';
                            } elseif (!empty($tour['dates'][0]) && $now >= $tour['dates'][0]) {
                                $status = 'ongoing';
                            }
                        }
                        
                        // Check if any round is ongoing
                        if (isset($tour['rounds'])) {
                            foreach ($tour['rounds'] as $round) {
                                if (isset($round['finished']) && $round['finished']) {
                                    // Round finished, check if there's an ongoing one
                                    continue;
                                }
                                if (isset($round['startsAt']) && isset($round['finished'])) {
                                    $status = 'ongoing';
                                    break;
                                }
                            }
                        }
                        
                        // Extract base name for grouping (everything before " | " or " : ")
                        $baseName = $tour['name'];
                        if (preg_match('/^(.*?)\s*[|:]\s*/', $tour['name'], $matches)) {
                            $baseName = trim($matches[1]);
                        }
                        
                        $broadcasts[] = [
                            'id' => $tour['id'],
                            'slug' => $tour['slug'],
                            'name' => $tour['name'],
                            'baseName' => $baseName,
                            'description' => $tour['description'] ?? $tour['info']['description'] ?? null,
                            'url' => $tour['url'],
                            'tier' => $tour['tier'] ?? null,
                            'status' => $status,
                            'startedAt' => isset($tour['dates'][0]) ? $tour['dates'][0] : null,
                            'endedAt' => isset($tour['dates'][1]) ? $tour['dates'][1] : null,
                            'rounds' => $tour['rounds'] ?? [],
                            'info' => $tour['info'] ?? null,
                            'dates' => $tour['dates'] ?? [],
                            'image' => $tour['image'] ?? null,
                            'createdAt' => $tour['createdAt'] ?? null,
                            'website' => $tour['info']['website'] ?? null,
                        ];
                    }
                }
                
                return $broadcasts;
            } catch (Exception $e) {
                Log::error('Lichess API exception fetching broadcasts', [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                ]);
                return [];
            }
        });
    }

    /**
     * Get broadcast detail by ID with rounds
     */
    public function getBroadcastDetail(string $broadcastId): ?array
    {
        // First try the direct endpoint
        $cacheKey = "lichess_broadcast_{$broadcastId}";

        return Cache::remember($cacheKey, 10, function () use ($broadcastId) {
            try {
                $response = Http::timeout(self::TIMEOUT)
                    ->get(self::LICHESS_API_BASE . "/broadcast/{$broadcastId}");

                if ($response->failed()) {
                    Log::warning("Lichess API failed to fetch broadcast {$broadcastId}", [
                        'status' => $response->status(),
                    ]);
                    return null;
                }

                $body = $response->body();
                $lines = explode("\n", trim($body));
                
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    
                    $data = json_decode($line, true);
                    if (isset($data['tour'])) {
                        return $data['tour'];
                    }
                    // If it has rounds directly, return it
                    if (isset($data['rounds'])) {
                        return $data;
                    }
                }
                
                return null;
            } catch (Exception $e) {
                Log::error("Lichess API exception fetching broadcast {$broadcastId}", [
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    /**
     * Get broadcast detail by ID including rounds (fetches from full list)
     */
    public function getBroadcastDetailWithRounds(string $broadcastId): ?array
    {
        $cacheKey = "lichess_broadcast_full_{$broadcastId}";

        return Cache::remember($cacheKey, 5, function () use ($broadcastId) {
            try {
                // Fetch the full broadcast list and find our broadcast
                $response = Http::timeout(self::TIMEOUT)
                    ->get(self::LICHESS_API_BASE . '/broadcast');

                if ($response->failed()) {
                    Log::warning("Failed to fetch broadcast list for {$broadcastId}");
                    return null;
                }

                $body = $response->body();
                $lines = explode("\n", trim($body));
                
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    
                    $data = json_decode($line, true);
                    
                    // Check both formats: tour.id or direct id
                    $tourData = null;
                    $roundsData = null;
                    
                    if (isset($data['tour']) && isset($data['tour']['id']) && $data['tour']['id'] === $broadcastId) {
                        $tourData = $data['tour'];
                        $roundsData = $data['tour']['rounds'] ?? [];
                    } elseif (isset($data['id']) && $data['id'] === $broadcastId) {
                        // Direct format without 'tour' wrapper
                        $tourData = $data;
                        $roundsData = $data['rounds'] ?? [];
                    }
                    
                    if ($tourData) {
                        // Add rounds to the tour data
                        $tourData['rounds'] = $roundsData;
                        return $tourData;
                    }
                }
                
                return null;
            } catch (Exception $e) {
                Log::error("Lichess API exception fetching broadcast with rounds {$broadcastId}", [
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    /**
     * Get broadcast round detail
     */
    public function getBroadcastRound(string $tourSlug, string $roundSlug): ?array
    {
        $cacheKey = "lichess_broadcast_round_{$tourSlug}_{$roundSlug}";

        return Cache::remember($cacheKey, 10, function () use ($tourSlug, $roundSlug) {
            try {
                $response = Http::timeout(self::TIMEOUT)
                    ->get(self::LICHESS_API_BASE . "/broadcast/by/{$tourSlug}/{$roundSlug}");

                if ($response->failed()) {
                    Log::warning("Lichess API failed to fetch round {$tourSlug}/{$roundSlug}", [
                        'status' => $response->status(),
                    ]);
                    return null;
                }

                return $response->json();
            } catch (Exception $e) {
                Log::error("Lichess API exception fetching round {$tourSlug}/{$roundSlug}", [
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    /**
     * Get broadcast leaderboard
     */
    public function getBroadcastLeaderboard(string $broadcastId): ?array
    {
        $cacheKey = "lichess_broadcast_leaderboard_{$broadcastId}";

        return Cache::remember($cacheKey, 15, function () use ($broadcastId) {
            try {
                $response = Http::timeout(self::TIMEOUT)
                    ->get("https://lichess.org/broadcast/{$broadcastId}/leaderboard");

                if ($response->failed()) {
                    Log::warning("Lichess API failed to fetch leaderboard for {$broadcastId}", [
                        'status' => $response->status(),
                    ]);
                    return null;
                }

                return $response->json();
            } catch (Exception $e) {
                Log::error("Lichess API exception fetching leaderboard for {$broadcastId}", [
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    /**
     * Get broadcast as PGN
     */
    public function getBroadcastPgn(string $broadcastId): ?string
    {
        $cacheKey = "lichess_broadcast_pgn_{$broadcastId}";

        return Cache::remember($cacheKey, 5, function () use ($broadcastId) {
            try {
                $response = Http::timeout(self::TIMEOUT)
                    ->get(self::LICHESS_API_BASE . "/broadcast/{$broadcastId}.pgn");

                if ($response->failed()) {
                    Log::warning("Lichess API failed to fetch PGN for {$broadcastId}", [
                        'status' => $response->status(),
                    ]);
                    return null;
                }

                return $response->body();
            } catch (Exception $e) {
                Log::error("Lichess API exception fetching PGN for {$broadcastId}", [
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    /**
     * Get round games as PGN
     */
    public function getRoundPgn(string $tourSlug, string $roundSlug): ?string
    {
        $cacheKey = "lichess_round_pgn_{$tourSlug}_{$roundSlug}";

        return Cache::remember($cacheKey, 5, function () use ($tourSlug, $roundSlug) {
            try {
                $response = Http::timeout(self::TIMEOUT)
                    ->get(self::LICHESS_API_BASE . "/broadcast/by/{$tourSlug}/{$roundSlug}.pgn");

                if ($response->failed()) {
                    Log::warning("Lichess API failed to fetch round PGN {$tourSlug}/{$roundSlug}", [
                        'status' => $response->status(),
                    ]);
                    return null;
                }

                return $response->body();
            } catch (Exception $e) {
                Log::error("Lichess API exception fetching round PGN {$tourSlug}/{$roundSlug}", [
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    /**
     * Get round PGN by round ID (from the broadcast/{id} endpoint)
     */
    public function getRoundPgnById(string $broadcastId, string $roundId): ?string
    {
        $cacheKey = "lichess_round_pgn_{$roundId}";

        return Cache::remember($cacheKey, 5, function () use ($broadcastId, $roundId) {
            try {
                // Use the broadcast endpoint with round ID
                $response = Http::timeout(self::TIMEOUT)
                    ->get(self::LICHESS_API_BASE . "/broadcast/{$broadcastId}");

                if ($response->failed()) {
                    return null;
                }

                // Parse NDJSON to find the round
                $body = $response->body();
                $lines = explode("\n", trim($body));
                
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    
                    $data = json_decode($line, true);
                    if (isset($data['tour']['rounds'])) {
                        $rounds = $data['tour']['rounds'];
                        
                        // Find the round with matching ID
                        foreach ($rounds as $round) {
                            if (isset($round['id']) && $round['id'] === $roundId) {
                                // Fetch PGN for this round
                                return $this->getRoundPgn($data['tour']['slug'], $round['slug']);
                            }
                        }
                    }
                }
                
                return null;
            } catch (Exception $e) {
                Log::error("Lichess API exception fetching round PGN by ID {$roundId}", [
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    /**
     * Clear specific broadcast cache
     */
    public function clearBroadcastCache(string $broadcastId = null): void
    {
        if ($broadcastId) {
            Cache::forget("lichess_broadcast_{$broadcastId}");
            Cache::forget("lichess_broadcast_leaderboard_{$broadcastId}");
            Cache::forget("lichess_broadcast_pgn_{$broadcastId}");
        } else {
            Cache::forget('lichess_broadcasts_ongoing');
        }
    }

    /**
     * Check if we're rate limited by Lichess API
     */
    public function isRateLimited(): bool
    {
        return Cache::has('lichess_api_rate_limited');
    }

    /**
     * Mark that we've been rate limited
     */
    public function setRateLimited(): void
    {
        Cache::put('lichess_api_rate_limited', true, self::RATE_LIMIT_BACKOFF);
        Log::warning('Lichess API rate limit hit. Backing off for ' . self::RATE_LIMIT_BACKOFF . ' seconds.');
    }
}
