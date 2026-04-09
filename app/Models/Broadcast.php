<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Broadcast extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'slug',
        'name',
        'description',
        'url',
        'tier',
        'status',
        'started_at',
        'ended_at',
        'synced_at',
    ];

    protected $casts = [
        'tier' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'synced_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all rounds for this broadcast
     */
    public function rounds(): HasMany
    {
        return $this->hasMany(BroadcastRound::class);
    }

    /**
     * Scope to get upcoming broadcasts
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    /**
     * Scope to get ongoing broadcasts
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    /**
     * Scope to get finished broadcasts
     */
    public function scopeFinished($query)
    {
        return $query->where('status', 'finished');
    }

    /**
     * Check if broadcast needs refresh from Lichess API
     * Ongoing broadcasts refresh every minute, others every hour
     */
    public function isStale(): bool
    {
        if ($this->status === 'ongoing') {
            return !$this->synced_at || $this->synced_at->diffInSeconds(now()) > 60;
        }

        return !$this->synced_at || $this->synced_at->diffInSeconds(now()) > 3600;
    }

    /**
     * Update broadcast from Lichess API response
     */
    public static function updateFromLichess(array $data): self
    {
        // Handle the new format from NDJSON (single broadcast object)
        $broadcastData = $data;
        if (isset($data['tour'])) {
            $broadcastData = $data['tour'];
        }

        $startedAt = null;
        $endedAt = null;
        
        if (isset($broadcastData['dates']) && is_array($broadcastData['dates'])) {
            $startedAt = isset($broadcastData['dates'][0]) 
                ? Carbon::createFromTimestampMs($broadcastData['dates'][0]) 
                : null;
            $endedAt = isset($broadcastData['dates'][1]) 
                ? Carbon::createFromTimestampMs($broadcastData['dates'][1]) 
                : null;
        }

        $broadcast = self::updateOrCreate(
            ['id' => $broadcastData['id']],
            [
                'slug' => $broadcastData['slug'] ?? str($broadcastData['name'] ?? 'broadcast')->slug(),
                'name' => $broadcastData['name'],
                'description' => $broadcastData['description'] ?? $broadcastData['info']['description'] ?? null,
                'url' => $broadcastData['url'] ?? "https://lichess.org/broadcast/{$broadcastData['id']}",
                'tier' => $broadcastData['tier'] ?? null,
                'status' => self::determineStatus($broadcastData),
                'started_at' => $startedAt,
                'ended_at' => $endedAt,
                'synced_at' => now(),
            ]
        );

        return $broadcast;
    }

    /**
     * Create broadcast from NDJSON format (simplified)
     */
    public static function createFromNdjson(array $tour): self
    {
        $startedAt = isset($tour['dates'][0]) 
            ? Carbon::createFromTimestampMs($tour['dates'][0]) 
            : null;
        $endedAt = isset($tour['dates'][1]) 
            ? Carbon::createFromTimestampMs($tour['dates'][1]) 
            : null;

        return self::updateOrCreate(
            ['id' => $tour['id']],
            [
                'slug' => $tour['slug'] ?? str($tour['name'] ?? 'broadcast')->slug(),
                'name' => $tour['name'],
                'description' => $tour['description'] ?? null,
                'url' => $tour['url'] ?? "https://lichess.org/broadcast/{$tour['id']}",
                'tier' => $tour['tier'] ?? null,
                'status' => self::determineStatus($tour),
                'started_at' => $startedAt,
                'ended_at' => $endedAt,
                'synced_at' => now(),
            ]
        );
    }

    /**
     * Determine status from Lichess API data
     */
    private static function determineStatus(array $data): string
    {
        // Check for finished rounds in the data
        if (isset($data['rounds'])) {
            $hasOngoing = false;
            $allFinished = true;
            
            foreach ($data['rounds'] as $round) {
                if (isset($round['finished']) && $round['finished']) {
                    continue;
                }
                // Round not finished
                $allFinished = false;
                if (isset($round['startsAt'])) {
                    $now = now()->timestamp * 1000;
                    // If the round hasn't started yet, it's upcoming
                    if ($round['startsAt'] > $now) {
                        return 'upcoming';
                    }
                    $hasOngoing = true;
                }
            }
            
            if ($allFinished) {
                return 'finished';
            }
            if ($hasOngoing) {
                return 'ongoing';
            }
        }

        // Fallback to old logic
        if (isset($data['isFinished']) && $data['isFinished']) {
            return 'finished';
        }

        if (isset($data['isOngoing']) && $data['isOngoing']) {
            return 'ongoing';
        }
        
        // Check dates for status
        if (isset($data['dates']) && is_array($data['dates'])) {
            $now = now()->timestamp * 1000;
            if (isset($data['dates'][1]) && $now > $data['dates'][1]) {
                return 'finished';
            }
            if (isset($data['dates'][0]) && $now >= $data['dates'][0]) {
                return 'ongoing';
            }
            return 'upcoming';
        }

        return 'upcoming';
    }

    /**
     * Get the URL to view this broadcast on Lichess
     */
    public function getLichessUrl(): string
    {
        return $this->url ?: "https://lichess.org/broadcast/{$this->id}";
    }

    /**
     * Format the broadcast for API response
     */
    public function toApiResponse(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'url' => $this->getLichessUrl(),
            'tier' => $this->tier,
            'status' => $this->status,
            'started_at' => $this->started_at?->toIso8601String(),
            'ended_at' => $this->ended_at?->toIso8601String(),
            'synced_at' => $this->synced_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
