<?php

use App\Models\Game;
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

Broadcast::channel('user.{id}', function (User $user, int $id) {
    return (int) $user->id === $id;
});

Broadcast::channel('game.{gameId}', function (User $user, string $gameId) {
    $game = Game::find($gameId);
    if (!$game || !$game->isPlayer($user->id)) {
        return false;
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
        'color' => $game->getPlayerColor($user->id),
    ];
});

Broadcast::channel('seeks', function (User $user) {
    return true;
});
