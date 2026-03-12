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
        Schema::create('puzzles', function (Blueprint $table) {
            $table->id();
            $table->string('lichess_puzzle_id')->unique()->nullable();
            $table->string('fen'); // the starting position
            $table->string('moves'); // space-separated winning moves (e.g., "e2e4 e7e5")
            $table->integer('rating'); // the difficulty of the puzzle
            $table->string('themes')->nullable(); // e.g., "fork, mateIn2"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puzzles');
    }
};
