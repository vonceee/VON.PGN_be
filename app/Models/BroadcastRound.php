<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BroadcastRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'broadcast_id',
        'lichess_round_id',
        'games_data',
        'games_count',
        'synced_at',
    ];

    protected $casts = [
        'games_data' => 'json',
        'games_count' => 'integer',
        'synced_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the broadcast this round belongs to
     */
    public function broadcast(): BelongsTo
    {
        return $this->belongsTo(Broadcast::class);
    }

    /**
     * Check if round data needs refresh from Lichess API
     */
    public function isStale(): bool
    {
        return !$this->synced_at || $this->synced_at->diffInSeconds(now()) > 5;
    }

    /**
     * Update round from Lichess API response
     */
    public static function updateFromLichess(string $broadcastId, array $roundData): self
    {
        $round = self::updateOrCreate(
            [
                'broadcast_id' => $broadcastId,
                'lichess_round_id' => $roundData['id'],
            ],
            [
                'games_data' => $roundData['games'] ?? [],
                'games_count' => count($roundData['games'] ?? []),
                'synced_at' => now(),
            ]
        );

        return $round;
    }

    /**
     * Format the round for API response
     */
    public function toApiResponse(): array
    {
        return [
            'id' => $this->lichess_round_id,
            'games_count' => $this->games_count,
            'games' => $this->games_data,
            'synced_at' => $this->synced_at?->toIso8601String(),
        ];
    }
}
