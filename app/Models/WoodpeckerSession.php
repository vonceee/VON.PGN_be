<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WoodpeckerSession extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'puzzle_ids',
        'total_puzzles',
        'rating_min',
        'rating_max',
        'theme',
        'current_cycle_number',
        'status',
    ];

    protected $casts = [
        'puzzle_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cycles()
    {
        return $this->hasMany(WoodpeckerCycle::class, 'woodpecker_session_id');
    }
}
