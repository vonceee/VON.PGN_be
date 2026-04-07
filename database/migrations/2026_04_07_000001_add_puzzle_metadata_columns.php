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
        Schema::table('puzzles', function (Blueprint $table) {
            $table->string('game_url')->nullable()->after('themes');
            $table->string('opening_tags')->nullable()->after('game_url');
            $table->integer('popularity')->nullable()->after('opening_tags');
            $table->integer('nb_plays')->nullable()->after('popularity');
            $table->integer('rating_deviation')->nullable()->after('nb_plays');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('puzzles', function (Blueprint $table) {
            $table->dropColumn([
                'game_url',
                'opening_tags',
                'popularity',
                'nb_plays',
                'rating_deviation',
            ]);
        });
    }
};