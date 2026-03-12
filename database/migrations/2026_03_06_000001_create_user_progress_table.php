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
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('completed_lesson_ids')->nullable();
            $table->string('last_active_lesson_id')->nullable();
            $table->integer('total_puzzles_solved')->default(0);
            $table->integer('current_streak_days')->default(0);
            $table->integer('experience_points')->default(0);
            $table->integer('current_level')->default(1);
            $table->integer('puzzle_rating')->default(1200);
            $table->integer('puzzle_streak')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
