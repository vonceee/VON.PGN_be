<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameSeek extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'time_control',
        'elo',
    ];

    protected function casts(): array
    {
        return [
            'elo' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
