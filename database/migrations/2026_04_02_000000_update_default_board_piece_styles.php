<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('user_preferences')
            ->where('board_style', 'classic')
            ->update(['board_style' => 'newspaper']);

        DB::table('user_preferences')
            ->where('piece_style', 'standard')
            ->update(['piece_style' => 'cburnett']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('user_preferences')
            ->where('board_style', 'newspaper')
            ->update(['board_style' => 'classic']);

        DB::table('user_preferences')
            ->where('piece_style', 'cburnett')
            ->update(['piece_style' => 'standard']);
    }
};
