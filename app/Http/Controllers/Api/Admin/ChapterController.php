<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'order' => 'required|integer',
        ]);

        $chapter = Chapter::create($validated);
        return response()->json($chapter, 201);
    }

    public function show($id)
    {
        $chapter = Chapter::with('lessons')->findOrFail($id);
        return response()->json($chapter);
    }

    public function update(Request $request, $id)
    {
        $chapter = Chapter::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'order' => 'sometimes|required|integer',
        ]);

        $chapter->update($validated);
        return response()->json($chapter);
    }

    public function destroy($id)
    {
        $chapter = Chapter::findOrFail($id);
        $chapter->delete();
        return response()->json(['message' => 'Chapter deleted']);
    }
}
