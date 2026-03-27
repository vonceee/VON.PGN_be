<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('banner_image')->nullable();
            $table->enum('status', ['upcoming', 'ongoing', 'past'])->default('upcoming');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('registration_deadline')->nullable();
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('format')->nullable();
            $table->string('time_control')->nullable();
            $table->string('entry_fee')->nullable();
            $table->string('prize_pool')->nullable();
            $table->string('organizer')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('rounds')->default(0);
            $table->unsignedInteger('current_participants')->default(0);
            $table->unsignedInteger('max_participants')->default(0);
            $table->json('eligibility')->nullable();
            $table->json('categories')->nullable();
            $table->json('schedule')->nullable();
            $table->string('winner')->nullable();
            $table->json('standings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
