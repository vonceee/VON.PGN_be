<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MapsUrlResolverController extends Controller
{
    public function resolve(Request $request)
    {
        $request->validate([
            'url' => 'required|url|max:2048',
        ]);

        $url = $request->input('url');

        if (!preg_match('/maps\.app\.goo\.gl|goo\.gl\/maps/', $url)) {
            return response()->json(['message' => 'Not a shortened Google Maps URL'], 422);
        }

        $response = Http::withOptions([
            'allow_redirects' => false,
            'timeout' => 10,
        ])->head($url);

        if ($response->status() >= 300 && $response->status() < 400) {
            $resolvedUrl = $response->header('Location');
            return response()->json(['url' => $resolvedUrl]);
        }

        return response()->json(['message' => 'Could not resolve URL'], 400);
    }
}
