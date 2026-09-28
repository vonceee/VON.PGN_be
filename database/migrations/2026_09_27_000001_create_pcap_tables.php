<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pcap_teams', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. 'pasig-king-pirates'
            $table->string('name');
            $table->string('conference')->default('alpha'); // 'alpha' or 'omega'
            $table->unsignedInteger('wins')->default(0);
            $table->unsignedInteger('losses')->default(0);
            $table->unsignedInteger('draws')->default(0);
            $table->unsignedInteger('match_points')->default(0);
            $table->decimal('board_points_for', 8, 1)->default(0.0);
            $table->decimal('board_points_against', 8, 1)->default(0.0);
            $table->timestamps();

            $table->index('conference');
        });

        Schema::create('pcap_players', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. 'pk-p1'
            $table->string('team_id');
            $table->string('name');
            $table->string('title', 20)->nullable(); // GM, IM, FM, NM...
            $table->unsignedInteger('rating')->nullable();
            $table->string('category', 10)->default('HG'); // 'O' (Open B1-B2), 'L' (Lady B3), 'S' (Senior B4), 'HG' (Homegrown B5-B7)
            $table->string('federation', 10)->default('PHI');
            $table->string('hometown')->nullable();
            $table->string('win_loss_record')->nullable();
            $table->timestamps();

            $table->foreign('team_id')
                ->references('id')
                ->on('pcap_teams')
                ->cascadeOnDelete();

            $table->index(['team_id', 'category']);
        });

        Schema::create('pcap_matches', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. 'match-rd1-1'
            $table->unsignedSmallInteger('round')->default(1);
            $table->string('conference')->default('alpha'); // 'alpha', 'omega'
            $table->string('season')->default('2026 Season • Wesley So Cup');
            $table->string('date')->default('Today');
            $table->string('time')->default('7:00 PM PHT');
            $table->string('status')->default('upcoming'); // 'upcoming', 'completed'
            $table->string('team_a_id');
            $table->string('team_b_id');
            $table->decimal('score_a', 5, 1)->default(0.0);
            $table->decimal('score_b', 5, 1)->default(0.0);
            $table->decimal('blitz_score_a', 5, 1)->default(0.0);
            $table->decimal('blitz_score_b', 5, 1)->default(0.0);
            $table->decimal('rapid_score_a', 5, 1)->default(0.0);
            $table->decimal('rapid_score_b', 5, 1)->default(0.0);
            $table->json('boards')->nullable(); // Array of PGNs
            $table->timestamps();

            $table->foreign('team_a_id')
                ->references('id')
                ->on('pcap_teams')
                ->cascadeOnDelete();

            $table->foreign('team_b_id')
                ->references('id')
                ->on('pcap_teams')
                ->cascadeOnDelete();

            $table->index(['conference', 'round']);
            $table->index('status');
        });

        Schema::create('pcap_standings', function (Blueprint $table) {
            $table->id();
            $table->string('team_id')->unique();
            $table->string('conference')->default('alpha'); // 'alpha' or 'omega'
            $table->unsignedInteger('matches_played')->default(0);
            $table->unsignedInteger('won')->default(0);
            $table->unsignedInteger('drawn')->default(0);
            $table->unsignedInteger('lost')->default(0);
            $table->unsignedInteger('match_points')->default(0);
            $table->decimal('board_points_for', 8, 1)->default(0.0);
            $table->decimal('board_points_against', 8, 1)->default(0.0);
            $table->decimal('board_points_diff', 8, 1)->default(0.0);
            $table->json('form')->nullable(); // ['W', 'D', 'L']
            $table->unsignedSmallInteger('rank')->default(1);
            $table->timestamps();

            $table->foreign('team_id')
                ->references('id')
                ->on('pcap_teams')
                ->cascadeOnDelete();

            $table->index(['conference', 'rank']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pcap_standings');
        Schema::dropIfExists('pcap_matches');
        Schema::dropIfExists('pcap_players');
        Schema::dropIfExists('pcap_teams');
    }
};
