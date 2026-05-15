<?php

namespace App\Services;

use App\Models\CollectiblePlayer;
use App\Models\User;
use App\Models\UserCollectible;
use Illuminate\Support\Facades\DB;

class GachaService
{
    const PULL_COST = 100;
    const TEN_PULL_COST = 900;

    /**
     * Perform a single gacha pull.
     */
    public function pull(User $user, int $count = 1): array
    {
        $this->ensureDailyPacksReset($user);

        if ($user->daily_packs_available < $count) {
            throw new \Exception('Insufficient daily packs. Come back tomorrow!');
        }

        return DB::transaction(function () use ($user, $count) {
            $user->decrement('daily_packs_available', $count);

            $results = [];
            for ($i = 0; $i < $count; $i++) {
                $player = $this->getRandomPlayer();
                $this->addPlayerToCollection($user, $player);
                $results[] = $player;
            }

            return $results;
        });
    }

    /**
     * Ensure the user's daily packs are reset if it's a new day.
     */
    public function ensureDailyPacksReset(User $user): void
    {
        $lastReset = $user->last_pack_reset;
        $now = now();

        if (!$lastReset || !$lastReset->isSameDay($now)) {
            $user->daily_packs_available = 10;
            $user->last_pack_reset = $now;
            $user->save();
        }
    }

    /**
     * Get a random player based on rarity weights.
     */
    private function getRandomPlayer(): CollectiblePlayer
    {
        $rand = mt_rand(1, 1000);

        if ($rand <= 20) { // 2%
            $rarity = 'Legendary';
        } elseif ($rand <= 100) { // 8%
            $rarity = 'Epic';
        } elseif ($rand <= 300) { // 20%
            $rarity = 'Rare';
        } else { // 70%
            $rarity = 'Common';
        }

        return CollectiblePlayer::where('rarity', $rarity)
            ->inRandomOrder()
            ->first() ?? CollectiblePlayer::inRandomOrder()->first();
    }

    /**
     * Add player to user collection or increment count.
     */
    private function addPlayerToCollection(User $user, CollectiblePlayer $player): void
    {
        $collectible = UserCollectible::firstOrNew([
            'user_id' => $user->id,
            'collectible_player_id' => $player->id,
        ]);

        if ($collectible->exists) {
            $collectible->increment('count');
        } else {
            $collectible->count = 1;
            $collectible->save();
        }
    }
}
