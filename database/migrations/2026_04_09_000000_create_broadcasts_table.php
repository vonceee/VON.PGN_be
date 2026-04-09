<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->string('id')->primary(); // Lichess broadcast ID
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('url'); // Lichess URL
            $table->integer('tier')->nullable(); // Official tier level
            $table->enum('status', ['upcoming', 'ongoing', 'finished'])->default('upcoming')->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('synced_at')->nullable()->index();
            $table->timestamps();
            
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
    }
};
