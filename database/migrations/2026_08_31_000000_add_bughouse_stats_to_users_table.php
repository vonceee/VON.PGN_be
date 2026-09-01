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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('bughouse_wins')->default(0);
            $table->unsignedInteger('bughouse_draws')->default(0);
            $table->unsignedInteger('bughouse_losses')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bughouse_wins', 'bughouse_draws', 'bughouse_losses']);
        });
    }
};
