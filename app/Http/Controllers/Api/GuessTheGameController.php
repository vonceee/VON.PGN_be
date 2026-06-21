<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuessTheGameChallenge;
use Illuminate\Http\Request;

class GuessTheGameController extends Controller
{
    /**
     * Fetch the daily challenge for Guess the Game.
     *
     * WHY: Retrieves the challenge set for today, falling back to the latest active challenge 
     *      or a random one to prevent empty states.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * ASSUMPTIONS/EDGE CASES:
     * - Assumes active_date is formatted as YYYY-MM-DD.
     * - Returns a 404 JSON response if no challenges are found in the database.
     */
    public function getDailyChallenge(Request $request)
    {
        $challengeId = $request->query('challenge_id');
        if ($challengeId) {
            $challenge = GuessTheGameChallenge::find($challengeId);
            if ($challenge) {
                return response()->json(['data' => $challenge]);
            }
        }

        // Fetch a completely random game challenge from the pool
        $challenge = GuessTheGameChallenge::inRandomOrder()->first();

        if (!$challenge) {
            return response()->json(['error' => 'No challenges available'], 404);
        }

        return response()->json(['data' => $challenge]);
    }

    /**
     * Import a new challenge from PGN.
     *
     * WHY: Automates challenge creation by parsing standard PGN tag headers (White, Black, Event, Date, Result).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * ASSUMPTIONS/EDGE CASES:
     * - Expects standard PGN tag format e.g. [Tag "Value"].
     * - Extracts only the moves portion of the PGN for frontend board navigation to minimize load.
     * - Date defaults to current year if parsing fails.
     */
    public function importChallenge(Request $request)
    {
        $request->validate([
            'pgn' => 'required|string',
            'active_date' => 'nullable|date',
        ]);

        $pgn = $request->input('pgn');
        $activeDate = $request->input('active_date');

        // Parse standard tags using regex
        preg_match('/\[White\s+"([^"]+)"\]/', $pgn, $whiteMatch);
        preg_match('/\[Black\s+"([^"]+)"\]/', $pgn, $blackMatch);
        preg_match('/\[Event\s+"([^"]+)"\]/', $pgn, $eventMatch);
        preg_match('/\[Date\s+"([^"]+)"\]/', $pgn, $dateMatch);
        preg_match('/\[ECO\s+"([^"]+)"\]/', $pgn, $ecoMatch);
        preg_match('/\[Result\s+"([^"]+)"\]/', $pgn, $resultMatch);

        $white = $whiteMatch[1] ?? 'Unknown White';
        $black = $blackMatch[1] ?? 'Unknown Black';
        $event = $eventMatch[1] ?? 'Unknown Event';
        $eco = $ecoMatch[1] ?? null;
        $result = $resultMatch[1] ?? '*';

        $year = (int) date('Y');
        if (!empty($dateMatch[1])) {
            $parts = explode('.', $dateMatch[1]);
            if (count($parts) > 0 && is_numeric($parts[0])) {
                $year = (int) $parts[0];
            }
        }

        // Clean PGN moves (remove tag headers)
        $movesPgn = preg_replace('/\[.*?\]\s*/', '', $pgn);
        $movesPgn = trim($movesPgn);

        // Check if there is an existing challenge for this active date
        if ($activeDate) {
            $existing = GuessTheGameChallenge::where('active_date', $activeDate)->first();
            if ($existing) {
                // Overwrite the existing challenge for that date
                $existing->update([
                    'white_player' => $white,
                    'black_player' => $black,
                    'event' => $event,
                    'year' => $year,
                    'eco' => $eco,
                    'result' => $result,
                    'pgn' => $movesPgn,
                ]);
                return response()->json(['success' => true, 'data' => $existing, 'overwritten' => true]);
            }
        }

        $challenge = GuessTheGameChallenge::create([
            'white_player' => $white,
            'black_player' => $black,
            'event' => $event,
            'year' => $year,
            'eco' => $eco,
            'result' => $result,
            'pgn' => $movesPgn,
            'active_date' => $activeDate,
        ]);

        return response()->json(['success' => true, 'data' => $challenge], 201);
    }

    public function getNextChallenge(Request $request)
    {
        $currentId = $request->query('current_id');
        
        $query = GuessTheGameChallenge::query();
        if ($currentId) {
            $query->where('id', '!=', $currentId);
        }
        
        $challenge = $query->inRandomOrder()->first();
        
        // If there's only 1 challenge total, fallback to returning the current one
        if (!$challenge && $currentId) {
            $challenge = GuessTheGameChallenge::find($currentId);
        }
        
        if (!$challenge) {
            return response()->json(['error' => 'No challenges available'], 404);
        }
        
        return response()->json(['data' => $challenge]);
    }
}
