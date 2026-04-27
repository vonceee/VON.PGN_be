<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_id',
        'user_id',
        'body',
    ];

    /**
     * Get the study that owns the message.
     */
    public function study(): BelongsTo
    {
        return $this->belongsTo(Study::class);
    }

    /**
     * Get the user who sent the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
