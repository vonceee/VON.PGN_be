<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use App\Services\LichessApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BroadcastController extends Controller
{
    private LichessApiService $lichessApi;

    public function __construct(LichessApiService $lichessApi)
    {
        $this->lichessApi = $lichessApi;
    }

    /**
     * Get list of broadcasts (merged from DB and live Lichess API)
     */
    public function index(Request $request): JsonResponse
    {
        // Check if we want ungrouped broadcasts (for category dropdown)
        if ($request->get('include_all') === 'true') {
            $lichessBroadcasts = [];
            if (!$this->lichessApi->isRateLimited()) {
                $lichessBroadcasts = $this->lichessApi->getOngoingBroadcasts();
            }
            return response()->json([
                'lichess' => $lichessBroadcasts,
            ])->header('Access-Control-Allow-Origin', '*');
        }

        // Fetch broadcasts from database
        $query = Broadcast::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $localBroadcasts = $query->orderBy('synced_at', 'desc')
            ->limit(50)
            ->get();

        // Fetch from Lichess API
        $lichessBroadcasts = [];
        if (!$this->lichessApi->isRateLimited()) {
            $lichessBroadcasts = $this->lichessApi->getOngoingBroadcasts();
        }

        // Group by baseName to combine related broadcasts
        $grouped = [];
        foreach ($lichessBroadcasts as $broadcast) {
            $baseName = $broadcast['baseName'] ?? $broadcast['name'];
            if (!isset($grouped[$baseName])) {
                $grouped[$baseName] = $broadcast;
                // Use baseName as displayName
                $grouped[$baseName]['displayName'] = $baseName;
            }
        }

        // Format and merge
        $localFormatted = $localBroadcasts->map(fn($b) => $b->toApiResponse())->toArray();
        $lichessFormatted = array_values($grouped);

        return response()->json([
            'data' => $localFormatted,
            'lichess' => $lichessFormatted,
        ])->header('Access-Control-Allow-Origin', '*')
           ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
           ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    /**
     * Get broadcast detail by ID or slug
     */
    public function show(string $identifier): JsonResponse
    {
        // Try to find in local database first
        $broadcast = Broadcast::where('id', $identifier)
            ->orWhere('slug', $identifier)
            ->first();

        if ($broadcast) {
            return response()->json([
                'broadcast' => $broadcast->toApiResponse(),
                'rounds' => $broadcast->rounds()->latest('synced_at')->limit(10)->get()
                    ->map(fn($r) => $r->toApiResponse())->toArray(),
            ])->header('Access-Control-Allow-Origin', '*')
               ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
               ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        // Fallback to Lichess API - get from the ongoing broadcasts list
        if (!$this->lichessApi->isRateLimited()) {
            $broadcasts = $this->lichessApi->getOngoingBroadcasts();
            
            // Find the broadcast with matching ID
            foreach ($broadcasts as $b) {
                if ($b['id'] === $identifier) {
                    $rounds = $b['rounds'] ?? [];
                    unset($b['rounds']); // Remove from broadcast to avoid duplication
                    
                    return response()->json([
                        'broadcast' => $b,
                        'rounds' => $rounds,
                    ])->header('Access-Control-Allow-Origin', '*')
                       ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                       ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
                }
            }
        }

        return response()->json(['message' => 'Broadcast not found'], 404)
            ->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Get live games for a broadcast (current PGN)
     */
    public function live(string $broadcastId): JsonResponse
    {
        // First try to get from local database
        $broadcast = Broadcast::find($broadcastId);

        $pgn = null;
        $syncedAt = null;

        if ($broadcast) {
            $pgn = $this->lichessApi->getBroadcastPgn($broadcastId);
            $syncedAt = $broadcast->synced_at;
        }

        // If no PGN from DB, try Lichess API directly
        if (!$pgn && !$this->lichessApi->isRateLimited()) {
            // Get the broadcast details to find the current round
            $lichessData = $this->lichessApi->getBroadcastDetail($broadcastId);
            
            if ($lichessData) {
                // Find the current/default round (not finished)
                $currentRound = null;
                if (isset($lichessData['rounds'])) {
                    foreach ($lichessData['rounds'] as $round) {
                        if (!isset($round['finished']) || !$round['finished']) {
                            $currentRound = $round;
                            break;
                        }
                    }
                }
                
                // If we have a round, get its PGN
                if ($currentRound && isset($currentRound['slug'])) {
                    $pgn = $this->lichessApi->getRoundPgn($lichessData['slug'], $currentRound['slug']);
                } else {
                    // Fallback to broadcast PGN
                    $pgn = $this->lichessApi->getBroadcastPgn($broadcastId);
                }
                $syncedAt = now();
            }
        }

        if ($pgn) {
            return response()->json([
                'broadcast_id' => $broadcastId,
                'pgn' => $pgn,
                'synced_at' => $syncedAt,
            ])->header('Access-Control-Allow-Origin', '*')
               ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
               ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        return response()->json(['message' => 'Broadcast not found'], 404)->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Get leaderboard for a broadcast
     */
    public function leaderboard(string $broadcastId): JsonResponse
    {
        if ($this->lichessApi->isRateLimited()) {
            return response()->json(['message' => 'Service temporarily unavailable'], 503)
                ->header('Access-Control-Allow-Origin', '*');
        }

        $leaderboard = $this->lichessApi->getBroadcastLeaderboard($broadcastId);

        if (!$leaderboard) {
            return response()->json(['message' => 'Leaderboard not found'], 404)
                ->header('Access-Control-Allow-Origin', '*');
        }

        return response()->json($leaderboard)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    /**
     * Get round detail by tour slug and round slug
     */
    public function round(string $tourSlug, string $roundSlug): JsonResponse
    {
        if ($this->lichessApi->isRateLimited()) {
            return response()->json(['message' => 'Service temporarily unavailable'], 503);
        }

        $round = $this->lichessApi->getBroadcastRound($tourSlug, $roundSlug);

        if (!$round) {
            return response()->json(['message' => 'Round not found'], 404);
        }

        return response()->json($round);
    }

    /**
     * Get round PGN by tour slug and round slug
     */
    public function roundPgn(string $tourSlug, string $roundSlug): JsonResponse
    {
        if ($this->lichessApi->isRateLimited()) {
            return response()->json(['message' => 'Service temporarily unavailable'], 503);
        }

        $pgn = $this->lichessApi->getRoundPgn($tourSlug, $roundSlug);

        if (!$pgn) {
            return response()->json(['message' => 'Round not found'], 404);
        }

        // Return PGN as plain text
        return response($pgn, 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Get round PGN by round ID (new endpoint)
     */
    public function roundPgnById(string $roundId): \Illuminate\Http\Response
    {
        // First, get the broadcast to find round info
        $lichessData = $this->lichessApi->getOngoingBroadcasts();
        
        foreach ($lichessData as $broadcastData) {
            if (isset($broadcastData['rounds'])) {
                foreach ($broadcastData['rounds'] as $round) {
                    if (isset($round['id']) && $round['id'] === $roundId) {
                        $pgn = $this->lichessApi->getRoundPgn($broadcastData['slug'], $round['slug']);
                        if ($pgn) {
                            return response($pgn, 200)
                                ->header('Content-Type', 'text/plain');
                        }
                    }
                }
            }
        }

        return response('Round not found', 404)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Stream live games using Server-Sent Events (SSE)
     * Much more efficient than polling - real-time updates from Lichess
     */
    public function stream(string $broadcastId): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->stream(function () use ($broadcastId) {
            // Send initial heartbeat to flush headers
            echo ":heartbeat\n\n";
            flush();

            // Send initial data
            echo "event: broadcast\n";
            echo "data: " . json_encode([
                'id' => $broadcastId,
                'status' => 'streaming',
            ]) . "\n\n";
            flush();

            // Send heartbeats every 10 seconds for 1 minute
            for ($i = 0; $i < 6; $i++) {
                sleep(10);
                echo ":heartbeat\n\n";
                flush();
            }

            // Stream end
            echo "event: end\n";
            echo "data: {\"message\":\"closed\"}\n\n";
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    /**
     * Extract moves from PGN
     */
    private function extractMoves(string $pgn): array
    {
        $moves = [];
        $lines = explode("\n", $pgn);

        foreach ($lines as $line) {
            if (strpos($line, '[') === 0) continue; // Skip headers
            if (trim($line) === '') continue;

            // Extract algebraic notation
            preg_match_all('/([KQRBN]?[a-h]?[1-8]?x?[a-h][1-8](?:=[QRBN])?)([\+#])?/', $line, $matches);
            if (isset($matches[1])) {
                $moves = array_merge($moves, $matches[1]);
            }
        }

        return $moves;
    }
}
