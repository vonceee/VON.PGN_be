<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Arena extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'start_date',
        'end_date',
        'time_control',
        'duration_minutes',
        'created_by',
        'current_participants',
        'winner',
        'standings',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'duration_minutes' => 'integer',
        'current_participants' => 'integer',
        'standings' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

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
}
