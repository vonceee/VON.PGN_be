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
        Schema::table('studies', function (Blueprint $table) {
            $table->index('user_id', 'studies_user_id_index');
            $table->index('visibility', 'studies_visibility_index');
            $table->index('category', 'studies_category_index');
            $table->index(['category', 'visibility'], 'studies_category_visibility_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studies', function (Blueprint $table) {
            $table->dropIndex('studies_user_id_index');
            $table->dropIndex('studies_visibility_index');
            $table->dropIndex('studies_category_index');
            $table->dropIndex('studies_category_visibility_index');
        });
    }
};
