<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CollectiblePlayer;

class CollectiblePlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $players = [
            // Legendary (World Champions / GOATs)
            ['name' => 'Magnus Carlsen', 'rarity' => 'Legendary', 'title' => 'GM', 'peak_rating' => 2882, 'bio' => 'The highest-rated player in history and multi-time World Champion.'],
            ['name' => 'Garry Kasparov', 'rarity' => 'Legendary', 'title' => 'GM', 'peak_rating' => 2851, 'bio' => 'Dominant World Champion for 15 years and pioneer of computer chess analysis.'],
            ['name' => 'Bobby Fischer', 'rarity' => 'Legendary', 'title' => 'GM', 'peak_rating' => 2785, 'bio' => 'The American genius who ended Soviet dominance in 1972.'],
            ['name' => 'Judit Polgar', 'rarity' => 'Legendary', 'title' => 'GM', 'peak_rating' => 2735, 'bio' => 'The strongest female player of all time, who defeated 11 World Champions.'],
            ['name' => 'Mikhail Tal', 'rarity' => 'Legendary', 'title' => 'GM', 'peak_rating' => 2705, 'bio' => 'The "Magician from Riga," known for his speculative and brilliant sacrifices.'],

            // Epic (Elite GMs / Candidates)
            ['name' => 'Hikaru Nakamura', 'rarity' => 'Epic', 'title' => 'GM', 'peak_rating' => 2816, 'bio' => 'Speed chess legend and one of the most popular chess personalities.'],
            ['name' => 'Fabiano Caruana', 'rarity' => 'Epic', 'title' => 'GM', 'peak_rating' => 2844, 'bio' => 'Known for his deep preparation and incredible 7/7 start at Sinquefield Cup.'],
            ['name' => 'Viswanathan Anand', 'rarity' => 'Epic', 'title' => 'GM', 'peak_rating' => 2817, 'bio' => 'The "Tiger of Madras" and 5-time World Champion across all formats.'],
            ['name' => 'Ding Liren', 'rarity' => 'Epic', 'title' => 'GM', 'peak_rating' => 2816, 'bio' => 'The first Chinese World Chess Champion.'],
            ['name' => 'Alireza Firouzja', 'rarity' => 'Epic', 'title' => 'GM', 'peak_rating' => 2804, 'bio' => 'The youngest player ever to cross the 2800 ELO mark.'],

            // Rare (Strong GMs / National Heroes)
            ['name' => 'Praggnanandhaa R', 'rarity' => 'Rare', 'title' => 'GM', 'peak_rating' => 2747, 'bio' => 'Indian prodigy and World Cup finalist.'],
            ['name' => 'Gukesh D', 'rarity' => 'Rare', 'title' => 'GM', 'peak_rating' => 2764, 'bio' => 'The youngest Candidates winner in history.'],
            ['name' => 'Anish Giri', 'rarity' => 'Rare', 'title' => 'GM', 'peak_rating' => 2797, 'bio' => 'Known for his solid play and witty social media presence.'],
            ['name' => 'Levon Aronian', 'rarity' => 'Rare', 'title' => 'GM', 'peak_rating' => 2830, 'bio' => 'Armenian superstar known for his creative attacking style.'],

            // Common (Rising Stars / Titleholders)
            ['name' => 'Abhimanyu Mishra', 'rarity' => 'Common', 'title' => 'GM', 'peak_rating' => 2627, 'bio' => 'The youngest Grandmaster in chess history.'],
            ['name' => 'Eric Hansen', 'rarity' => 'Common', 'title' => 'GM', 'peak_rating' => 2629, 'bio' => 'Founder of Chessbrah and popular streamer.'],
            ['name' => 'Aman Hambleton', 'rarity' => 'Common', 'title' => 'GM', 'peak_rating' => 2512, 'bio' => 'Co-founder of Chessbrah and Grandmaster.'],
            ['name' => 'Anna Rudolf', 'rarity' => 'Common', 'title' => 'IM', 'peak_rating' => 2393, 'bio' => 'Popular commentator and International Master.'],
        ];

        foreach ($players as $player) {
            CollectiblePlayer::create($player);
        }
    }
}
