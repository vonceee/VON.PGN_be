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
            $table->string('preview_fen')->nullable();
            $table->string('preview_last_move')->nullable();
        });

        // Automatically backfill existing studies in the database during deployment migration
        $studies = \App\Models\Study::all();
        foreach ($studies as $study) {
            $firstChapter = $study->chapters()->orderBy('order')->first();
            if ($firstChapter) {
                $moves = $firstChapter->moves ?? [];
                $previewFen = $firstChapter->current_fen ?? $firstChapter->initial_fen ?? 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
                $previewLastMove = null;

                if (is_array($moves) && count($moves) > 0 && !isset($moves['pgn'])) {
                    $lastNode = end($moves);
                    if (isset($lastNode['fen'])) {
                        $previewFen = $lastNode['fen'];
                    }
                    if (isset($lastNode['uci'])) {
                        $previewLastMove = $lastNode['uci'];
                    }
                }

                $study->update([
                    'preview_fen' => $previewFen,
                    'preview_last_move' => $previewLastMove,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studies', function (Blueprint $table) {
            $table->dropColumn(['preview_fen', 'preview_last_move']);
        });
    }
};
