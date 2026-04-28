<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'title',
        'short_info',
        'fide_rating',
        'profile_picture',
        'is_academy_instructor',
        'playing_experience',
        'teaching_experience',
        'bio',
        'location',
        'availability',
        'teaching_methods',
        'coaching_type',
        'social_media',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_academy_instructor' => 'boolean',
        'playing_experience' => 'array',
        'teaching_experience' => 'array',
        'teaching_methods' => 'array',
        'social_media' => 'array',
        'fide_rating' => 'integer',
    ];
}
