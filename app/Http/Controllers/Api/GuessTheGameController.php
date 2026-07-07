<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Study;
use App\Models\StudyChapter;
use App\Models\User;
use Illuminate\Http\Request;

class GuessTheGameController extends Controller
{
    /**
     * Fetch a challenge for Guess the Game.
     *
     * WHY: Retrieves a random challenge from the 'GTG source' study owned by 'vonchess',
     *      falling back to specific challenge_id if requested.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDailyChallenge(Request $request)
    {
        $user = User::whereRaw('LOWER(name) = ?', ['vonchess'])->first();
        if (!$user) {
            return response()->json(['error' => 'No challenges available. Please ensure the user "vonchess" exists.'], 404);
        }

        $study = Study::where('user_id', $user->id)
            ->whereRaw('LOWER(name) = ?', [strtolower('GTG source')])
            ->first();

        if (!$study) {
            return response()->json(['error' => 'No challenges available. Please ensure the study "GTG source" exists for user "vonchess".'], 404);
        }

        $challengeId = $request->query('challenge_id');
        if ($challengeId) {
            $chapter = StudyChapter::where('id', $challengeId)
                ->where('study_id', $study->id)
                ->first();
            if ($chapter) {
                return response()->json(['data' => $this->transformChapterToChallenge($chapter)]);
            }
            return response()->json(['error' => 'Challenge not found.'], 404);
        }

        $chapters = $study->chapters()->get();
        if ($chapters->isEmpty()) {
            return response()->json(['error' => 'No challenges available. Please ensure the study "GTG source" has chapters.'], 404);
        }

        $chapter = $chapters->random();
        return response()->json(['data' => $this->transformChapterToChallenge($chapter)]);
    }

    /**
     * Get the next random challenge from the linked study.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNextChallenge(Request $request)
    {
        $currentId = $request->query('current_id');
        
        $user = User::whereRaw('LOWER(name) = ?', ['vonchess'])->first();
        if (!$user) {
            return response()->json(['error' => 'No challenges available. Please ensure the user "vonchess" exists.'], 404);
        }

        $study = Study::where('user_id', $user->id)
            ->whereRaw('LOWER(name) = ?', [strtolower('GTG source')])
            ->first();

        if (!$study) {
            return response()->json(['error' => 'No challenges available. Please ensure the study "GTG source" exists for user "vonchess".'], 404);
        }

        $query = $study->chapters();
        if ($currentId) {
            $query->where('id', '!=', $currentId);
        }
        $chapters = $query->get();

        if ($chapters->isEmpty()) {
            // If there's only 1 chapter total, fallback to returning the current one
            if ($currentId) {
                $chapter = StudyChapter::where('id', $currentId)
                    ->where('study_id', $study->id)
                    ->first();
            } else {
                $chapter = null;
            }
            
            if (!$chapter) {
                return response()->json(['error' => 'No other challenges available.'], 404);
            }
        } else {
            $chapter = $chapters->random();
        }

        return response()->json(['data' => $this->transformChapterToChallenge($chapter)]);
    }

    /**
     * Transform a StudyChapter model to the GuessTheGameChallenge shape.
     */
    private function transformChapterToChallenge(StudyChapter $chapter)
    {
        $tags = $chapter->pgn_tags ?? [];
        
        // Extract year from Date or Year tag (e.g. "1999.01.20" -> 1999)
        $year = (int) date('Y');
        if (!empty($tags['Date'])) {
            $parts = explode('.', $tags['Date']);
            if (count($parts) > 0 && is_numeric($parts[0])) {
                $year = (int) $parts[0];
            }
        } elseif (!empty($tags['Year'])) {
            $year = (int) $tags['Year'];
        }

        // Extract PGN moves
        $moves = $chapter->moves;
        $pgn = '';
        if (isset($moves['pgn'])) {
            $pgn = $moves['pgn'];
        } elseif (is_array($moves)) {
            $pgn = $this->serializeMoveTree($moves);
        }

        // Clean headers from PGN
        $pgn = preg_replace('/\[.*?\]\s*/', '', $pgn);
        $pgn = preg_replace('/\s*\.\.\.\s*$/', '', $pgn);
        $pgn = trim($pgn);

        $result = $tags['Result'] ?? '*';

        $startPly = null;
        foreach ($tags as $key => $value) {
            $lowerKey = strtolower($key);
            if ($lowerKey === 'startply' || $lowerKey === 'guessstartply') {
                if (is_numeric($value)) {
                    $startPly = (int)$value;
                    break;
                }
            } elseif ($lowerKey === 'startmove' || $lowerKey === 'guessstartmove') {
                if (is_numeric($value)) {
                    $startPly = ((int)$value - 1) * 2;
                    break;
                }
            }
        }

        return [
            'id' => $chapter->id,
            'white_player' => $tags['White'] ?? 'Unknown White',
            'black_player' => $tags['Black'] ?? 'Unknown Black',
            'white_rating' => $tags['WhiteElo'] ?? null,
            'black_rating' => $tags['BlackElo'] ?? null,
            'event' => $tags['Event'] ?? ($chapter->study->name ?? 'Unknown Event'),
            'year' => $year,
            'eco' => $tags['ECO'] ?? null,
            'result' => $result,
            'pgn' => $pgn,
            'active_date' => null,
            'is_study_chapter' => true,
            'study_id' => $chapter->study_id,
            'study_link' => $tags['StudyLink'] ?? null,
            'initial_fen' => $chapter->initial_fen,
            'start_ply' => $startPly,
        ];
    }

