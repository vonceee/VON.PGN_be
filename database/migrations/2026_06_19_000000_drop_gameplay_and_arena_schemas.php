<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop foreign-key-dependent tables first
        Schema::dropIfExists('arena_messages');
        Schema::dropIfExists('arenas');
        Schema::dropIfExists('game_seeks');
        Schema::dropIfExists('games');

        // 2. Drop live gameplay columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bullet_rating',
                'bullet_rd',
                'bullet_games',
                'blitz_rating',
                'blitz_rd',
                'blitz_games',
                'rapid_rating',
                'rapid_rd',
                'rapid_games',
                'last_game_at',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-creating these is not necessary as this is a destructive transition.
    }
};
