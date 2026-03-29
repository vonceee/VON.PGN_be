<?php

namespace App\Services;

use Ryanhs\Chess\Chess;

class ChessService
{
    /**
     * Validate and apply a move in UCI format (e.g., "e2e4").
     * Returns the FEN after the move, or null if illegal.
     */
    public function validateMove(string $fen, string $uciMove): ?array
    {
        $chess = new Chess();

        if (!$chess->load($fen)) {
            return null;
        }

        $from = substr($uciMove, 0, 2);
        $to = substr($uciMove, 2, 2);
        $promotion = strlen($uciMove) > 4 ? $uciMove[4] : null;

        $moveInput = ['from' => $from, 'to' => $to];
        if ($promotion) {
            $moveInput['promotion'] = $promotion;
        }

        $result = $chess->move($moveInput);

        if ($result === null) {
            return null;
        }

        return [
            'fen' => $chess->fen(),
            'san' => $result['san'],
        ];
    }

    /**
     * Check the current game status.
     * Returns: 'checkmate', 'stalemate', 'draw', 'check', 'ongoing'
     */
    public function getGameStatus(string $fen): string
    {
        $chess = new Chess();

        if (!$chess->load($fen)) {
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
        $chess = new Chess();
        $chess->load($fen);

        return $chess->turn() === 'w' ? 'white' : 'black';
    }

    /**
     * Get all legal moves for the current position in UCI format.
     */
    public function getLegalMoves(string $fen): array
    {
        $chess = new Chess();

        if (!$chess->load($fen)) {
            return [];
        }

        $moves = [];
        foreach ($chess->moves(['verbose' => true]) as $move) {
            $uci = $move['from'] . $move['to'];
            if (isset($move['promotion'])) {
                $uci .= $move['promotion'];
            }
            $moves[] = $uci;
        }

        return $moves;
    }
}
