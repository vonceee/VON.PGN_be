<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model representing an individual game within a World Championship match.
 * 
 * WHY: Stores PGN moves, FEN start, metadata, and editorial sections.
 */
class WorldChampionshipGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'game_number',
        'slug',
        'title',
        'subtitle',
        'white_player',
        'black_player',
        'result',
        'game_date',
        'eco',
        'opening_name',
        'pgn',
        'initial_fen',
        'total_plies',
        'narrative_overview',
        'is_highlighted',
        'order',
    ];

    protected $casts = [
        'game_number' => 'integer',
        'game_date' => 'date',
        'total_plies' => 'integer',
        'is_highlighted' => 'boolean',
        'order' => 'integer',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(WorldChampionshipMatch::class, 'match_id');
    }

    /**
     * Editorial narrative story sections matching the two-column layout.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(WorldChampionshipGameSection::class, 'game_id')
            ->orderBy('order');
    }
}
