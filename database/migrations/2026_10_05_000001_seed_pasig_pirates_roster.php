<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PcapTeam;
use App\Models\PcapPlayer;
use App\Models\PcapStanding;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Reseeds/updates the Pasig City King Pirates roster idempotently in production,
     * updating IM Chito Garma's Chess.com username to 'Garma0712' without clearing
     * any other teams, rosters, or fixtures.
     * 
     * Since this is a Laravel Migration, it runs exactly once during deployment
     * via start.sh (`php artisan migrate --force`) and is permanently recorded in the
     * `migrations` table. It will NEVER rerun or wipe data when the Render free-tier
     * container shuts down and spins back up.
     */
    public function up(): void
    {
        $team = PcapTeam::updateOrCreate(
            ['id' => 'pasig-king-pirates'],
            [
                'name' => 'Pasig City King Pirates',
                'conference' => 'alpha',
            ]
        );

        PcapStanding::firstOrCreate(
            ['team_id' => 'pasig-king-pirates'],
            [
                'conference' => 'alpha',
                'matches_played' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'match_points' => 0,
                'board_points_for' => 0.0,
                'board_points_against' => 0.0,
                'board_points_diff' => 0.0,
                'form' => [],
                'rank' => 99,
            ]
        );

        $roster = [
            ['id' => 'pasig-p1', 'name' => 'Daniel Quizon', 'title' => 'GM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'quizden37'],
            ['id' => 'pasig-p2', 'name' => 'Michael Concio Jr', 'title' => 'IM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'jakopogi'],
            ['id' => 'pasig-p3', 'name' => 'Sherily Cua', 'title' => 'WFM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'resnullus'],
            ['id' => 'pasig-p4', 'name' => 'Chito Garma', 'title' => 'IM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'Garma0712'],
            ['id' => 'pasig-p5', 'name' => 'Idelfonso Datu', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'DanielDatu'],
            ['id' => 'pasig-p6', 'name' => 'Marc Kevin Labog', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'MarcKevinLabog'],
            ['id' => 'pasig-p7', 'name' => 'Jerome Villanueva', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'MovesChilling'],
            ['id' => 'pasig-p8', 'name' => 'Sherwin Tiu', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Kordapyu'],
            ['id' => 'pasig-p9', 'name' => 'Omar Bagalacsa', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'T0B3RMOR3Y'],
            ['id' => 'pasig-p10', 'name' => 'Carl Espallardo', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'coachcarl'],
            ['id' => 'pasig-p11', 'name' => 'Cromwell Sabado', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Cromwell_Sabado'],
            ['id' => 'pasig-p12', 'name' => 'Faye Bernardino', 'title' => null, 'rating' => null, 'category' => 'HG/L', 'federation' => 'PHI', 'chesscom_username' => 'chessterz07'],
            ['id' => 'pasig-p13', 'name' => 'Gombosuren Munkhgal', 'title' => 'GM', 'rating' => null, 'category' => 'O', 'federation' => 'MGL', 'chesscom_username' => 'Virgo15'],
        ];

        foreach ($roster as $player) {
            $player['team_id'] = $team->id;
            PcapPlayer::updateOrCreate(
                ['id' => $player['id']],
                $player
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive: preserve user data on rollback
    }
};
