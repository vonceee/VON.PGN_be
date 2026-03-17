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
        'experience_points',
        'current_level',
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

    public function gainExperience(int $amount): bool
    {
        $this->experience_points += $amount;
        $leveledUp = false;

        $xpForNextLevel = $this->current_level * 100;

        while ($this->experience_points >= $xpForNextLevel) {
            $this->current_level += 1;
            $this->experience_points -= $xpForNextLevel;
            $leveledUp = true;

            $xpForNextLevel = $this->current_level * 100;
        }

        $this->save();

        return $leveledUp;
    }
}
