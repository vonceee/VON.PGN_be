<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('short_info')->nullable();
            $table->integer('fide_rating')->nullable();
            $table->string('email');

            // Experience - stored as JSON
            $table->json('playing_experience')->nullable();
            $table->json('teaching_experience')->nullable();
            $table->json('teaching_methods')->nullable();

            // Details
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('availability')->nullable();
            $table->string('coaching_type')->nullable();

            // Social media
            $table->string('twitter')->nullable();
            $table->string('youtube')->nullable();
            $table->string('twitch')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('chesscom')->nullable();
            $table->string('lichess')->nullable();

            // Profile picture path (file storage)
            $table->string('profile_picture_path')->nullable();

            // Status and metadata
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('submitted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_applications');
    }
};