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
            $query->where('user_id', Auth::id());
        } else {
            $query->where('visibility', 'public')
                  ->orWhere('user_id', Auth::id());
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
}
