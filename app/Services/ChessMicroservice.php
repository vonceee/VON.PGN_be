<?php

namespace App\Services;

use App\Models\Game;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ChessMicroservice
{
    private string $microserviceUrl;

    public function __construct()
    {
        // Use 127.0.0.1 for development to avoid IPv6/IPv4 resolution issues
        $defaultUrl = app()->environment('local')
            ? 'http://127.0.0.1:3006'
            : 'https://von-pgn-microservice.onrender.com';

        $this->microserviceUrl = env('CHESS_MICROSERVICE_URL', $defaultUrl);
    }

    /**
     * Helper to fetch game state from microservice and handle synchronization.
     * Includes retries for cold-start scenarios where microservice is waking up.
     */
    public function fetchGameState(Game $game): ?array
    {
        $url = $this->microserviceUrl . '/api/games/' . $game->id;
        $maxRetries = 5;
        $lastError = null;
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                Log::info('Fetching game state from microservice', [
                    'game_id' => $game->id,
                    'attempt' => $attempt,
                    'url' => $url
                ]);
                
                // Longer timeouts for cold-start: 5s, 10s, 15s, 15s, 15s
                $timeout = $attempt < 4 ? $attempt * 5 : 15;
                $response = Http::timeout($timeout)
                    ->withHeaders(['X-Internal-Secret' => config('services.chess.internal_secret')])
                    ->get($url);

                if ($response->successful()) {
                    $gameData = $response->json();
                    
                    // Authoritative Sync: If microservice says game is done (completed/aborted), update Laravel DB
                    $isFinished = in_array($gameData['status'] ?? '', ['completed', 'aborted']);
                    if ($isFinished && $game->status !== $gameData['status']) {
                        $this->finalizeGame($game, $gameData);
                    }
                    
                    return $gameData;
                }

                if ($response->status() === 404) {
                    Log::warning('Game missing from microservice. Marking as abandoned in DB.', [
                        'game_id' => $game->id
                    ]);
                    
                    $game->update([
                        'status' => 'completed',
                        'result' => null,
                        'termination' => 'abandoned'
                    ]);
                    
                    return null;
                }

                Log::error('Microservice returned HTTP error', [
                    'game_id' => $game->id,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                throw new \Exception('Microservice returned HTTP ' . $response->status());
                
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $lastError = $e->getMessage();
                Log::warning('Microservice connection failed (attempt ' . $attempt . '/' . $maxRetries . ')', [
                    'game_id' => $game->id,
                    'error' => $e->getMessage()
                ]);
                
                if ($attempt < $maxRetries) {
                    usleep($attempt * 2000000); // 2s, 4s, 6s, 8s backoff
                }
            }
        }
        
        Log::error('Microservice communication failed after retries', [
            'game_id' => $game->id,
            'last_error' => $lastError
        ]);
        
        throw new HttpException(503, 'Chess microservice is currently unavailable. Please try again later.');
    }

    /**
     * Call microservice endpoint with retry logic for cold-start scenarios.
     */
    public function callWithRetry(string $endpoint, array $data, string $method = 'POST'): bool
    {
        $url = $this->microserviceUrl . $endpoint;
        $maxRetries = 5;
        $lastError = null;
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                Log::info('Calling microservice', [
                    'endpoint' => $endpoint,
                    'attempt' => $attempt,
                    'method' => $method
                ]);
                
                $timeout = $attempt < 4 ? $attempt * 5 : 15;
                $response = $method === 'POST' 
                    ? Http::timeout($timeout)
                        ->withHeaders(['X-Internal-Secret' => config('services.chess.internal_secret')])
                        ->post($url, $data)
                    : Http::timeout($timeout)
                        ->withHeaders(['X-Internal-Secret' => config('services.chess.internal_secret')])
                        ->get($url);

                if ($response->successful()) {
                    return true;
                }

                Log::error('Microservice returned HTTP error', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return false;
                
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $lastError = $e->getMessage();
                Log::warning('Microservice connection failed (attempt ' . $attempt . '/' . $maxRetries . ')', [
                    'endpoint' => $endpoint,
                    'error' => $e->getMessage()
                ]);
                
                if ($attempt < $maxRetries) {
                    usleep($attempt * 2000000);
                }
            } catch (\Exception $e) {
                Log::error('Microservice call failed', [
                    'endpoint' => $endpoint,
                    'error' => $e->getMessage()
                ]);
                return false;
            }
        }
        
        return false;
    }

    /**
     * Sync game status and trigger events.
     */
    private function finalizeGame(Game $game, array $gameData): void
    {
        $game->update([
            'status' => $gameData['status'],
            'result' => $gameData['result'] ?? null,
            'termination' => $gameData['termination'] ?? null,
        ]);
        
    }

    /**
     * Get the base microservice URL.
     */
    public function getUrl(): string
    {
        return $this->microserviceUrl;
    }
}
