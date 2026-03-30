<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TournamentResource;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::orderBy('start_date', 'desc')->get();
        return TournamentResource::collection($tournaments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tournaments',
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

        $tournament = Tournament::create($validated);

        return (new TournamentResource($tournament))
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        $tournament = Tournament::where('slug', $id)->firstOrFail();
        return new TournamentResource($tournament);
    }

    public function update(Request $request, $id)
    {
        $tournament = Tournament::where('slug', $id)->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:tournaments,slug,' . $tournament->id,
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

        $tournament->update($validated);

        return new TournamentResource($tournament);
    }

    public function destroy($id)
    {
        $tournament = Tournament::where('slug', $id)->firstOrFail();
        $tournament->delete();

        return response()->json(['message' => 'Tournament deleted']);
    }
}
