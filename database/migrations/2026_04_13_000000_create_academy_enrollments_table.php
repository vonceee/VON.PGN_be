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
        Schema::create('academy_enrollments', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('full_name');
            $blueprint->string('email');
            $blueprint->string('contact_number');
            $blueprint->string('chess_level');
            $blueprint->text('experience')->nullable();
            $blueprint->enum('status', ['pending', 'contacted', 'confirmed', 'paid', 'cancelled'])->default('pending');
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academy_enrollments');
    }
};
