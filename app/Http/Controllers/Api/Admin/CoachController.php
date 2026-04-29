<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CoachResource;
use App\Models\Coach;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CoachController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return CoachResource::collection(Coach::orderBy('created_at', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse | CoachResource
    {
        $validated = $request->validate([
            'id' => 'nullable|string|unique:coaches,id',
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'short_info' => 'required|string',
            'fide_rating' => 'nullable|integer',
            'profile_picture' => 'required', // Can be string (URL) or File
            'is_academy_instructor' => 'boolean',
            'playing_experience' => 'required|array',
            'teaching_experience' => 'required|array',
            'bio' => 'required|string',
            'location' => 'required|string',
            'availability' => 'required|string',
            'teaching_methods' => 'required|array',
            'coaching_type' => 'required|string',
            'social_media' => 'required|array',
        ]);

        if (empty($validated['id'])) {
            $validated['id'] = Str::slug($validated['name']);
            
            // Handle duplicate slugs
            $count = 1;
            while (Coach::where('id', $validated['id'])->exists()) {
                $validated['id'] = Str::slug($validated['name']) . '-' . $count;
                $count++;
            }
        }

        // Handle File Upload to Cloudinary
        if ($request->hasFile('profile_picture')) {
            try {
                $file = $request->file('profile_picture');
                \Log::info('Attempting Cloudinary upload for new coach', ['size' => $file->getSize()]);
                
                $result = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'coaches'
                ]);
                $validated['profile_picture'] = $result['secure_url'];
                \Log::info('Cloudinary upload successful', ['url' => $validated['profile_picture']]);
            } catch (\Exception $e) {
                \Log::error('Cloudinary upload failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['message' => 'Image upload failed: ' . $e->getMessage()], 500);
            }
        }

        $coach = Coach::create($validated);

        return new CoachResource($coach);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): CoachResource | JsonResponse
    {
        $coach = Coach::find($id);

        if (!$coach) {
            return response()->json(['message' => 'Coach not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'title' => 'nullable|string|max:255',
            'short_info' => 'sometimes|required|string',
            'fide_rating' => 'nullable|integer',
            'profile_picture' => 'sometimes|required',
            'is_academy_instructor' => 'boolean',
            'playing_experience' => 'sometimes|required|array',
            'teaching_experience' => 'sometimes|required|array',
            'bio' => 'sometimes|required|string',
            'location' => 'sometimes|required|string',
            'availability' => 'sometimes|required|string',
            'teaching_methods' => 'sometimes|required|array',
            'coaching_type' => 'sometimes|required|string',
            'social_media' => 'sometimes|required|array',
        ]);

        // Handle File Upload to Cloudinary
        if ($request->hasFile('profile_picture')) {
            try {
                $file = $request->file('profile_picture');
                \Log::info('Attempting Cloudinary upload for coach update', ['coach_id' => $id, 'size' => $file->getSize()]);

                // Delete old image if it's a Cloudinary asset
                if ($coach->profile_picture && str_contains($coach->profile_picture, 'cloudinary.com')) {
                    try {
                        $parts = explode('/', $coach->profile_picture);
                        $filename = end($parts);
                        $publicId = 'coaches/' . explode('.', $filename)[0];
                        \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->destroy($publicId);
                    } catch (\Exception $e) {
                        \Log::warning('Failed to delete old Cloudinary image', ['error' => $e->getMessage()]);
                    }
                }

                $result = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'coaches'
                ]);
                $validated['profile_picture'] = $result['secure_url'];
                \Log::info('Cloudinary update successful', ['url' => $validated['profile_picture']]);
            } catch (\Exception $e) {
                \Log::error('Cloudinary update failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['message' => 'Image upload failed: ' . $e->getMessage()], 500);
            }
        }

        $coach->update($validated);

        return new CoachResource($coach);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $coach = Coach::find($id);

        if (!$coach) {
            return response()->json(['message' => 'Coach not found'], 404);
        }

        // Cleanup image from Cloudinary if applicable
        if ($coach->profile_picture && str_contains($coach->profile_picture, 'cloudinary.com')) {
            $parts = explode('/', $coach->profile_picture);
            $filename = end($parts);
            $publicId = 'coaches/' . explode('.', $filename)[0];
            \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->destroy($publicId);
        }

        $coach->delete();

        return response()->json(['message' => 'Coach deleted successfully']);
    }
}
