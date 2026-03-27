<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'banner_image',
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
        'description',
        'rounds',
        'current_participants',
        'max_participants',
        'eligibility',
        'categories',
        'schedule',
        'winner',
        'standings',
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
}
