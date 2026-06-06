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
            $table->index(['visibility', 'updated_at'], 'studies_visibility_updated_at_index');
            $table->index(['category', 'visibility', 'updated_at'], 'studies_category_visibility_updated_at_index');
            $table->index(['visibility', 'name'], 'studies_visibility_name_index');
            $table->index(['category', 'visibility', 'name'], 'studies_category_visibility_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studies', function (Blueprint $table) {
            $table->dropIndex('studies_visibility_updated_at_index');
            $table->dropIndex('studies_category_visibility_updated_at_index');
            $table->dropIndex('studies_visibility_name_index');
            $table->dropIndex('studies_category_visibility_name_index');
        });
    }
};
