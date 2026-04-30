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
        Schema::table('study_collaborators', function (Blueprint $table) {
            $table->boolean('is_syncing')->default(true)->after('can_edit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_collaborators', function (Blueprint $table) {
            $table->dropColumn('is_syncing');
        });
    }
};
