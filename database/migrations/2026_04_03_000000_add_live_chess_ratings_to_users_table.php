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
        Schema::table('users', function (Blueprint $table) {
            // Bullet rating
            $table->unsignedInteger('bullet_rating')->default(1500);
            $table->unsignedInteger('bullet_rd')->default(350);
            $table->unsignedInteger('bullet_games')->default(0);

            // Blitz rating
            $table->unsignedInteger('blitz_rating')->default(1500);
            $table->unsignedInteger('blitz_rd')->default(350);
            $table->unsignedInteger('blitz_games')->default(0);

            // Rapid rating
            $table->unsignedInteger('rapid_rating')->default(1500);
            $table->unsignedInteger('rapid_rd')->default(350);
            $table->unsignedInteger('rapid_games')->default(0);

            // Last game timestamp
            $table->timestamp('last_game_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
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
};