<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FidePlayer extends Model
{
    protected $primaryKey = 'fide_id';
    public $incrementing = false;

    protected $fillable = [
        'fide_id',
        'name',
        'federation_code',
        'title',
        'rating_standard',
        'rating_rapid',
        'rating_blitz',
        'birth_year',
        'is_active',
    ];

    public function federation(): BelongsTo
    {
        return $this->belongsTo(FideFederation::class, 'federation_code', 'code');
    }
}
