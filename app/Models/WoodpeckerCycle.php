<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WoodpeckerCycle extends Model
{
    protected $fillable = [
        'woodpecker_session_id',
        'cycle_number',
        'status',
        'current_puzzle_index',
        'start_time',
        'end_time',
        'total_solved',
        'total_correct',
        'total_time_seconds',
        'attempts',
    ];

    protected $casts = [
        'attempts' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(WoodpeckerSession::class, 'woodpecker_session_id');
    }
}
