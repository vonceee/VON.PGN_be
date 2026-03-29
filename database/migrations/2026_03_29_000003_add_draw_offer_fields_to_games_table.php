<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->unsignedInteger('draw_offered_by')->nullable()->after('termination');
            $table->timestamp('draw_offered_at')->nullable()->after('draw_offered_by');
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['draw_offered_by', 'draw_offered_at']);
        });
    }
};
