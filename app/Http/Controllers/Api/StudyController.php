<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Study;
use App\Models\StudyChapter;
use App\Http\Resources\StudyResource;
use App\Http\Resources\StudyChapterResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyController extends Controller
{
    /**
     * Display a listing of public studies.
     */
    public function index(Request $request)
    {
        $query = Study::withCount('chapters')->orderBy('updated_at', 'desc');

        if ($request->has('my')) {
            abort_if(!Auth::check(), 401, 'Authentication required');
            $query->where('user_id', Auth::id());
        } else {
            $query->where('visibility', 'public');
        }

        return StudyResource::collection($query->paginate(20));
    }

    /**
     * Store a newly created study in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|in:public,private,unlisted',
        ]);

        $study = Study::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'visibility' => $request->visibility,
        ]);

        // Create an initial empty chapter
        $study->chapters()->create([
            'name' => 'Chapter 1',
            'order' => 1,
        ]);

        return new StudyResource($study->load('chapters'));
    }

    /**
     * Display the specified study.
     */
    public function show(Study $study)
    {
        // Check visibility
        if ($study->visibility === 'private' && $study->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new StudyResource($study->load('chapters'));
    }

    /**
     * Update the specified study in storage.
     */
    public function update(Request $request, Study $study)
    {
        if ($study->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'sometimes|required|in:public,private,unlisted',
        ]);

        $study->update($request->all());

        return new StudyResource($study);
    }

    /**
     * Remove the specified study from storage.
     */
    public function destroy(Study $study)
    {
        if ($study->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $study->delete();

        return response()->json(['message' => 'Study deleted successfully']);
    }

    /**
     * Add a chapter to the study.
     */
    public function addChapter(Request $request, Study $study)
    {
        if ($study->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'initial_fen' => 'nullable|string',
        ]);

        $order = $study->chapters()->max('order') + 1;

        $chapter = $study->chapters()->create([
            'name' => $request->name,
            'initial_fen' => $request->initial_fen ?? 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1',
            'current_fen' => $request->initial_fen ?? 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1',
            'order' => $order,
        ]);

        return new StudyChapterResource($chapter);
    }

    /**
     * Update a chapter's content.
     */
    public function updateChapter(Request $request, Study $study, StudyChapter $chapter)
    {
        if ($study->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($chapter->study_id !== $study->id) {
            return response()->json(['message' => 'Chapter does not belong to this study'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'current_fen' => 'sometimes|required|string',
            'moves' => 'sometimes|required|array',
        ]);

        $chapter->update($request->all());

        return new StudyChapterResource($chapter);
    }

    /**
     * Delete a chapter from the study.
     */
    public function deleteChapter(Study $study, StudyChapter $chapter)
    {
        if ($study->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($chapter->study_id !== $study->id) {
            return response()->json(['message' => 'Chapter does not belong to this study'], 404);
        }

        $chapter->delete();

        return response()->json(['message' => 'Chapter deleted successfully']);
    }

    /**
     * Import a multi-game PGN into the study.
     */
    public function importPgn(Request $request, Study $study)
    {
        if ($study->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'pgn' => 'required|string',
        ]);

        $pgn = $request->pgn;
        
        return DB::transaction(function () use ($pgn, $study) {
            // Normalize newlines
            $pgn = str_replace("\r\n", "\n", $pgn);
            
            // Split PGN by games.
            $games = preg_split('/\n\n(?=\[)/', trim($pgn));
            
            $importedCount = 0;
            $order = $study->chapters()->max('order') ?? 0;

            foreach ($games as $gameContent) {
                if (empty(trim($gameContent))) continue;

                // Extract tags
                $tags = [];
                preg_match_all('/\[(\w+)\s+"(.*)"\]/', $gameContent, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $tags[$match[1]] = $match[2];
                }

                $name = $tags['ChapterName'] ?? $tags['Event'] ?? ('Chapter ' . ($order + 1));
                if (isset($tags['StudyName']) && str_starts_with($name, $tags['StudyName'])) {
                    $name = trim(str_replace($tags['StudyName'] . ':', '', $name));
                    if (empty($name)) $name = $tags['ChapterName'] ?? 'Untitled';
                }

                $initialFen = $tags['FEN'] ?? 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';

                $study->chapters()->create([
                    'name' => $name,
                    'initial_fen' => $initialFen,
                    'current_fen' => $initialFen,
                    'moves' => ['pgn' => $gameContent],
                    'order' => ++$order,
                ]);

                $importedCount++;
            }

            return response()->json([
                'message' => "Successfully imported {$importedCount} chapters.",
                'study' => new StudyResource($study->load('chapters'))
            ]);
        });
    }

    /**
     * Export the study as a multi-game PGN.
     */
    public function exportPgn(Study $study)
    {
        // Check visibility
        if ($study->visibility === 'private' && $study->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $study->load('chapters');
        $pgn = "";
        $userName = Auth::user()->name ?? 'Unknown';

        foreach ($study->chapters as $chapter) {
            $pgn .= "[Event \"" . ($study->name . ": " . $chapter->name) . "\"]\n";
            $pgn .= "[Site \"VON.CHESS\"]\n";
            $pgn .= "[Date \"" . now()->format('Y.m.d') . "\"]\n";
            $pgn .= "[Round \"?\"]\n";
            $pgn .= "[White \"?\"]\n";
            $pgn .= "[Black \"?\"]\n";
            $pgn .= "[Result \"*\"]\n";
            if ($chapter->initial_fen !== 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1') {
                $pgn .= "[SetUp \"1\"]\n";
                $pgn .= "[FEN \"" . $chapter->initial_fen . "\"]\n";
            }
            $pgn .= "[StudyName \"" . $study->name . "\"]\n";
            $pgn .= "[ChapterName \"" . $chapter->name . "\"]\n";
            $pgn .= "[Annotator \"" . $userName . "\"]\n\n";

            // Moves
            $moves = $chapter->moves;
            if (isset($moves['pgn'])) {
                $pgn .= $moves['pgn'];
            } else {
                // If it's a tree, we'd need to serialize it back to PGN.
                // For now, if it's already a chapter created via UI, we might just have a flat array or tree.
                // We'll need a helper to serialize MoveNode[] -> PGN string.
                $pgn .= "*"; 
            }
            
            $pgn .= "\n\n";
        }

        return response($pgn)
            ->header('Content-Type', 'application/x-chess-pgn')
            ->header('Content-Disposition', 'attachment; filename="' . str_replace(' ', '_', $study->name) . '.pgn"');
    }
}
