<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TournamentResource;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
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
            'poster_settings' => 'nullable|array',
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
            'poster_settings' => 'nullable|array',
        ]);

        if (empty($validated['slug']) && isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if (isset($validated['slug'])) {
            $validated['slug'] = $this->generateUniqueSlug($validated['slug'], $tournament->id);
        }

        // Detect and cleanup replaced media
        if (isset($validated['poster_settings'])) {
            $this->cleanupReplacedMedia($tournament, $validated['poster_settings']);
        }

        $tournament->update($validated);

        return new TournamentResource($tournament);
    }

    private function cleanupReplacedMedia(Tournament $tournament, array $newPs)
    {
        $oldPs = $tournament->poster_settings;
        if (!$oldPs) return;
        if (is_string($oldPs)) {
            $oldPs = json_decode($oldPs, true);
        }
        if (!$oldPs) return;

        // Cleanup custom poster if replaced
        if (!empty($oldPs['customPosterUrl']) && 
            (!isset($newPs['customPosterUrl']) || $newPs['customPosterUrl'] !== $oldPs['customPosterUrl'])) {
            $this->deleteMediaByUrl($oldPs['customPosterUrl']);
        }

        // Cleanup background if replaced
        if (!empty($oldPs['backgroundImage']) && 
            (!isset($newPs['backgroundImage']) || $newPs['backgroundImage'] !== $oldPs['backgroundImage'])) {
            $this->deleteMediaByUrl($oldPs['backgroundImage']);
        }

        // Cleanup logos if removed
        if (!empty($oldPs['logos']) && is_array($oldPs['logos'])) {
            $newLogos = $newPs['logos'] ?? [];
            foreach ($oldPs['logos'] as $oldLogo) {
                if (!in_array($oldLogo, $newLogos)) {
                    $this->deleteMediaByUrl($oldLogo);
                }
            }
        }
    }

    public function destroy(Request $request, $id)
    {
        $tournament = Tournament::where('slug', $id)->firstOrFail();

        if ($tournament->created_by !== $request->user()->id) {
            throw new AccessDeniedHttpException('You do not have permission to delete this tournament.');
        }

        // Cleanup media before deleting
        $this->cleanupTournamentMedia($tournament);

        $tournament->delete();

        return response()->json(['message' => 'Tournament deleted']);
    }

    private function cleanupTournamentMedia(Tournament $tournament)
    {
        $ps = $tournament->poster_settings;
        if (!$ps) return;

        if (is_string($ps)) {
            $ps = json_decode($ps, true);
        }

        if (!$ps) return;

        // Delete background
        if (!empty($ps['backgroundImage'])) {
            $this->deleteMediaByUrl($ps['backgroundImage']);
        }
        if (!empty($ps['background_image'])) { // snake_case check
            $this->deleteMediaByUrl($ps['background_image']);
        }

        // Delete custom poster
        if (!empty($ps['customPosterUrl'])) {
            $this->deleteMediaByUrl($ps['customPosterUrl']);
        }

        // Delete logos
        if (!empty($ps['logos']) && is_array($ps['logos'])) {
            foreach ($ps['logos'] as $logoUrl) {
                $this->deleteMediaByUrl($logoUrl);
            }
        }
    }

    private function deleteMediaByUrl(string $url)
    {
        // Handle Cloudinary deletion
        if (str_contains($url, 'cloudinary.com')) {
            $parts = explode('/', $url);
            $filename = end($parts);
            $folder = 'tournaments/' . $parts[count($parts) - 2];
            $publicId = $folder . '/' . explode('.', $filename)[0];
            \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->destroy($publicId);
            return;
        }

        // Fallback for internal URLs pointing to our media API
        if (str_contains($url, '/api/media/')) {
            $parts = explode('/api/media/', $url);
            if (count($parts) > 1) {
                $path = $parts[1]; // e.g. "posters/filename.png"
                $fullPath = "tournaments/{$path}";
                if (Storage::disk('public')->exists($fullPath)) {
                    Storage::disk('public')->delete($fullPath);
                }
            }
        }
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:10240', // 10MB max
            'type' => 'required|string|in:background,logo,poster',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $type = $request->input('type');
            
            $folderMap = [
                'background' => 'backgrounds',
                'logo' => 'logos',
                'poster' => 'posters'
            ];

            $subFolder = $folderMap[$type] ?? 'misc';
            $folder = 'tournaments/' . $subFolder;
            
            $result = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);
            $url = $result['secure_url'];
            
            return response()->json(['url' => $url]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }
}
