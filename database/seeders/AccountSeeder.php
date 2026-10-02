<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPreference;
use App\Models\UserProgress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    /**
     * Run the account seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@vonchess.net'],
            [
                'name' => 'vonchess',
                'password' => Hash::make('password123'),
                'is_admin' => true,
                'verified_organizer' => true,
                'country_code' => 'PH',
                'email_verified_at' => now(),
            ]
        );

        UserPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'theme' => 'system',
                'board_style' => 'newspaper',
                'piece_style' => 'cburnett',
                'sound_enabled' => true,
            ]
        );

        UserProgress::firstOrCreate(
            ['user_id' => $user->id],
            [
                'puzzle_rating' => 1200,
                'total_puzzles_solved' => 0,
                'current_streak_days' => 0,
                'puzzle_streak' => 0,
            ]
        );

        $this->command->info("AccountSeeder: Successfully seeded admin account '{$user->name}' ({$user->email}).");
    }
}
