<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCollectible extends Model
{
    protected $fillable = [
        'user_id',
        'collectible_player_id',
        'count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collectiblePlayer()
    {
        return $this->belongsTo(CollectiblePlayer::class);
    }
}
