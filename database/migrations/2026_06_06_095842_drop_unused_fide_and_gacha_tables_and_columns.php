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
        Schema::dropIfExists('user_collectibles');
        Schema::dropIfExists('collectible_players');
        Schema::dropIfExists('fide_players');
        Schema::dropIfExists('fide_federations');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['daily_packs_available', 'last_pack_reset']);
        });

        Schema::table('user_preferences', function (Blueprint $table) {
            $table->dropColumn('background_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('fide_federations', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('name', 100);
            $table->string('flag_url', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('fide_players', function (Blueprint $table) {
            $table->unsignedInteger('fide_id')->primary();
            $table->string('name', 150);
            $table->string('federation_code', 10)->nullable();
            $table->integer('standard_rating')->nullable();
            $table->integer('rapid_rating')->nullable();
            $table->integer('blitz_rating')->nullable();
            $table->integer('birth_year')->nullable();
            $table->string('title', 15)->nullable();
            $table->string('gender', 5)->nullable();
            $table->timestamps();
            $table->foreign('federation_code')->references('code')->on('fide_federations');
        });

        Schema::create('collectible_players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rarity');
            $table->string('title')->nullable();
            $table->integer('peak_rating')->nullable();
            $table->text('bio')->nullable();
            $table->string('image_url')->nullable();
            $table->json('stats')->nullable();
            $table->timestamps();
        });

        Schema::create('user_collectibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('collectible_player_id')->constrained()->onDelete('cascade');
            $table->integer('count')->default(1);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('daily_packs_available')->default(10);
            $table->timestamp('last_pack_reset')->nullable();
        });

        Schema::table('user_preferences', function (Blueprint $table) {
            $table->string('background_image', 400)->nullable();
        });
    }
};
