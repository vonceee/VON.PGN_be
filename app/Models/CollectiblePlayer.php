<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectiblePlayer extends Model
{
    protected $fillable = [
        'name',
        'rarity',
        'title',
        'peak_rating',
        'bio',
        'image_url',
        'stats',
    ];

    protected $casts = [
        'stats' => 'array',
    ];

    public function owners()
    {
        return $this->hasMany(UserCollectible::class);
    }
}
