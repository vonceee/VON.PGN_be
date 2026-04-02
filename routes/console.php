<?php

use App\Jobs\CheckGameTimeJob;
use App\Models\Game;
use App\Models\GameSeek;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    GameSeek::where('created_at', '<', now()->subMinutes(10))->delete();
})->everyMinute();

// Note: With Lichess-style clocks, timeout is only checked when a player makes a move
// or when sync-clock is called. No need for frequent scheduled checks.
// The client calculates time locally between server updates.
