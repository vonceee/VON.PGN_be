<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_seeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('time_control'); // e.g., "180+0"
            $table->integer('elo')->default(1200);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['user_id', 'time_control']);
            $table->index(['time_control', 'elo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_seeks');
    }
};
