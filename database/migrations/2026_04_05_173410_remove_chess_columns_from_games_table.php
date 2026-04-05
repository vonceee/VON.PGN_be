<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove chess-related columns since chess logic is now handled by microservice.
     */
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn([
                'initial_fen',
                'current_fen',
                'white_time_remaining_ms',
                'black_time_remaining_ms',
                'last_move_timestamp',
                'white_first_move_at',
                'black_first_move_at',
                'clock_start_at',
                'turn',
                'moves',
                'draw_offered_by',
                'draw_offered_at'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('initial_fen')->nullable();
            $table->string('current_fen')->nullable();
            $table->integer('white_time_remaining_ms')->nullable();
            $table->integer('black_time_remaining_ms')->nullable();
            $table->timestamp('last_move_timestamp')->nullable();
            $table->timestamp('white_first_move_at')->nullable();
            $table->timestamp('black_first_move_at')->nullable();
            $table->timestamp('clock_start_at')->nullable();
            $table->enum('turn', ['white', 'black'])->nullable();
            $table->json('moves')->nullable();
            $table->integer('draw_offered_by')->nullable();
            $table->timestamp('draw_offered_at')->nullable();
        });
    }
};
