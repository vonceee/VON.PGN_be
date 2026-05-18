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
        Schema::create('woodpecker_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->json('puzzle_ids');
            $table->integer('total_puzzles');
            $table->integer('rating_min')->nullable();
            $table->integer('rating_max')->nullable();
            $table->string('theme')->nullable();
            $table->integer('current_cycle_number')->default(1);
            $table->string('status')->default('active'); // active, completed, abandoned
            $table->timestamps();
        });

        Schema::create('woodpecker_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('woodpecker_session_id')->constrained('woodpecker_sessions')->onDelete('cascade');
            $table->integer('cycle_number');
            $table->string('status')->default('in_progress'); // in_progress, completed
            $table->integer('current_puzzle_index')->default(0);
            $table->timestamp('start_time')->useCurrent();
            $table->timestamp('end_time')->nullable();
            $table->integer('total_solved')->default(0);
            $table->integer('total_correct')->default(0);
            $table->integer('total_time_seconds')->default(0);
            $table->json('attempts'); // Array of items: [{"puzzle_id": 12, "correct": true, "time_spent": 5, "moves": "e4 e5"}]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('woodpecker_cycles');
        Schema::dropIfExists('woodpecker_sessions');
    }
};
