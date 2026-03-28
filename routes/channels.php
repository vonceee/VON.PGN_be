<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {
    return $user->conversations()
        ->where('conversation_id', $conversationId)
        ->exists();
});

Broadcast::channel('updates', function (User $user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
    ];
});
