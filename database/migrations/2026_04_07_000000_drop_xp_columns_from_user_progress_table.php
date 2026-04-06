<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_progress', function (Blueprint $table) {
            $table->dropColumn(['experience_points', 'current_level']);
        });
    }

    public function down(): void
    {
        Schema::table('user_progress', function (Blueprint $table) {
            $table->unsignedInteger('experience_points')->default(0);
            $table->unsignedInteger('current_level')->default(1);
        });
    }
};