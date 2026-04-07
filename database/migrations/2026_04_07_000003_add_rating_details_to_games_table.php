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
        Schema::table('games', function (Blueprint $table) {
            // White player Glicko-2 metadata snapshots
            $table->unsignedInteger('white_rd')->default(350);
            $table->float('white_vol')->default(0.06);

            // Black player Glicko-2 metadata snapshots
            $table->unsignedInteger('black_rd')->default(350);
            $table->float('black_vol')->default(0.06);
            
            // Final rating changes (to be populated on game end)
            $table->integer('white_rating_change')->nullable();
            $table->integer('black_rating_change')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn([
                'white_rd',
                'white_vol',
                'black_rd',
                'black_vol',
                'white_rating_change',
                'black_rating_change'
            ]);
        });
    }
};
