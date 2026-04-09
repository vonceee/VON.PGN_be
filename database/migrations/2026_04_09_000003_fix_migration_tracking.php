<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mark all existing migrations as completed
        // This fixes the inconsistent state where tables exist but aren't tracked
        $existingMigrations = [
            '2026_03_06_000000_create_user_preference_table',
            '2026_03_06_000001_create_user_progress_table',
            '2026_03_06_000002_create_badges_table',
            '2026_03_06_000003_create_badge_user_table',
            '2026_03_06_000004_create_personal_access_tokens_table',
            '2026_03_09_000001_create_courses_table',
            '2026_03_09_000002_create_chapters_table',
            '2026_03_09_000003_create_lessons_table',
            '2026_03_10_000002_create_puzzles_table',
            '2026_03_22_000000_add_is_admin_to_users_table',
            '2026_03_27_000000_create_tournaments_table',
            '2026_03_27_000001_create_follows_table',
            '2026_03_28_000000_create_conversations_table',
            '2026_03_28_000001_create_conversation_user_table',
            '2026_03_28_000002_create_messages_table',
            '2026_03_28_000003_add_online_status_to_users_table',
            '2026_03_29_000001_create_games_table',
            '2026_03_29_000002_create_game_seeks_table',
            '2026_03_29_000003_add_draw_offer_fields_to_games_table',
            '2026_03_31_000000_add_created_by_to_tournaments_table',
            '2026_03_31_000001_add_bio_to_users_table',
            '2026_03_31_000002_add_verified_organizer_to_users_table',
            '2026_03_31_000003_create_payments_table',
            '2026_03_31_000004_add_view_count_to_tournaments_table',
            '2026_03_31_000005_create_tournament_bookmarks_table',
            '2026_04_01_000000_add_google_id_to_users_table',
            '2026_04_01_000000_add_link_to_tournaments_table',
            '2026_04_02_000000_update_default_board_piece_styles',
            '2026_04_02_000001_add_first_move_timestamps_to_games_table',
            '2026_04_03_000000_add_live_chess_ratings_to_users_table',
            '2026_04_03_000001_add_heartbeat_columns_to_games_table',
            '2026_04_05_000000_create_coach_applications_table',
            '2026_04_05_173410_remove_chess_columns_from_games_table',
            '2026_04_06_000000_add_indexes_to_user_progress_table',
            '2026_04_07_000000_drop_xp_columns_from_user_progress_table',
            '2026_04_07_000001_add_puzzle_metadata_columns',
            '2026_04_07_000002_add_rating_deviation_to_user_progress',
            '2026_04_07_000003_add_rating_details_to_games_table',
            '2026_04_08_000000_add_moves_to_games_table',
        ];

        $batch = DB::table('migrations')->max('batch') + 1;

        foreach ($existingMigrations as $migration) {
            DB::table('migrations')->insertOrIgnore([
                'migration' => $migration,
                'batch' => $batch,
            ]);
        }
    }

    public function down(): void
    {
        // No rollback needed for this fix
    }
};
