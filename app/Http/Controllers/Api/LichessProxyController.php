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
}
