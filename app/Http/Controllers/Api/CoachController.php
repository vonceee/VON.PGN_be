<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CoachResource;
use App\Models\Coach;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CoachController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return CoachResource::collection(Coach::all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): CoachResource | JsonResponse
    {
        $coach = Coach::find($id);

        if (!$coach) {
            return response()->json(['message' => 'Coach not found'], 404);
        }

        return new CoachResource($coach);
    }
}
