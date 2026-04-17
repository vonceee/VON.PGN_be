<?php

use App\Models\User;
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
        User::where('name', 'Huiqi')->update(['name' => 'Kuromi2026']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::where('name', 'Kuromi2026')->update(['name' => 'Huiqi']);
    }
};
