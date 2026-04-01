<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TournamentResource;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TournamentController extends Controller
{
    /**
     * Generate a unique slug, appending a numeric suffix if a collision is detected.
     */
    private function generateUniqueSlug(string $baseSlug, ?int $excludeId = null): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = Tournament::where('slug', $slug);
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
        $tournaments = Tournament::where('created_by', $request->user()->id)
            ->orderBy('start_date', 'desc')
            ->get();

        return TournamentResource::collection($tournaments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'status' => 'required|in:upcoming,ongoing,past',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'registration_deadline' => 'nullable|date|before_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'format' => 'nullable|string|max:255',
            'time_control' => 'nullable|string|max:255',
            'entry_fee' => 'nullable|string|max:255',
            'prize_pool' => 'nullable|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'contact_email' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:2048',
            'description' => 'nullable|string',
            'registration_instructions' => 'nullable|string',
            'rounds' => 'nullable|integer|min:0',
            'current_participants' => 'nullable|integer|min:0',
            'max_participants' => 'nullable|integer|min:0',
            'eligibility' => 'nullable|array',
            'categories' => 'nullable|array',
            'schedule' => 'nullable|array',
            'winner' => 'nullable|string|max:255',
            'standings' => 'nullable|array',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['slug'] = $this->generateUniqueSlug($validated['slug']);

        $validated['created_by'] = $request->user()->id;

        $tournament = Tournament::create($validated);

        return (new TournamentResource($tournament))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, $id)
    {
        $tournament = Tournament::where('slug', $id)->firstOrFail();

        if ($tournament->created_by !== $request->user()->id) {
            throw new AccessDeniedHttpException('You do not have permission to view this tournament.');
        }

        return new TournamentResource($tournament);
    }

    public function update(Request $request, $id)
    {
        $tournament = Tournament::where('slug', $id)->firstOrFail();

        if ($tournament->created_by !== $request->user()->id) {
            throw new AccessDeniedHttpException('You do not have permission to edit this tournament.');
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:upcoming,ongoing,past',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'registration_deadline' => 'nullable|date|before_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'format' => 'nullable|string|max:255',
            'time_control' => 'nullable|string|max:255',
            'entry_fee' => 'nullable|string|max:255',
            'prize_pool' => 'nullable|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'contact_email' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:2048',
            'description' => 'nullable|string',
            'registration_instructions' => 'nullable|string',
            'rounds' => 'nullable|integer|min:0',
            'current_participants' => 'nullable|integer|min:0',
            'max_participants' => 'nullable|integer|min:0',
            'eligibility' => 'nullable|array',
            'categories' => 'nullable|array',
            'schedule' => 'nullable|array',
            'winner' => 'nullable|string|max:255',
            'standings' => 'nullable|array',
        ]);

        if (empty($validated['slug']) && isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if (isset($validated['slug'])) {
            $validated['slug'] = $this->generateUniqueSlug($validated['slug'], $tournament->id);
        }

        $tournament->update($validated);

        return new TournamentResource($tournament);
    }

    public function destroy(Request $request, $id)
    {
        $tournament = Tournament::where('slug', $id)->firstOrFail();

        if ($tournament->created_by !== $request->user()->id) {
            throw new AccessDeniedHttpException('You do not have permission to delete this tournament.');
        }

        $tournament->delete();

        return response()->json(['message' => 'Tournament deleted']);
    }
}
