<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Puzzle extends Model
{
    protected $fillable = ['lichess_puzzle_id', 'fen', 'moves', 'rating', 'themes'];
}
