<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Game extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'white_player_id',
        'black_player_id',
        'status',
        'time_control',
        'initial_time_ms',
        'increment_ms',
        'result',
        'termination',
        'moves',
        'white_elo',
        'black_elo',
        'white_rd',
        'black_rd',
        'white_vol',
        'black_vol',
        'white_rating_change',
        'black_rating_change',
        'white_last_heartbeat_at',
        'black_last_heartbeat_at',
        'arena_id',
    ];

    protected function casts(): array
    {
        return [
            'initial_time_ms' => 'integer',
            'increment_ms' => 'integer',
            'moves' => 'array',
            'white_elo' => 'integer',
            'black_elo' => 'integer',
            'white_rd' => 'integer',
            'black_rd' => 'integer',
            'white_vol' => 'float',
            'black_vol' => 'float',
            'white_rating_change' => 'integer',
            'black_rating_change' => 'integer',
            'white_last_heartbeat_at' => 'datetime',
            'black_last_heartbeat_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Game $game) {
            if (empty($game->id)) {
                $game->id = Str::uuid();
            }
        });
    }

    public function whitePlayer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'white_player_id');
    }

    public function blackPlayer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'black_player_id');
    }

    public function isPlayer(int $userId): bool
    {
        return $this->white_player_id === $userId || $this->black_player_id === $userId;
    }

    public function getPlayerColor(int $userId): ?string
    {
        if ($this->white_player_id === $userId) return 'white';
        if ($this->black_player_id === $userId) return 'black';
        return null;
    }

    public function getOpponentId(int $userId): ?int
    {
        if ($this->white_player_id === $userId) return $this->black_player_id;
        if ($this->black_player_id === $userId) return $this->white_player_id;
        return null;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Keep basic heartbeat methods for connection tracking
    public function isPlayerAway(string $color): bool
    {
        if ($color === 'white') {
            return $this->white_last_heartbeat_at !== null
                && $this->white_last_heartbeat_at->diffInSeconds(now()) > 30;
        }
        return $this->black_last_heartbeat_at !== null
            && $this->black_last_heartbeat_at->diffInSeconds(now()) > 30;
    }

    /**
     * Format game data for frontend display.
     */
    public function toDisplayArray(?int $userId): array
    {
        return [
            'id' => $this->id,
            'white_player' => $this->whitePlayer ? [
                'id' => $this->whitePlayer->id,
                'name' => $this->whitePlayer->name,
                'rating' => $this->white_elo
            ] : null,
            'black_player' => $this->blackPlayer ? [
                'id' => $this->blackPlayer->id,
                'name' => $this->blackPlayer->name,
                'rating' => $this->black_elo
            ] : null,
            'status' => $this->status,
            'time_control' => $this->time_control,
            'initial_time_ms' => $this->initial_time_ms,
            'increment_ms' => $this->increment_ms,
            'result' => $this->result,
            'termination' => $this->termination,
            'moves' => $this->moves ?? [],
            'my_color' => $this->getPlayerColor($userId),
            'arena_id' => $this->arena_id,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
