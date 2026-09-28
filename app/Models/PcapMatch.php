<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Eloquent model for a PCAP Match Fixture.
 */
class PcapMatch extends Model
{
    protected $table = 'pcap_matches';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'round',
        'conference',
        'season',
        'date',
        'time',
        'status',
        'team_a_id',
        'team_b_id',
        'score_a',
        'score_b',
        'blitz_score_a',
        'blitz_score_b',
        'rapid_score_a',
        'rapid_score_b',
        'boards',
    ];

    protected $casts = [
        'round' => 'integer',
        'score_a' => 'float',
        'score_b' => 'float',
        'blitz_score_a' => 'float',
        'blitz_score_b' => 'float',
        'rapid_score_a' => 'float',
        'rapid_score_b' => 'float',
        'boards' => 'array',
    ];

    public function teamA(): BelongsTo
    {
        return $this->belongsTo(PcapTeam::class, 'team_a_id');
    }

    public function teamB(): BelongsTo
    {
        return $this->belongsTo(PcapTeam::class, 'team_b_id');
    }

    /**
     * Transform to camelCase array matching Angular PcapMatch interface.
     */
    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'round' => (int) $this->round,
            'conference' => $this->conference,
            'season' => $this->season,
            'date' => $this->date,
            'time' => $this->time,
            'status' => $this->status,
            'teamA' => [
                'id' => $this->team_a_id,
                'name' => $this->teamA?->name ?? 'Team A',
                'conference' => $this->teamA?->conference ?? $this->conference,
            ],
            'teamB' => [
                'id' => $this->team_b_id,
                'name' => $this->teamB?->name ?? 'Team B',
                'conference' => $this->teamB?->conference ?? $this->conference,
            ],
            'scoreA' => (float) $this->score_a,
            'scoreB' => (float) $this->score_b,
            'blitzScoreA' => (float) $this->blitz_score_a,
            'blitzScoreB' => (float) $this->blitz_score_b,
            'rapidScoreA' => (float) $this->rapid_score_a,
            'rapidScoreB' => (float) $this->rapid_score_b,
            'boards' => $this->boards ?? [],
        ];
    }
}
