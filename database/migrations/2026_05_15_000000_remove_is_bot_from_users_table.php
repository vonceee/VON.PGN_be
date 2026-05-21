<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete all bot users before dropping the column
        DB::table('users')->whereRaw('is_bot = true')->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_bot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_bot')->default(false)->after('is_online');
        });
    }
};
