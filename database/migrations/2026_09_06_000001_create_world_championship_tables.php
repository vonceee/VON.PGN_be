<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for World Chess Championship Matchups, Games, and Narrative Story Sections.
 * 
 * WHY: Supports dedicated championship editorial pages, historical game analysis,
 *      move annotations, and interactive study linking.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. World Championship Matches (Overview, duels, scores, venues)
        Schema::create('world_championship_matches', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // e.g. 'wcc-2024', 'wcc-1972', 'opera-game'
            $table->unsignedSmallInteger('year')->index(); // e.g. 2024
            $table->string('display_year')->nullable(); // e.g. '1993 (PCA)'
            $table->string('title'); // e.g. '2024 World Chess Championship'
            $table->string('champion'); // e.g. 'Ding Liren'
            $table->string('challenger'); // e.g. 'Gukesh Dommaraju'
            $table->string('winner'); // e.g. 'Gukesh Dommaraju'
            $table->string('score'); // e.g. '7.5 - 6.5'
            $table->string('format'); // e.g. '14 Classical Games + Tiebreaks'
            $table->string('location'); // e.g. 'Resorts World Sentosa, Singapore'
            $table->string('era'); // e.g. 'Modern Era (2006-Present)'
            $table->text('description'); // Historical context & narrative
            $table->json('key_highlights')->nullable(); // Bullet points
            $table->unsignedInteger('games_count')->default(0);
            $table->unsignedBigInteger('study_id')->nullable()->index(); // Optional FK/link to studies table
            $table->boolean('is_published')->default(true)->index();
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();

            $table->index(['year', 'is_published']);
            $table->index(['era', 'is_published']);
        });

        // 2. World Championship Games (Individual games of the match)
        Schema::create('world_championship_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('world_championship_matches')->cascadeOnDelete();
            $table->unsignedSmallInteger('game_number')->default(1); // Round/game number
            $table->string('slug')->unique()->nullable(); // e.g. 'wcc-2024-g14'
            $table->string('title'); // e.g. 'Game 14: The Crowning Moment'
            $table->string('subtitle')->nullable(); // e.g. 'Ding Liren vs. Gukesh Dommaraju – Singapore, 2024'
            $table->string('white_player');
            $table->string('black_player');
            $table->string('result', 10); // '1-0', '0-1', '1/2-1/2', '*'
            $table->date('game_date')->nullable();
            $table->string('eco', 10)->nullable(); // e.g. 'D37'
            $table->string('opening_name')->nullable(); // e.g. "Queen's Gambit Declined"
            $table->longText('pgn'); // Complete PGN moves & tags
            $table->string('initial_fen')->default('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1');
            $table->unsignedSmallInteger('total_plies')->default(0);
            $table->text('narrative_overview')->nullable(); // Intro paragraph for editorial layout
            $table->boolean('is_highlighted')->default(false); // Key game to feature first
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['match_id', 'game_number']);
            $table->index(['match_id', 'is_highlighted']);
        });

        // 3. Editorial Narrative Sections (Story chapters matching the two-column layout)
        Schema::create('world_championship_game_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('world_championship_games')->cascadeOnDelete();
            $table->string('title'); // e.g. 'Grabbing the center', 'Development over material'
            $table->longText('content'); // Prose commentary with inline moves
            $table->unsignedSmallInteger('start_ply')->nullable(); // e.g. 1
            $table->unsignedSmallInteger('end_ply')->nullable(); // e.g. 10
            $table->string('key_move_san')->nullable(); // e.g. '10. Nxb5!'
            $table->integer('order')->default(1);
            $table->timestamps();

            $table->index(['game_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('world_championship_game_sections');
        Schema::dropIfExists('world_championship_games');
        Schema::dropIfExists('world_championship_matches');
    }
};
