<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'theme',
        'board_style',
        'piece_style',
        'background_image',
        'sound_enabled',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
