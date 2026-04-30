<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LichessProxyController extends Controller
{
    public function pgn(Request $request)
    {
        $url = $request->query('url');

        if (!$url) {
            return response()->json(['message' => 'Missing url parameter'], 400);
        }

        $parsed = parse_url($url);
        $host = $parsed['host'] ?? '';

        if (!in_array($host, ['lichess.org', 'www.lichess.org'])) {
            return response()->json(['message' => 'Only lichess.org URLs are allowed'], 400);
        }

        if (!str_ends_with($url, '.pgn')) {
            $url .= '.pgn';
        }

        $response = Http::timeout(15)->get($url);

        if ($response->failed()) {
            return response()->json(['message' => 'Failed to fetch PGN from Lichess'], $response->status());
        }

        return response($response->body(), 200)->header('Content-Type', 'text/plain');
    }

    public function explorer(Request $request, $db = 'lichess')
    {
        $baseUrl = "https://explorer.lichess.ovh/{$db}";
        
        // Strictly filter parameters based on the target database
        // Masters API only supports a subset of parameters
        if ($db === 'masters') {
            $params = $request->only(['fen', 'play', 'moves', 'topGames']);
        } else {
            $params = $request->only([
                'variant', 'fen', 'play', 'since', 'until', 'speeds', 'ratings', 
                'player', 'color', 'modes', 'topGames', 'recentGames'
            ]);
        }

        $token = config('services.lichess.token');
        $requestBuilder = Http::withHeaders([
            'User-Agent' => 'VON.CHESS/1.0 (contact: support@vonchess.com)'
        ]);

        if ($token) {
            $requestBuilder->withToken($token);
        }

        $response = $requestBuilder->timeout(10)->get($baseUrl, $params);

        if ($response->failed()) {
            $status = $response->status();
            
            // Log the error for the developer
            \Illuminate\Support\Facades\Log::error("Lichess Proxy Error: {$db}", [
                'status' => $status,
                'url' => $baseUrl,
                'params' => $params,
                'response' => $response->json() ?? $response->body()
            ]);

            // Map 401/403 from Lichess to 502 to avoid triggering our app's logout interceptor
            if ($status === 401 || $status === 403) {
                $status = 502;
            }
            return response()->json([
                'message' => 'Failed to fetch data from Lichess Explorer',
                'lichess_status' => $response->status(),
                'db' => $db
            ], $status);
        }

        return response($response->body(), 200)->header('Content-Type', 'application/json');
    }

    public function tablebase(Request $request, $variant = 'standard')
    {
        $baseUrl = "https://tablebase.lichess.ovh/{$variant}";
        $params = $request->only(['fen']);

        $token = config('services.lichess.token');
        $requestBuilder = Http::withHeaders([
            'User-Agent' => 'VON.CHESS/1.0 (contact: support@vonchess.com)'
        ]);

        if ($token) {
            $requestBuilder->withToken($token);
        }

        $response = $requestBuilder->timeout(10)->get($baseUrl, $params);

        if ($response->failed()) {
            $status = $response->status();
            if ($status === 401 || $status === 403) {
                $status = 502;
            }
            return response()->json([
                'message' => 'Failed to fetch data from Lichess Tablebase',
                'lichess_status' => $response->status()
            ], $status);
        }

        return response($response->body(), 200)->header('Content-Type', 'application/json');
    }
}
