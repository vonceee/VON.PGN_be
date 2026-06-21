<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuessTheGameChallenge extends Model
{
    protected $fillable = [
        'white_player',
        'black_player',
        'event',
        'year',
        'eco',
        'result',
        'pgn',
        'active_date',
    ];

    protected $casts = [
        'year' => 'integer',
        'active_date' => 'date',
    ];
}
