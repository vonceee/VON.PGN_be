<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LessonController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:lessons',
            'content_blocks' => 'nullable|array',
            'order' => 'required|integer',
            'xp_reward' => 'required|integer',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if (!isset($validated['content_blocks'])) {
            $validated['content_blocks'] = [];
        }

        $lesson = Lesson::create($validated);
        return response()->json($lesson, 201);
    }

    public function show($id)
    {
        $lesson = Lesson::findOrFail($id);
        return response()->json($lesson);
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:lessons,slug,' . $lesson->id,
            'content_blocks' => 'nullable|array',
            'order' => 'sometimes|required|integer',
            'xp_reward' => 'sometimes|required|integer',
        ]);

        if (empty($validated['slug']) && isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if (array_key_exists('content_blocks', $validated) && is_null($validated['content_blocks'])) {
            $validated['content_blocks'] = [];
        }

        $lesson->update($validated);
        return response()->json($lesson);
    }

    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();
        return response()->json(['message' => 'Lesson deleted']);
    }
}
