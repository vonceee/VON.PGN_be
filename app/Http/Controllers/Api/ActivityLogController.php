<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Study;
use App\Models\Blog;
use App\Models\Tournament;
use App\Utils\StudyObfuscator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ActivityLogController extends Controller
{
    /**
     * Get recent unified activities from the creator.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $creatorName = config('services.chess.creator_username', 'vonchess');
        
        $user = User::where('name', $creatorName)->first();
        if (!$user) {
            return response()->json(['data' => []]);
        }

        $userId = $user->id;

        // Cache the activity feed for 10 minutes (cleared on study/blog/tournament updates normally,
        // or just naturally expires)
        $feed = Cache::remember("creator_activity_feed_{$userId}", now()->addMinutes(10), function () use ($userId) {
            $activities = collect();

            // 1. Fetch recent studies (limit 6)
            try {
                $studies = Study::where('user_id', $userId)
                    ->where('visibility', 'public')
                    ->latest()
                    ->limit(6)
                    ->get();

                foreach ($studies as $study) {
                    $activities->push([
                        'id' => 'study_' . $study->id,
                        'type' => 'study',
                        'title' => 'Created study: ' . $study->name,
                        'description' => 'Category: ' . ucfirst(str_replace('_', ' ', $study->category ?? 'general')),
                        'route' => ['/study', StudyObfuscator::encode($study->id)],
                        'created_at' => $study->created_at->toIso8601String(),
                        'timestamp' => $study->created_at->timestamp,
                    ]);
                }
            } catch (\Exception $e) {
                // Safely catch if DB tables aren't set up yet
            }

            // 2. Fetch recent published blog posts (limit 6)
            try {
                $blogs = Blog::where('user_id', $userId)
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->limit(6)
                    ->get();

                foreach ($blogs as $blog) {
                    $activities->push([
                        'id' => 'blog_' . $blog->id,
                        'type' => 'blog',
                        'title' => 'Published blog: ' . $blog->title,
                        'description' => $blog->summary ?? 'Read the latest chess article.',
                        'route' => ['/blog', $blog->slug],
                        'created_at' => ($blog->published_at ?? $blog->created_at)->toIso8601String(),
                        'timestamp' => ($blog->published_at ?? $blog->created_at)->timestamp,
                    ]);
                }
            } catch (\Exception $e) {
            }

            // 3. Fetch recent scheduled tournaments created by user (limit 6)
            try {
                $tournaments = Tournament::where('created_by', $userId)
                    ->latest()
                    ->limit(6)
                    ->get();

                foreach ($tournaments as $tournament) {
                    $activities->push([
                        'id' => 'tournament_' . $tournament->id,
                        'type' => 'tournament',
                        'title' => 'Scheduled tournament: ' . $tournament->name,
                        'description' => 'Format: ' . ucfirst($tournament->format ?? 'arena') . ' | ' . ($tournament->time_control ?? 'blitz'),
                        'route' => ['/events', $tournament->slug],
                        'created_at' => $tournament->created_at->toIso8601String(),
                        'timestamp' => $tournament->created_at->timestamp,
                    ]);
                }
            } catch (\Exception $e) {
            }

            // Sort all activities by timestamp descending and take the top 6
            return $activities->sortByDesc('timestamp')->values()->take(6)->map(function ($item) {
                // Remove timestamp helper field from response
                unset($item['timestamp']);
                return $item;
            })->toArray();
        });

        return response()->json([
            'data' => $feed
        ]);
    }
}
