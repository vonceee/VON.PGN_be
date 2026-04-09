<?php

use App\Jobs\CheckGameTimeJob;
use App\Jobs\SyncBroadcastsJob;
use App\Models\Game;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Broadcast sync: fetch live tournaments from Lichess every minute
Schedule::job(new SyncBroadcastsJob)
    ->everyMinute()
    ->withoutOverlapping()
    ->name('sync-broadcasts');

// Note: With Lichess-style clocks, timeout is only checked when a player makes a move
// or when sync-clock is called. No need for frequent scheduled checks.
// The client calculates time locally between server updates.