    /**
     * Recursively serializes a UI-edited moves tree (array of MoveNode) to a PGN string.
     */
    private function serializeMoveTree(array $nodes, int $lastPly = 0, bool $forceNumber = true): string
    {
        $pgn = "";
        foreach ($nodes as $node) {
            $ply = $node['ply'] ?? ($lastPly + 1);
            $isWhite = ($ply % 2 !== 0);
            
            // 1. Pre-comments
            if (!empty($node['preComments'])) {
                foreach ($node['preComments'] as $comment) {
                    $pgn .= "{ " . trim($comment) . " } ";
                }
                $forceNumber = true;
            }
            
            // 2. Move number
            if ($isWhite) {
                $moveNum = ceil($ply / 2);
                $pgn .= $moveNum . ". ";
            } elseif ($forceNumber) {
                $moveNum = ceil($ply / 2);
                $pgn .= $moveNum . "... ";
            }
            
            // 3. Move SAN
            $pgn .= ($node['san'] ?? '') . " ";
            
            // 4. Glyphs / NAGs (optional)
            if (!empty($node['glyphs'])) {
                foreach ($node['glyphs'] as $glyph) {
                    if (is_array($glyph) && isset($glyph['symbol'])) {
                        $pgn .= $glyph['symbol'] . " ";
                    } elseif (is_numeric($glyph)) {
                        $pgn .= "$" . $glyph . " ";
                    } elseif (is_string($glyph)) {
                        $pgn .= $glyph . " ";
                    }
                }
            }
            
            // 5. Post-comments
            if (!empty($node['comments'])) {
                foreach ($node['comments'] as $comment) {
                    $pgn .= "{ " . trim($comment) . " } ";
                }
                $forceNumber = true;
            } else {
                $forceNumber = false;
            }
            
            // 6. Variations
            if (!empty($node['variations'])) {
                foreach ($node['variations'] as $variation) {
                    if (!empty($variation)) {
                        $pgn .= "( " . trim($this->serializeMoveTree($variation, $ply - 1, true)) . " ) ";
                        $forceNumber = true;
                    }
                }
            }
            
            // 7. Children (mainline continuation)
            if (!empty($node['children'])) {
                $pgn .= $this->serializeMoveTree($node['children'], $ply, $forceNumber);
            }
        }
        return trim($pgn);
    }
}
