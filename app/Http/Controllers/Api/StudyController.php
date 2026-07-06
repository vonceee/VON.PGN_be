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
use App\Notifications\CollaboratorAddedNotification;
use App\Models\User;

class StudyController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of public studies.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $relations = ['owner'];
        if ($request->has('include')) {
            $includes = explode(',', $request->include);
            if (in_array('chapters', $includes)) {
                $relations[] = 'chapters';
            }
        }
        $query = Study::with($relations)->withCount('chapters');

        if ($request->has('my')) {
            abort_if(!$user, 401, 'Authentication required');
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('collaborators', function ($c) use ($user) {
                      $c->where('users.id', $user->id);
                  });
            });
        } else {
            $query->where('visibility', 'public');
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($ownerQ) use ($search) {
                      $ownerQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $sort = $request->input('sort', 'last_updated');
        if ($sort === 'alphabetical') {
            $query->orderBy('name', 'asc');
        } else {
            $query->orderBy('updated_at', 'desc');
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
            'engine_visibility' => $request->engine_visibility ?? 'everyone',
            'export_visibility' => $request->export_visibility ?? 'owner',
            'category' => $request->category ?? 'general',
            'orientation' => $request->orientation ?? 'white',
        ]);

        // Create an initial empty chapter inheriting the study's orientation
        $study->chapters()->create([
            'name' => 'Chapter 1',
            'orientation' => $request->orientation ?? 'white',
            'order' => 1,
        ]);

        $this->syncStudyPreview($study);

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

        $oldOrientation = $study->orientation;
        $study->update($request->validated());

        // If it's an opening repertoire and the orientation changed, sync all chapters
        if ($study->category === 'opening_repertoire' && $request->has('orientation') && $request->orientation !== $oldOrientation) {
            $study->chapters()->update(['orientation' => $request->orientation]);
        }

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
            'orientation' => $request->orientation ?? $study->orientation ?? 'white',
            'order' => $order,
        ]);

        $this->syncStudyPreview($study);

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
            'pgn_tags' => 'sometimes|array|nullable',
        ]);

        $chapter->update($request->all());

        $this->syncStudyPreview($study);

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

        $this->syncStudyPreview($study);

        return response()->json(['message' => 'Chapter deleted successfully']);
    }

    /**
     * Reorder chapters in the study.
     */
    public function reorderChapters(Request $request, Study $study)
    {
        $this->authorize('manageChapters', $study);

        $request->validate([
            'chapter_ids' => 'required|array',
            'chapter_ids.*' => 'exists:study_chapters,id'
        ]);

        $chapterIds = $request->chapter_ids;

        DB::transaction(function () use ($chapterIds, $study) {
            foreach ($chapterIds as $index => $id) {
                StudyChapter::where('id', $id)
                    ->where('study_id', $study->id)
                    ->update(['order' => $index + 1]);
            }
        });

        $this->syncStudyPreview($study);

        return response()->json(['message' => 'Chapters reordered successfully']);
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
                
                // Fix missing spaces after move numbers: "1.d4" -> "1. d4", "1...Nf6" -> "1... Nf6"
                // This helps the frontend tokenizer and other PGN tools parse the moves correctly.
                $pgn = preg_replace('/(\d+\.{1,3})([^\s])/', '$1 $2', $pgn);
                
                // Split PGN by games. Improved regex to handle multiple newlines and whitespace.
                $games = preg_split('/\n\s*\n(?=\[)/', trim($pgn));
                
                $importedCount = 0;
                $order = $study->chapters()->max('order') ?? 0;

                foreach ($games as $gameContent) {
                    if (empty(trim($gameContent))) continue;

                    // Extract tags using a more robust regex that handles escaped quotes
                    $tags = [];
                    preg_match_all('/\[(\w+)\s+"((?:[^"\\\\]|\\\\.)*)"\]/', $gameContent, $matches, PREG_SET_ORDER);
                    foreach ($matches as $match) {
                        $tags[$match[1]] = stripslashes($match[2]);
                    }

                    // Determine chapter name: prioritized tags
                    $name = $tags['ChapterName'] ?? null;
                    
                    if (!$name && isset($tags['White']) && isset($tags['Black'])) {
                        $name = $tags['White'] . ' - ' . $tags['Black'];
                    }
                    
                    if (!$name) {
                        $name = $tags['Event'] ?? ('Chapter ' . ($order + 1));
                    }

                    if (isset($tags['StudyName']) && str_starts_with($name, $tags['StudyName'])) {
                        $name = trim(str_replace($tags['StudyName'] . ':', '', $name));
                        if (empty($name)) $name = $tags['ChapterName'] ?? 'Untitled';
                    }

                    $initialFen = $tags['FEN'] ?? 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';

                    // Parse chapter orientation from PGN tags or inherit from study default
                    $orientation = $study->orientation ?? 'white';
                    if (isset($tags['Orientation'])) {
                        $orientation = strtolower($tags['Orientation']) === 'black' ? 'black' : 'white';
                    } elseif (isset($tags['orientation'])) {
                        $orientation = strtolower($tags['orientation']) === 'black' ? 'black' : 'white';
                    }

                    $study->chapters()->create([
                        'name' => $name,
                        'initial_fen' => $initialFen,
                        'current_fen' => $initialFen,
                        'orientation' => $orientation,
                        'moves' => ['pgn' => $gameContent],
                        'pgn_tags' => $tags,
                        'order' => ++$order,
                    ]);

                    $importedCount++;
                }

                $this->syncStudyPreview($study);

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

        // Check export visibility
        if (($study->export_visibility ?? 'owner') === 'owner' && (!$user || $study->user_id !== $user->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $chapterIdsInput = request()->query('chapter_ids');
        if ($chapterIdsInput) {
            if (is_string($chapterIdsInput)) {
                $chapterIds = explode(',', $chapterIdsInput);
            } else {
                $chapterIds = (array)$chapterIdsInput;
            }
            $chapters = $study->chapters()->whereIn('id', $chapterIds)->orderBy('order')->get();
        } else {
            $chapters = $study->chapters()->orderBy('order')->get();
        }

        $pgn = "";
        $userName = $user ? $user->name : 'Unknown';

        foreach ($chapters as $chapter) {
            $tags = $chapter->pgn_tags ?? [];
            
            // Standard/override tags
            $tags['Event'] = $study->name . ": " . $chapter->name;
            $tags['Site'] = $tags['Site'] ?? 'VON.CHESS';
            $tags['Date'] = $tags['Date'] ?? now()->format('Y.m.d');
            $tags['Round'] = $tags['Round'] ?? '?';
            $tags['White'] = $tags['White'] ?? '?';
            $tags['Black'] = $tags['Black'] ?? '?';
            
            $result = $tags['Result'] ?? '*';
            $tags['Result'] = $result;
            
            if ($chapter->initial_fen !== 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1') {
                $tags['SetUp'] = '1';
                $tags['FEN'] = $chapter->initial_fen;
            } else {
                unset($tags['SetUp']);
                unset($tags['FEN']);
            }
            
            $tags['StudyName'] = $study->name;
            $tags['ChapterName'] = $chapter->name;
            $tags['Annotator'] = $userName;

            foreach ($tags as $key => $value) {
                $pgn .= "[" . $key . " \"" . $value . "\"]\n";
            }
            $pgn .= "\n";

            // Moves
            $moves = $chapter->moves;
            if (isset($moves['pgn'])) {
                $pgn .= $moves['pgn'];
            } elseif (is_array($moves)) {
                $serialized = $this->serializeMoveTree($moves);
                $pgn .= !empty($serialized) ? $serialized . " " . $result : $result;
            } else {
                $pgn .= $result;
            }
            
            $pgn .= "\n\n";
        }

        return response($pgn)
            ->header('Content-Type', 'application/x-chess-pgn')
            ->header('Content-Disposition', 'attachment; filename="' . str_replace(' ', '_', $study->name) . '.pgn"');
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

    /**
     * Add a collaborator to the study.
     */
    public function addCollaborator(Request $request, Study $study)
    {
        try {
            $this->authorize('update', $study);

            $request->validate([
                'user_id' => 'required|exists:users,id',
                'can_edit' => 'sometimes|boolean',
            ]);

            $pivotData = [];
            if ($request->has('can_edit')) {
                $pivotData['can_edit'] = DB::raw($request->can_edit ? 'true' : 'false');
            }

            $study->collaborators()->syncWithoutDetaching([
                $request->user_id => $pivotData
            ]);

            // Send notification
            $user = User::find($request->user_id);
            if ($user) {
                $user->notify(new CollaboratorAddedNotification($study->load('owner')));
            }

            return new StudyResource($study->load('collaborators'));
        } catch (\Exception $e) {
            Log::error("Add Collaborator Failed: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Failed to add collaborator.',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => explode("\n", $e->getTraceAsString())
            ], 500);
        }
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
            'can_edit' => DB::raw($request->can_edit ? 'true' : 'false'),
        ]);

        return new StudyResource($study->load('collaborators'));
    }

    /**
     * Get chat messages for the study lobby.
     */
    public function messages(Study $study)
    {
        $this->authorize('view', $study);

        $messages = $study->messages()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->reverse()
            ->values();

        return response()->json($messages);
    }

    /**
     * Store a new chat message in the study lobby.
     */
    public function sendMessage(Request $request, Study $study)
    {
        $this->authorize('view', $study);

        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $message = $study->messages()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        return response()->json($message->load('user:id,name'));
    }

    /**
     * Clear all chat messages from the study lobby.
     */
    public function clearMessages(Study $study)
    {
        $this->authorize('update', $study);

        $study->messages()->delete();

        return response()->json(['message' => 'Chat cleared successfully']);
    }

    private function syncStudyPreview(Study $study)
    {
        $firstChapter = $study->chapters()->orderBy('order')->first();
        if (!$firstChapter) {
            $study->update([
                'preview_fen' => 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1',
                'preview_last_move' => null,
            ]);
            return;
        }

        $moves = $firstChapter->moves ?? [];
        $initialFen = $firstChapter->initial_fen ?? 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';

        $previewFen = $initialFen;
        $previewLastMove = null;

        if (is_array($moves) && count($moves) > 0) {
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
