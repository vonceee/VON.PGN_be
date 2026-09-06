<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model representing an editorial narrative section within a Championship Game.
 * 
 * WHY: Powers the left-column story text (e.g. "Grabbing the center", "Development over material")
 *      with move ranges and tactical explanations.
 */
class WorldChampionshipGameSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'title',
        'content',
        'start_ply',
        'end_ply',
        'key_move_san',
        'order',
    ];

    protected $casts = [
        'start_ply' => 'integer',
        'end_ply' => 'integer',
        'order' => 'integer',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(WorldChampionshipGame::class, 'game_id');
    }
}
