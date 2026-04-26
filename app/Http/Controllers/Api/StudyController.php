<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Study;
use App\Models\StudyChapter;
use App\Http\Resources\StudyResource;
use App\Http\Resources\StudyChapterResource;
use App\Http\Requests\StoreStudyRequest;
use App\Http\Requests\UpdateStudyRequest;
use App\Http\Requests\ImportPgnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StudyController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of public studies.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $query = Study::with(['owner', 'collaborators'])->withCount('chapters')->orderBy('updated_at', 'desc');

        if ($request->has('my')) {
            abort_if(!$user, 401, 'Authentication required');
            $query->where('user_id', $user->id);
        } else {
            $query->where('visibility', 'public');
        }

        return StudyResource::collection($query->paginate(20));
    }

    /**
     * Store a newly created study in storage.
     */
    public function store(StoreStudyRequest $request)
    {
        $study = Study::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'visibility' => $request->visibility,
        ]);

        // Create an initial empty chapter
        $study->chapters()->create([
            'name' => 'Chapter 1',
            'order' => 1,
        ]);

        return new StudyResource($study->load(['owner', 'chapters']));
    }

    /**
     * Display the specified study.
     */
    public function show(Study $study)
    {
        if ($user = Auth::guard('sanctum')->user()) {
            Auth::setUser($user);
        }

        $this->authorize('view', $study);

        return new StudyResource($study->load(['owner', 'chapters', 'collaborators']));
    }

    /**
     * Update the specified study in storage.
     */
    public function update(UpdateStudyRequest $request, Study $study)
    {
        $this->authorize('update', $study);

        $study->update($request->validated());

        return new StudyResource($study);
    }

    /**
     * Remove the specified study from storage.
     */
    public function destroy(Study $study)
    {
        $this->authorize('delete', $study);

        $study->delete();

        return response()->json(['message' => 'Study deleted successfully']);
    }

    /**
     * Add a chapter to the study.
     */
    public function addChapter(Request $request, Study $study)
    {
        $this->authorize('manageChapters', $study);

        $request->validate([
            'name' => 'required|string|max:255',
            'initial_fen' => 'nullable|string',
            'orientation' => 'nullable|string|in:white,black',
        ]);

        $order = $study->chapters()->max('order') + 1;

        $chapter = $study->chapters()->create([
            'name' => $request->name,
            'initial_fen' => $request->initial_fen ?? 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1',
            'current_fen' => $request->initial_fen ?? 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1',
            'orientation' => $request->orientation ?? 'white',
            'order' => $order,
        ]);

        return new StudyChapterResource($chapter);
    }

    /**
     * Update a chapter's content.
     */
    public function updateChapter(Request $request, Study $study, StudyChapter $chapter)
    {
        $this->authorize('manageChapters', $study);

        if ($chapter->study_id !== $study->id) {
            return response()->json(['message' => 'Chapter does not belong to this study'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'current_fen' => 'sometimes|required|string',
            'orientation' => 'sometimes|required|string|in:white,black',
            'moves' => 'sometimes|array|nullable',
        ]);

        $chapter->update($request->all());

        return new StudyChapterResource($chapter);
    }

    /**
     * Delete a chapter from the study.
     */
    public function deleteChapter(Study $study, StudyChapter $chapter)
    {
        $this->authorize('manageChapters', $study);

        if ($chapter->study_id !== $study->id) {
            return response()->json(['message' => 'Chapter does not belong to this study'], 404);
        }

        $chapter->delete();

        return response()->json(['message' => 'Chapter deleted successfully']);
    }

    /**
     * Import a multi-game PGN into the study.
     */
    public function importPgn(ImportPgnRequest $request, Study $study)
    {
        $this->authorize('manageChapters', $study);

        $pgn = $request->pgn;
        
        try {
            return DB::transaction(function () use ($pgn, $study) {
                // Normalize newlines
                $pgn = str_replace("\r\n", "\n", $pgn);
                
                // Split PGN by games. Improved regex to handle multiple newlines and whitespace.
                $games = preg_split('/\n\s*\n(?=\[)/', trim($pgn));
                
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
                    'study' => new StudyResource($study->load(['owner', 'chapters']))
                ]);
            });
        } catch (\Exception $e) {
            Log::error("PGN Import Failed for Study {$study->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'pgn_length' => strlen($pgn ?? ''),
            ]);
            
            $message = 'Failed to import PGN. Please ensure the format is valid.';
            $response = ['message' => $message];
            
            if (config('app.debug')) {
                $response['debug'] = [
                    'exception' => $e->getMessage(),
                    'file' => $e->getFile() . ':' . $e->getLine(),
                ];
            }
            
            return response()->json($response, 500);
        }
    }

    /**
     * Export the study as a multi-game PGN.
     */
    public function exportPgn(Study $study)
    {
        $user = Auth::guard('sanctum')->user();

        // Check visibility
        if ($study->visibility === 'private' && (!$user || $study->user_id !== $user->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $study->load('chapters');
        $pgn = "";
        $userName = $user ? $user->name : 'Unknown';

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

    /**
     * Add a collaborator to the study.
     */
    public function addCollaborator(Request $request, Study $study)
    {
        $this->authorize('update', $study);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $study->collaborators()->syncWithoutDetaching([$request->user_id]);

        return new StudyResource($study->load('collaborators'));
    }

    /**
     * Remove a collaborator from the study.
     */
    public function removeCollaborator(Study $study, $userId)
    {
        $this->authorize('update', $study);

        $study->collaborators()->detach($userId);

        return response()->json(['message' => 'Collaborator removed successfully']);
    }

    /**
     * Update collaborator permissions.
     */
    public function updateCollaborator(Request $request, Study $study, $userId)
    {
        $this->authorize('update', $study);

        $request->validate([
            'can_edit' => 'required|boolean',
        ]);

        $study->collaborators()->updateExistingPivot($userId, [
            'can_edit' => $request->can_edit,
        ]);

        return new StudyResource($study->load('collaborators'));
    }
}
