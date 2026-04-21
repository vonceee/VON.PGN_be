<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tournament;
use App\Models\Arena;
use Illuminate\Http\Response;
use Carbon\Carbon;

class SitemapController extends Controller
{
    public function dynamic(): Response
    {
        $baseUrl = 'https://vonchess.net';
        $now = Carbon::now();

        // 1. Users: Active in last 30 days
        $users = User::where('last_game_at', '>=', $now->copy()->subDays(30))
            ->select('id', 'updated_at')
            ->get();

        // 2. Tournaments: Upcoming/Active + Completed < 7 days old
        $tournaments = Tournament::whereIn('status', ['upcoming', 'ongoing'])
            ->orWhere(function ($query) use ($now) {
                $query->where('status', 'past')
                    ->where('end_date', '>=', $now->copy()->subDays(7));
            })
            ->select('id', 'updated_at')
            ->get();

        // 3. Arenas: Upcoming/Active + Completed < 7 days old
        $arenas = Arena::whereIn('status', ['upcoming', 'ongoing'])
            ->orWhere(function ($query) use ($now) {
                $query->where('status', 'past')
                    ->where('end_date', '>=', $now->copy()->subDays(7));
            })
            ->select('id', 'updated_at')
            ->get();

        $content = view('sitemaps.dynamic', [
            'baseUrl' => $baseUrl,
            'users' => $users,
            'tournaments' => $tournaments,
            'arenas' => $arenas,
        ])->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }
}
