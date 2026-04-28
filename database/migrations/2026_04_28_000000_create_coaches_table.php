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
        Schema::create('coaches', function (Blueprint $table) {
            $table->string('id')->primary(); // Using string ID as slug/primary key
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('short_info');
            $table->integer('fide_rating')->nullable();
            $table->string('profile_picture');
            $table->boolean('is_academy_instructor')->default(false);
            $table->json('playing_experience');
            $table->json('teaching_experience');
            $table->text('bio');
            $table->string('location');
            $table->string('availability');
            $table->json('teaching_methods');
            $table->string('coaching_type');
            $table->json('social_media');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coaches');
    }
};
