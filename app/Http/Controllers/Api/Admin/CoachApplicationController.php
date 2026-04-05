<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CoachApplicationResource;
use App\Models\CoachApplication;
use Illuminate\Http\Request;

/**
 * Admin controller for managing coach profile submissions/listings
 */
class CoachApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = CoachApplication::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->orderBy('submitted_at', 'desc')->get();

        return CoachApplicationResource::collection($applications);
    }

    public function show($id)
    {
        $application = CoachApplication::findOrFail($id);
        return new CoachApplicationResource($application);
    }

    public function approve($id)
    {
        $application = CoachApplication::findOrFail($id);
        $application->update(['status' => 'approved']);

        return new CoachApplicationResource($application);
    }

    public function reject($id)
    {
        $application = CoachApplication::findOrFail($id);
        $application->update(['status' => 'rejected']);

        return new CoachApplicationResource($application);
    }

    public function destroy($id)
    {
        $application = CoachApplication::findOrFail($id);
        $application->delete();

        return response()->json(['message' => 'Coach application deleted']);
    }
}