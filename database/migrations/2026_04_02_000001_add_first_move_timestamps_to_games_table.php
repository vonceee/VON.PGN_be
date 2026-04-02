<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->timestamp('white_first_move_at')->nullable()->after('last_move_timestamp');
            $table->timestamp('black_first_move_at')->nullable()->after('white_first_move_at');
            $table->timestamp('clock_start_at')->nullable()->after('black_first_move_at');
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['white_first_move_at', 'black_first_move_at', 'clock_start_at']);
        });
    }
};
