<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyChapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_id',
        'name',
        'initial_fen',
        'current_fen',
        'orientation',
        'moves',
        'order'
    ];

    protected $casts = [
        'moves' => 'array',
    ];

    /**
     * The study this chapter belongs to.
     */
    public function study(): BelongsTo
    {
        return $this->belongsTo(Study::class);
    }
}
