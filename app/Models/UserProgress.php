<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    protected $fillable = [
        'user_id',
        'completed_lesson_ids',
        'last_active_lesson_id',
        'total_puzzles_solved',
        'current_streak_days',
        'puzzle_rating',
        'puzzle_streak',
    ];

    protected $casts = [
        'completed_lesson_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
