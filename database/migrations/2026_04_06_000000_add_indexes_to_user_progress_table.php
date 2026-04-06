<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_progress', function (Blueprint $table) {
            $table->index('puzzle_rating', 'user_progress_puzzle_rating_index');
            $table->index('puzzle_streak', 'user_progress_puzzle_streak_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_progress', function (Blueprint $table) {
            $table->dropIndex('user_progress_puzzle_rating_index');
            $table->dropIndex('user_progress_puzzle_streak_index');
        });
    }
};