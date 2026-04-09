<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Temporarily disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Drop the broadcasts table if it exists (leftover from failed migration)
        Schema::dropIfExists('broadcasts');
        
        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Recreate it properly
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
        
        // Record the original migration as completed
        DB::table('migrations')->insertOrIgnore([
            'migration' => '2026_04_09_000000_create_broadcasts_table',
            'batch' => DB::table('migrations')->max('batch'),
        ]);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('broadcasts');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
