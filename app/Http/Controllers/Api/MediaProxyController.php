<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaProxyController extends Controller
{
    /**
     * Serve tournament media with correct CORS headers for Konva.js/Canvas use.
     */
    public function serve(string $type, string $filename)
    {
        // Allowed types based on our upload logic
        if (!in_array($type, ['backgrounds', 'logos', 'posters', 'coaches'])) {
            abort(404);
        }

        $path = ($type === 'coaches') ? "coaches/{$filename}" : "tournaments/{$type}/{$filename}";

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('public')->get($path);
        $mime = Storage::disk('public')->mimeType($path);

        return response($file, 200)
            ->header('Content-Type', $mime)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With')
            ->header('Cache-Control', 'public, max-age=31536000');
    }
}
