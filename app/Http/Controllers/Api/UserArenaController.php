<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArenaResource;
use App\Models\Arena;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserArenaController extends Controller
{
    private function generateUniqueSlug(string $baseSlug, ?int $excludeId = null): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = Arena::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            if (!$query->exists()) {
                return $slug;
            }
            $counter++;
            $slug = $baseSlug . '-' . $counter;
        }
    }

    public function index(Request $request)
    {
        $arenas = Arena::where('created_by', $request->user()->id)
            ->orderBy('start_date', 'desc')
            ->get();

        return ArenaResource::collection($arenas);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'status' => 'required|in:upcoming,ongoing,past',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'time_control' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:5|max:360',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['slug'] = $this->generateUniqueSlug($validated['slug']);
        $validated['created_by'] = $request->user()->id;

        $arena = Arena::create($validated);

        return (new ArenaResource($arena))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $id)
    {
        $arena = Arena::where('slug', $id)->firstOrFail();

        if ($arena->created_by !== $request->user()->id) {
            throw new AccessDeniedHttpException('You do not have permission to view this arena.');
        }

        return new ArenaResource($arena);
    }

    public function update(Request $request, string $id)
    {
        $arena = Arena::where('slug', $id)->firstOrFail();

        if ($arena->created_by !== $request->user()->id) {
            throw new AccessDeniedHttpException('You do not have permission to edit this arena.');
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:upcoming,ongoing,past',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'time_control' => 'sometimes|required|string|max:255',
            'duration_minutes' => 'sometimes|required|integer|min:5|max:360',
        ]);

        if (empty($validated['slug']) && isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if (isset($validated['slug'])) {
            $validated['slug'] = $this->generateUniqueSlug($validated['slug'], $arena->id);
        }

        $arena->update($validated);

        return new ArenaResource($arena);
    }

    public function destroy(Request $request, string $id)
    {
        $arena = Arena::where('slug', $id)->firstOrFail();

        if ($arena->created_by !== $request->user()->id) {
            throw new AccessDeniedHttpException('You do not have permission to delete this arena.');
        }

        $arena->delete();

        return response()->json(['message' => 'Arena deleted']);
    }
}
