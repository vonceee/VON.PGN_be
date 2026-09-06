<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model representing a World Chess Championship match duel.
 * 
 * WHY: Powers the dedicated championship matchup pages, historical records,
 *      and featured annotated games.
 */
class WorldChampionshipMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'year',
        'display_year',
        'title',
        'champion',
        'challenger',
        'winner',
        'score',
        'format',
        'location',
        'era',
        'description',
        'key_highlights',
        'games_count',
        'study_id',
        'is_published',
        'view_count',
    ];

    protected $casts = [
        'year' => 'integer',
        'key_highlights' => 'array',
        'games_count' => 'integer',
        'is_published' => 'boolean',
        'view_count' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Games belonging to this world championship match.
     */
    public function games(): HasMany
    {
        return $this->hasMany(WorldChampionshipGame::class, 'match_id')
            ->orderBy('order')
            ->orderBy('game_number');
    }

    /**
     * The primary highlighted / immortal game of the match.
     */
    public function highlightedGame()
    {
        return $this->hasOne(WorldChampionshipGame::class, 'match_id')
            ->where('is_highlighted', true);
    }

    /**
     * Associated interactive study (if linked).
     */
    public function study(): BelongsTo
    {
        return $this->belongsTo(Study::class, 'study_id');
    }
}
