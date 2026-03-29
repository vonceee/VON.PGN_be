<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('white_player_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('black_player_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, active, completed, aborted
            $table->string('time_control')->default('600+0'); // e.g., "600+0" (seconds+increment)
            $table->integer('initial_time_ms')->default(600000);
            $table->integer('increment_ms')->default(0);
            $table->string('initial_fen')->default('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1');
            $table->string('current_fen')->default('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1');
            $table->integer('white_time_remaining_ms')->default(600000);
            $table->integer('black_time_remaining_ms')->default(600000);
            $table->timestamp('last_move_timestamp')->nullable();
            $table->string('turn')->default('white'); // white or black
            $table->json('moves')->nullable(); // array of UCI moves
            $table->string('result')->nullable(); // "1-0", "0-1", "1/2-1/2"
            $table->string('termination')->nullable(); // checkmate, stalemate, timeout, resignation, agreement, aborted
            $table->integer('white_elo')->default(1200);
            $table->integer('black_elo')->default(1200);
            $table->timestamps();

            $table->index('status');
            $table->index(['white_player_id', 'status']);
            $table->index(['black_player_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
