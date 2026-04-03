<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\TacticsController;
use App\Http\Controllers\Api\TournamentController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Api\Admin\ChapterController as AdminChapterController;
use App\Http\Controllers\Api\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Api\Admin\TournamentController as AdminTournamentController;

use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\MapsUrlResolverController;
use App\Http\Controllers\Api\UserTournamentController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TournamentBookmarkController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\LichessProxyController;

Route::get('/ping', function () {
    return response()->json(['pong' => true, 'timestamp' => now()->toIso8601String()]);
});

// Google auth - use stateless (no session needed)
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:10,60');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->middleware('throttle:6,1')
    ->name('password.email');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->name('password.store');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    if (! $request->hasValidSignature()) {
        return response()->json(['message' => 'Invalid or expired verification link.'], 403);
    }
    
    $user = \App\Models\User::findOrFail($id);
    
    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Invalid verification link.'], 403);
    }
    
    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new \Illuminate\Auth\Events\Verified($user));
    }
    
    return redirect(env('FRONTEND_URL', 'http://localhost:4200') . '/login?verified=1');
})->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    try {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent!']);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Failed to resend verification email: ' . $e->getMessage());
        return response()->json(['message' => 'Failed to send email, please try again later.'], 500);
    }
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

Route::get('/users/search', [UserProfileController::class, 'search']);
Route::get('/users/{id}', [UserProfileController::class, 'showProfile']);

Route::get('/users/{id}/followers', [FollowController::class, 'followers']);
Route::get('/users/{id}/following', [FollowController::class, 'following']);

Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{slug}', [CourseController::class, 'show']);
Route::get('/lessons/{slug}', [CourseController::class, 'getLesson']);

Route::get('/lichess/pgn', [LichessProxyController::class, 'pgn']);

Route::get('/tactics/next', [TacticsController::class, 'getDailyPuzzle']);

Route::get('/tournaments', [TournamentController::class, 'index']);
Route::get('/tournaments/bookmarks', [TournamentBookmarkController::class, 'index'])
    ->middleware('auth:sanctum');
Route::get('/tournaments/{slug}', [TournamentController::class, 'show']);
Route::get('/users/{id}/tournaments', [TournamentController::class, 'userTournaments']);

// PayMongo webhook (no auth - verified by PayMongo signature)
Route::post('/webhooks/paymongo', [PaymentController::class, 'webhook']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/email/update', [AuthController::class, 'updateEmail']);
    Route::get('/profile', [UserProfileController::class, 'myProfile']);
    Route::put('/profile/bio', [UserProfileController::class, 'updateBio']);
    Route::put('/profile/rating', [UserProfileController::class, 'updateRating']);

    Route::post('/progress/complete-lecture', [ProgressController::class, 'completeLecture']);

    Route::post('/tactics/solve', [TacticsController::class, 'solve']);

    // Follow routes
    Route::post('/users/{id}/follow', [FollowController::class, 'follow']);
    Route::delete('/users/{id}/follow', [FollowController::class, 'unfollow']);
    Route::get('/users/{id}/follow-status', [FollowController::class, 'status']);

    // Chat routes
    Route::get('/chat/conversations', [ChatController::class, 'conversations']);
    Route::post('/chat/conversations', [ChatController::class, 'startConversation']);
    Route::get('/chat/conversations/{conversationId}/messages', [ChatController::class, 'messages']);
    Route::post('/chat/conversations/{conversationId}/messages', [ChatController::class, 'sendMessage']);
    Route::post('/chat/conversations/{conversationId}/read', [ChatController::class, 'markAsRead']);
    Route::post('/chat/conversations/{conversationId}/typing', [ChatController::class, 'typing']);
    Route::post('/chat/status', [ChatController::class, 'updateStatus']);
    Route::get('/chat/unread', [ChatController::class, 'unreadCount']);

    // Game routes
    Route::get('/seeks', [GameController::class, 'seeks']);
    Route::post('/game/seek', [GameController::class, 'seek']);
    Route::post('/game/seek/cancel', [GameController::class, 'cancelSeek']);
    Route::get('/game/active', [GameController::class, 'activeGame']);
    Route::get('/game/{gameId}', [GameController::class, 'show']);
    Route::post('/game/{gameId}/move', [GameController::class, 'move']);
    Route::post('/game/{gameId}/resign', [GameController::class, 'resign']);
    Route::post('/game/{gameId}/abort', [GameController::class, 'abort']);
    Route::post('/game/{gameId}/draw', [GameController::class, 'draw']);
    Route::post('/game/{gameId}/sync-clock', [GameController::class, 'syncClock']);
    Route::post('/game/{gameId}/heartbeat', [GameController::class, 'heartbeat']);

    // User tournament management
    Route::get('/my/tournaments', [UserTournamentController::class, 'index']);
    Route::post('/my/tournaments', [UserTournamentController::class, 'store']);
    Route::get('/my/tournaments/{id}', [UserTournamentController::class, 'show']);
    Route::put('/my/tournaments/{id}', [UserTournamentController::class, 'update']);
    Route::delete('/my/tournaments/{id}', [UserTournamentController::class, 'destroy']);

    // Payment routes
    Route::post('/payments/checkout', [PaymentController::class, 'createCheckout']);
    Route::get('/payments/history', [PaymentController::class, 'history']);

    // Tournament bookmark routes
    Route::post('/tournaments/{slug}/bookmark', [TournamentBookmarkController::class, 'toggle']);
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::apiResource('courses', AdminCourseController::class);
    Route::apiResource('chapters', AdminChapterController::class);
    Route::apiResource('lessons', AdminLessonController::class);
    Route::apiResource('tournaments', AdminTournamentController::class);
    Route::post('resolve-maps-url', [MapsUrlResolverController::class, 'resolve']);
    Route::post('users/{id}/toggle-verified-organizer', [UserProfileController::class, 'toggleVerifiedOrganizer']);
});

