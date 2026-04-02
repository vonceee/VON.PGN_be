<?php

namespace App\Services;

use PChess\Chess\Chess;

class ChessService
{
    /**
     * Validate and apply a move in UCI format (e.g., "e2e4").
     * Returns the FEN after the move, or null if illegal.
     */
    public function validateMove(string $fen, string $uciMove): ?array
    {
        try {
            $chess = new Chess($fen);
        } catch (\Exception $e) {
            return null;
        }

        // Convert UCI to SAN for the move
        $from = substr($uciMove, 0, 2);
        $to = substr($uciMove, 2, 2);
        $promotion = strlen($uciMove) > 4 ? $uciMove[4] : null;

        // Get all legal moves and find the matching one
        $moves = $chess->moves();
        $matchingMove = null;
        
        foreach ($moves as $move) {
            if ($move->from === $from && $move->to === $to) {
                if ($promotion === null || (isset($move->promotion) && $move->promotion === $promotion)) {
                    $matchingMove = $move;
                    break;
                }
            }
        }

        if ($matchingMove === null) {
            return null;
        }

        $result = $chess->move($matchingMove->san);

        if ($result === null) {
            return null;
        }

        return [
            'fen' => $chess->fen(),
            'san' => $result->san,
        ];
    }

    /**
     * Check the current game status.
     * Returns: 'checkmate', 'stalemate', 'draw', 'check', 'ongoing'
     */
    public function getGameStatus(string $fen): string
    {
        try {
            $chess = new Chess($fen);
        } catch (\Exception $e) {
            return 'invalid';
        }

        if ($chess->inCheckmate()) {
            return 'checkmate';
        }

        if ($chess->inStalemate()) {
            return 'stalemate';
        }

        if ($chess->inDraw()) {
            return 'draw';
        }

        if ($chess->inCheck()) {
            return 'check';
        }

        return 'ongoing';
    }

    /**
     * Get the current turn color from a FEN string.
     * Returns 'white' or 'black'.
     */
    public function getTurn(string $fen): string
    {
        try {
            $chess = new Chess($fen);
        } catch (\Exception $e) {
            return 'white';
        }

        return $chess->turn === 'w' ? 'white' : 'black';
    }

    /**
     * Get all legal moves for the current position in UCI format.
     */
    public function getLegalMoves(string $fen): array
    {
        try {
            $chess = new Chess($fen);
        } catch (\Exception $e) {
            return [];
        }

        $moves = [];
        foreach ($chess->moves() as $move) {
            $uci = $move->from . $move->to;
            if (isset($move->promotion)) {
                $uci .= $move->promotion;
            }
            $moves[] = $uci;
        }

        return $moves;
    }
}
