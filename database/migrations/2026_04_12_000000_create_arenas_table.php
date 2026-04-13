<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arenas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('status', ['upcoming', 'ongoing', 'past'])->default('upcoming');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('time_control')->nullable();
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('current_participants')->default(0);
            $table->string('winner')->nullable();
            $table->json('standings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arenas');
    }
};
