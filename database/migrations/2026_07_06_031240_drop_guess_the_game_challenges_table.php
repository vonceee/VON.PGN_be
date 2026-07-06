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
        Schema::dropIfExists('guess_the_game_challenges');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('guess_the_game_challenges', function (Blueprint $table) {
            $table->id();
            $table->string('white_player');
            $table->string('black_player');
            $table->string('event');
            $table->integer('year');
            $table->string('eco')->nullable();
            $table->string('result');
            $table->text('pgn');
            $table->date('active_date')->unique()->nullable();
            $table->timestamps();
        });
    }
};
