<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BotUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bots = [
            ['name' => 'ShadowKnight_99', 'bullet' => 1200, 'blitz' => 1250, 'rapid' => 1300],
            ['name' => 'ChessMaster_2004', 'bullet' => 2100, 'blitz' => 2150, 'rapid' => 2200],
            ['name' => 'CasualPawn_88', 'bullet' => 800, 'blitz' => 850, 'rapid' => 900],
            ['name' => 'QueenGambit_Fan', 'bullet' => 1500, 'blitz' => 1550, 'rapid' => 1600],
            ['name' => 'Grandmaster_Ghost', 'bullet' => 2400, 'blitz' => 2450, 'rapid' => 2500],
            ['name' => 'DeepThinker_Chess', 'bullet' => 1800, 'blitz' => 1850, 'rapid' => 1900],
            ['name' => 'BlitzWizard', 'bullet' => 2000, 'blitz' => 2050, 'rapid' => 2100],
            ['name' => 'RookieMove_1', 'bullet' => 900, 'blitz' => 950, 'rapid' => 1000],
            ['name' => 'TacticalEagle', 'bullet' => 1650, 'blitz' => 1700, 'rapid' => 1750],
            ['name' => 'EndgamePro_92', 'bullet' => 1950, 'blitz' => 2000, 'rapid' => 2050],
        ];

        foreach ($bots as $bot) {
            \App\Models\User::updateOrCreate(
                ['email' => strtolower($bot['name']) . '@vonchess.bot'],
                [
                    'name' => $bot['name'],
                    'password' => \Illuminate\Support\Facades\Hash::make('bot-password-' . $bot['name']),
                    'is_bot' => true,
                    'bullet_rating' => $bot['bullet'],
                    'bullet_rd' => 150,
                    'blitz_rating' => $bot['blitz'],
                    'blitz_rd' => 150,
                    'rapid_rating' => $bot['rapid'],
                    'rapid_rd' => 150,
                ]
            );
        }
    }
}
