<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Puzzle extends Model
{
    protected $fillable = [
        'lichess_puzzle_id',
        'fen',
        'moves',
        'rating',
        'themes',
        'game_url',
        'opening_tags',
        'popularity',
        'nb_plays',
        'rating_deviation',
    ];
}
