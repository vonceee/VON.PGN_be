<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FideFederation extends Model
{
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'alpha2',
        'player_count',
    ];

    public function players(): HasMany
    {
        return $this->hasMany(FidePlayer::class, 'federation_code', 'code');
    }
}
