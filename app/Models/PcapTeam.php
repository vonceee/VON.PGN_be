<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Eloquent model for a PCAP Team Franchise.
 */
class PcapTeam extends Model
{
    protected $table = 'pcap_teams';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'conference',
        'wins',
        'losses',
        'draws',
        'match_points',
        'board_points_for',
        'board_points_against',
    ];

    protected $casts = [
        'wins' => 'integer',
        'losses' => 'integer',
        'draws' => 'integer',
        'match_points' => 'integer',
        'board_points_for' => 'float',
        'board_points_against' => 'float',
    ];

    /**
     * Team roster players relation.
     */
    public function roster(): HasMany
    {
        return $this->hasMany(PcapPlayer::class, 'team_id')
            ->orderByRaw("CASE category WHEN 'O' THEN 1 WHEN 'L' THEN 2 WHEN 'S' THEN 3 WHEN 'HG' THEN 4 WHEN 'HG/S' THEN 5 WHEN 'HG/L' THEN 6 ELSE 7 END")
            ->orderByRaw("rating IS NULL, rating DESC");
    }

    /**
     * Standing relation.
     */
    public function standing(): HasOne
    {
        return $this->hasOne(PcapStanding::class, 'team_id');
    }

    /**
     * Transform model to camelCase array matching Angular PcapTeam interface.
     */
    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'conference' => $this->conference,
            'wins' => (int) $this->wins,
            'losses' => (int) $this->losses,
            'draws' => (int) $this->draws,
            'matchPoints' => (int) $this->match_points,
            'boardPointsFor' => (float) $this->board_points_for,
            'boardPointsAgainst' => (float) $this->board_points_against,
            'roster' => $this->roster ? $this->roster->map->toFrontendArray()->values()->all() : [],
        ];
    }
}
