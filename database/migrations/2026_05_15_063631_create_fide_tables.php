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
        Schema::create('fide_federations', function (Blueprint $table) {
            $table->string('code', 3)->primary(); // e.g., 'NOR', 'USA'
            $table->string('name');
            $table->string('alpha2', 2)->nullable(); // e.g., 'NO', 'US' for flags
            $table->integer('player_count')->default(0);
            $table->timestamps();
        });

        Schema::create('fide_players', function (Blueprint $table) {
            $table->unsignedInteger('fide_id')->primary();
            $table->string('name');
            $table->string('federation_code', 3)->index();
            $table->string('title', 5)->nullable();
            $table->integer('rating_standard')->nullable()->index();
            $table->integer('rating_rapid')->nullable();
            $table->integer('rating_blitz')->nullable();
            $table->year('birth_year')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('federation_code')->references('code')->on('fide_federations');
            $table->index(['name', 'rating_standard']); // For searching top players by name
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fide_players');
        Schema::dropIfExists('fide_federations');
    }
};
