<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Eloquent model for a PCAP Division Standing.
 */
class PcapStanding extends Model
{
    protected $table = 'pcap_standings';

    protected $fillable = [
        'team_id',
        'conference',
        'matches_played',
        'won',
        'drawn',
        'lost',
        'match_points',
        'board_points_for',
        'board_points_against',
        'board_points_diff',
        'form',
        'rank',
    ];

    protected $casts = [
        'matches_played' => 'integer',
        'won' => 'integer',
        'drawn' => 'integer',
        'lost' => 'integer',
        'match_points' => 'integer',
        'board_points_for' => 'float',
        'board_points_against' => 'float',
        'board_points_diff' => 'float',
        'form' => 'array',
        'rank' => 'integer',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(PcapTeam::class, 'team_id');
    }

    /**
     * Transform to camelCase array matching Angular PcapStanding interface.
     */
    public function toFrontendArray(): array
    {
        return [
            'teamId' => $this->team_id,
            'teamName' => $this->team?->name ?? '',
            'conference' => $this->conference,
            'matchesPlayed' => (int) $this->matches_played,
            'won' => (int) $this->won,
            'drawn' => (int) $this->drawn,
            'lost' => (int) $this->lost,
            'matchPoints' => (int) $this->match_points,
            'boardPointsFor' => (float) $this->board_points_for,
            'boardPointsAgainst' => (float) $this->board_points_against,
            'boardPointsDiff' => (float) $this->board_points_diff,
            'form' => $this->form ?? [],
            'rank' => (int) $this->rank,
        ];
    }
}
