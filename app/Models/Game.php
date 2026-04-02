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
        'initial_fen',
        'current_fen',
        'white_time_remaining_ms',
        'black_time_remaining_ms',
        'last_move_timestamp',
        'white_first_move_at',
        'black_first_move_at',
        'clock_start_at',
        'turn',
        'moves',
        'result',
        'termination',
        'white_elo',
        'black_elo',
        'draw_offered_by',
        'draw_offered_at',
    ];

    protected function casts(): array
    {
        return [
            'moves' => 'array',
            'last_move_timestamp' => 'datetime',
            'white_first_move_at' => 'datetime',
            'black_first_move_at' => 'datetime',
            'clock_start_at' => 'datetime',
            'draw_offered_at' => 'datetime',
            'white_time_remaining_ms' => 'integer',
            'black_time_remaining_ms' => 'integer',
            'initial_time_ms' => 'integer',
            'increment_ms' => 'integer',
            'white_elo' => 'integer',
            'black_elo' => 'integer',
            'draw_offered_by' => 'integer',
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

    public function getTimeRemainingForColor(string $color): int
    {
        return $color === 'white'
            ? $this->white_time_remaining_ms
            : $this->black_time_remaining_ms;
    }

    public function clearDrawOffer(): void
    {
        $this->update([
            'draw_offered_by' => null,
            'draw_offered_at' => null,
        ]);
    }

    public function hasActiveDrawOffer(): bool
    {
        return $this->draw_offered_by !== null && $this->draw_offered_at !== null;
    }

    public function isDrawOfferOnCooldown(int $userId): bool
    {
        if (!$this->draw_offered_at) {
            return false;
        }

        return $this->draw_offered_at->addSeconds(30)->isFuture();
    }

    public function hasBothFirstMoves(): bool
    {
        return $this->white_first_move_at !== null && $this->black_first_move_at !== null;
    }
}
