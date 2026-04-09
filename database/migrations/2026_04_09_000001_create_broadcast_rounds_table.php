<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_rounds', function (Blueprint $table) {
            $table->id();
            $table->string('broadcast_id');
            $table->string('lichess_round_id');
            $table->json('games_data')->nullable();
            $table->integer('games_count')->default(0);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            
            $table->foreign('broadcast_id')->references('id')->on('broadcasts')->onDelete('cascade');
            $table->unique(['broadcast_id', 'lichess_round_id']);
            $table->index('synced_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_rounds');
    }
};
