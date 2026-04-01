<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'start_date',
        'end_date',
        'registration_deadline',
        'location',
        'latitude',
        'longitude',
        'format',
        'time_control',
        'entry_fee',
        'prize_pool',
        'organizer',
        'contact_email',
        'link',
        'description',
        'registration_instructions',
        'rounds',
        'current_participants',
        'max_participants',
        'eligibility',
        'categories',
        'schedule',
        'winner',
        'standings',
        'created_by',
        'view_count',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_deadline' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'rounds' => 'integer',
        'current_participants' => 'integer',
        'max_participants' => 'integer',
        'eligibility' => 'array',
        'categories' => 'array',
        'schedule' => 'array',
        'standings' => 'array',
        'view_count' => 'integer',
    ];

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopePast($query)
    {
        return $query->where('status', 'past');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tournament_bookmarks')->withTimestamps();
    }

    public function isBookmarkedBy(User $user): bool
    {
        return $this->bookmarkedBy()->where('user_id', $user->id)->exists();
    }
}
