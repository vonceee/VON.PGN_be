<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Eloquent model for a PCAP Roster Player.
 */
class PcapPlayer extends Model
{
    protected $table = 'pcap_players';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'team_id',
        'name',
        'title',
        'rating',
        'category',
        'federation',
        'hometown',
        'win_loss_record',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Parent team relation.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(PcapTeam::class, 'team_id');
    }

    /**
     * Transform to camelCase array matching Angular PcapRosterPlayer interface.
     */
    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title ?? '',
            'rating' => $this->rating !== null ? (int) $this->rating : null,
            'category' => $this->category ?? 'HG',
            'federation' => $this->federation ?: 'PHI',
            'hometown' => $this->hometown ?? '',
            'winLossRecord' => $this->win_loss_record ?? '',
        ];
    }
}
