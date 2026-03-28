<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [];

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withPivot(['unread_count', 'last_read_at'])
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        });
    }

    public static function getOrCreateBetween(int $userId1, int $userId2): self
    {
        $conversation = self::forUser($userId1)
            ->whereHas('participants', function ($q) use ($userId2) {
                $q->where('users.id', $userId2);
            })
            ->first();

        if ($conversation) {
            return $conversation;
        }

        $conversation = self::create();
        $conversation->participants()->attach([$userId1, $userId2]);

        return $conversation;
    }

    public function getUnreadCountFor(int $userId): int
    {
        $pivot = $this->participants()
            ->where('users.id', $userId)
            ->first()?->pivot;

        return $pivot?->unread_count ?? 0;
    }
}
