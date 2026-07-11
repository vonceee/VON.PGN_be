<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of published blog posts.
     */
    public function index(Request $request)
    {
        $blogs = Blog::with(['author:id,name'])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return response()->json($blogs);
    }

    /**
     * Display a listing of the authenticated user's blog posts (both draft and published).
     */
    public function myBlogs(Request $request)
    {
        $user = $request->user();
        
        $blogs = Blog::with(['author:id,name'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json($blogs);
    }

    /**
     * Display the specified blog post by slug.
     */
    public function show(Request $request, string $slug)
    {
        $blog = Blog::with(['author:id,name,bio', 'games'])
            ->where('slug', $slug)
            ->firstOrFail();

        // If the blog is a draft, only the author or another admin can view it
        if ($blog->status === 'draft') {
            $user = $request->user('sanctum');
            if (!$user || ($user->id !== $blog->user_id && !$user->is_admin)) {
                abort(403, 'Unauthorized to view this draft.');
            }
        }

        return response()->json(['data' => $blog]);
    }

    /**
     * Store a newly created blog post.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'games' => 'nullable|array',
            'games.*.title' => 'nullable|string|max:255',
            'games.*.pgn' => 'required|string',
            'games.*.order' => 'integer',
        ]);

        $user = $request->user();
        $slug = Str::slug($request->title);
        
        // Ensure slug uniqueness
        $originalSlug = $slug;
        $count = 1;
        while (Blog::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $blog = DB::transaction(function () use ($request, $user, $slug) {
            $publishedAt = $request->status === 'published' ? now() : null;
            
            $blog = Blog::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'slug' => $slug,
                'summary' => $request->summary,
                'content' => $request->content,
                'status' => $request->status,
                'published_at' => $publishedAt,
            ]);

            if ($request->has('games')) {
                foreach ($request->games as $gameData) {
                    BlogGame::create([
                        'blog_id' => $blog->id,
                        'title' => $gameData['title'] ?? null,
                        'pgn' => $gameData['pgn'],
                        'order' => $gameData['order'] ?? 0,
                    ]);
                }
            }

            return $blog;
        });

        return response()->json(['data' => $blog->load('games')], 201);
    }

    /**
     * Update the specified blog post.
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'games' => 'nullable|array',
            'games.*.title' => 'nullable|string|max:255',
            'games.*.pgn' => 'required|string',
            'games.*.order' => 'integer',
        ]);

        $slug = $blog->slug;
        if ($blog->title !== $request->title) {
            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $count = 1;
            while (Blog::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
        }

        DB::transaction(function () use ($request, $blog, $slug) {
            $publishedAt = $blog->published_at;
            if ($request->status === 'published' && !$publishedAt) {
                $publishedAt = now();
            } elseif ($request->status === 'draft') {
                $publishedAt = null;
            }

            $blog->update([
                'title' => $request->title,
                'slug' => $slug,
                'summary' => $request->summary,
                'content' => $request->content,
                'status' => $request->status,
                'published_at' => $publishedAt,
            ]);

            // Sync games by deleting old ones and re-inserting new ones
            $blog->games()->delete();

            if ($request->has('games')) {
                foreach ($request->games as $gameData) {
                    BlogGame::create([
                        'blog_id' => $blog->id,
                        'title' => $gameData['title'] ?? null,
                        'pgn' => $gameData['pgn'],
                        'order' => $gameData['order'] ?? 0,
                    ]);
                }
            }
        });

        return response()->json(['data' => $blog->load('games')]);
    }

    /**
     * Remove the specified blog post.
     */
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();

        return response()->json(['message' => 'Blog post deleted successfully.']);
    }
}
