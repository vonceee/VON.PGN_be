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
        Schema::create('collectible_players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('rarity', ['Common', 'Rare', 'Epic', 'Legendary'])->default('Common');
            $table->string('title')->nullable(); // e.g. GM, IM, WGM
            $table->integer('peak_rating')->nullable();
            $table->text('bio')->nullable();
            $table->string('image_url')->nullable();
            $table->json('stats')->nullable(); // For future flexibility
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collectible_players');
    }
};
