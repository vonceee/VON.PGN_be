<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image_url'
    ];

    // The inverse of the many-to-many relationship
    public function users()
    {
        return $this->belongsToMany(User::class, 'badge_user')
            ->withPivot('earned_at');
    }
}
