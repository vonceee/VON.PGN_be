<?php

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
