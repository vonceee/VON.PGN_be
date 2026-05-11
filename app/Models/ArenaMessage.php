<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArenaMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'arena_id',
        'user_id',
        'body',
    ];

    /**
     * Get the arena that owns the message.
     */
    public function arena(): BelongsTo
    {
        return $this->belongsTo(Arena::class);
    }

    /**
     * Get the user who sent the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
