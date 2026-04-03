<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->timestamp('white_last_heartbeat_at')->nullable()->after('clock_start_at');
            $table->timestamp('black_last_heartbeat_at')->nullable()->after('white_last_heartbeat_at');
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['white_last_heartbeat_at', 'black_last_heartbeat_at']);
        });
    }
};
