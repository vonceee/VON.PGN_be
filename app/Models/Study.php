<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Study extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'visibility',
        'engine_visibility'
    ];

    /**
     * The owner of the study.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Chapters in the study.
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(StudyChapter::class)->orderBy('order');
    }

    /**
     * Collaborators of the study.
     */
    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'study_collaborators')->withPivot(['can_edit', 'is_syncing'])->withTimestamps();
    }

    /**
     * Chat messages in the study lobby.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(StudyMessage::class);
    }
}
