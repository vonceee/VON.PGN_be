<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'short_info',
        'fide_rating',
        'email',
        'playing_experience',
        'teaching_experience',
        'teaching_methods',
        'bio',
        'location',
        'availability',
        'coaching_type',
        'twitter',
        'youtube',
        'twitch',
        'instagram',
        'facebook',
        'chesscom',
        'lichess',
        'profile_picture',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'playing_experience' => 'array',
            'teaching_experience' => 'array',
            'teaching_methods' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    // Scopes for filtering by status
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}