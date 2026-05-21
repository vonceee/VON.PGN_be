<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuzzleAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'puzzle_id',
        'success',
        'rating_change',
        'user_rating_after',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function puzzle()
    {
        return $this->belongsTo(Puzzle::class);
    }
}
