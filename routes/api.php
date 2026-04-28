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
use App\Http\Controllers\Api\MediaProxyController;
use App\Http\Controllers\Api\CoachApplicationController;
use App\Http\Controllers\Api\Admin\CoachApplicationController as AdminCoachApplicationController;
use App\Http\Controllers\Api\MatchmakingController;
use App\Http\Controllers\Api\StudyController;
use App\Http\Controllers\Api\ArenaController;
use App\Http\Controllers\Api\UserArenaController;
use App\Http\Controllers\Api\AcademyEnrollmentController;
use App\Http\Controllers\Api\Admin\AcademyEnrollmentController as AdminAcademyEnrollmentController;
use App\Http\Controllers\Api\CoachController;

Route::get('/ping', function () {
    return response()->json(['pong' => true, 'timestamp' => now()->toIso8601String()]);
});

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
Route::get('/tactics/leaderboard', [TacticsController::class, 'leaderboard']);

// Public route for submitting coach applications
Route::post('/coach-applications', [CoachApplicationController::class, 'store'])
    ->middleware('throttle:5,60'); // Rate limiting

// Check if current user has submitted an application
Route::middleware('auth:sanctum')->get('/coach-applications/my-status', [CoachApplicationController::class, 'myStatus']);

Route::get('/tournaments', [TournamentController::class, 'index']);
Route::get('/tournaments/bookmarks', [TournamentBookmarkController::class, 'index'])
    ->middleware('auth:sanctum');
Route::get('/tournaments/{slug}', [TournamentController::class, 'show']);
Route::get('/users/{id}/tournaments', [TournamentController::class, 'userTournaments']);

// Arena routes
Route::get('/arenas', [ArenaController::class, 'index']);
Route::get('/arenas/{slug}', [ArenaController::class, 'show']);

// Media Proxy (Public with CORS)
Route::get('/media/{type}/{filename}', [MediaProxyController::class, 'serve'])
    ->where('filename', '.*');

Route::get('/coaches', [CoachController::class, 'index']);
Route::get('/coaches/{id}', [CoachController::class, 'show']);

// Academy Enrollment
Route::post('/academy/enroll', [AcademyEnrollmentController::class, 'store'])
    ->middleware('throttle:5,60');



// Academy Enrollment
Route::post('/academy/enroll', [AcademyEnrollmentController::class, 'store'])
    ->middleware('throttle:5,60');

// PayMongo webhook (no auth - verified by PayMongo signature)
Route::post('/webhooks/paymongo', [PaymentController::class, 'webhook']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/email/update', [AuthController::class, 'updateEmail']);
    Route::get('/profile', [UserProfileController::class, 'myProfile']);
    Route::put('/profile/bio', [UserProfileController::class, 'updateBio']);

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

    // Matchmaking routes
    Route::get('/seeks', [MatchmakingController::class, 'index']);
    Route::post('/seeks/{seekId}/join', [MatchmakingController::class, 'joinSeek']);
    Route::post('/game/seek', [MatchmakingController::class, 'seek']);
    Route::post('/game/seek/cancel', [MatchmakingController::class, 'cancelSeek']);
    Route::get('/game/active', [GameController::class, 'activeGame']);
    Route::get('/game/{gameId}', [GameController::class, 'show']);
    Route::post('/game/{gameId}/resign', [GameController::class, 'resign']);
    Route::post('/game/{gameId}/draw', [GameController::class, 'draw']);
    Route::post('/game/{gameId}/abort', [GameController::class, 'abort']);
    Route::get('/games/archived/{gameId}', [GameController::class, 'showArchived']);
    Route::get('/games/history', [GameController::class, 'history']);
    Route::post('/game/{gameId}/sync-clock', [GameController::class, 'syncClock']);

    // User tournament management
    Route::get('/my/tournaments', [UserTournamentController::class, 'index']);
    Route::post('/my/tournaments', [UserTournamentController::class, 'store']);
    Route::get('/my/tournaments/{id}', [UserTournamentController::class, 'show']);
    Route::put('/my/tournaments/{id}', [UserTournamentController::class, 'update']);
    Route::delete('/my/tournaments/{id}', [UserTournamentController::class, 'destroy']);
    Route::post('/my/tournaments/media', [UserTournamentController::class, 'uploadMedia']);

    // User arena management
    Route::get('/my/arenas', [UserArenaController::class, 'index']);
    Route::post('/my/arenas', [UserArenaController::class, 'store']);
    Route::get('/my/arenas/{id}', [UserArenaController::class, 'show']);
    Route::put('/my/arenas/{id}', [UserArenaController::class, 'update']);
    Route::delete('/my/arenas/{id}', [UserArenaController::class, 'destroy']);

    // Payment routes
    Route::post('/payments/checkout', [PaymentController::class, 'createCheckout']);
    Route::get('/payments/history', [PaymentController::class, 'history']);

    // Tournament bookmark routes
    Route::post('/tournaments/{slug}/bookmark', [TournamentBookmarkController::class, 'toggle']);

    // Tournament bookmark routes
    Route::post('/tournaments/{slug}/bookmark', [TournamentBookmarkController::class, 'toggle']);
});

// Public/Guest accessible Study routes
Route::get('/studies', [StudyController::class, 'index']);
Route::get('/studies/{study}', [StudyController::class, 'show']);
Route::get('/studies/{study}/export-pgn', [StudyController::class, 'exportPgn']);
Route::get('/studies/{study}/messages', [StudyController::class, 'messages']);

Route::middleware('auth:sanctum')->group(function () {
    // Authenticated Study routes
    Route::post('/studies', [StudyController::class, 'store']);
    Route::post('/studies/{study}/import-pgn', [StudyController::class, 'importPgn']);
    Route::put('/studies/{study}', [StudyController::class, 'update']);
    Route::delete('/studies/{study}', [StudyController::class, 'destroy']);
    Route::post('/studies/{study}/chapters', [StudyController::class, 'addChapter']);
    Route::put('/studies/{study}/chapters/{chapter}', [StudyController::class, 'updateChapter']);
    Route::delete('/studies/{study}/chapters/{chapter}', [StudyController::class, 'deleteChapter']);
    Route::post('/studies/{study}/collaborators', [StudyController::class, 'addCollaborator']);
    Route::delete('/studies/{study}/collaborators/{userId}', [StudyController::class, 'removeCollaborator']);
    Route::put('/studies/{study}/collaborators/{userId}', [StudyController::class, 'updateCollaborator']);
    Route::post('/studies/{study}/messages', [StudyController::class, 'sendMessage']);
    Route::delete('/studies/{study}/messages', [StudyController::class, 'clearMessages']);
});

use App\Http\Controllers\Api\Admin\UserController as AdminUserController;

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::apiResource('courses', AdminCourseController::class);
    Route::apiResource('chapters', AdminChapterController::class);
    Route::apiResource('lessons', AdminLessonController::class);
    Route::apiResource('tournaments', AdminTournamentController::class);
    Route::post('tournaments/media', [AdminTournamentController::class, 'uploadMedia']);
    Route::post('resolve-maps-url', [MapsUrlResolverController::class, 'resolve']);
    
    // User management
    Route::get('users', [AdminUserController::class, 'index']);
    Route::get('users/{id}', [AdminUserController::class, 'show']);
    Route::post('users/{id}/toggle-admin', [AdminUserController::class, 'toggleAdmin']);
    Route::post('users/{id}/toggle-organizer', [AdminUserController::class, 'toggleOrganizer']);
    Route::delete('users/{id}', [AdminUserController::class, 'destroy']);
    
    Route::post('users/{id}/toggle-verified-organizer', [UserProfileController::class, 'toggleVerifiedOrganizer']);

    // Coach applications management
    Route::get('/coach-applications', [AdminCoachApplicationController::class, 'index']);
    Route::get('/coach-applications/{id}', [AdminCoachApplicationController::class, 'show']);
    Route::post('/coach-applications/{id}/approve', [AdminCoachApplicationController::class, 'approve']);
    Route::post('/coach-applications/{id}/reject', [AdminCoachApplicationController::class, 'reject']);
    Route::delete('/coach-applications/{id}', [AdminCoachApplicationController::class, 'destroy']);

    // Academy Enrollments management
    Route::get('/academy/enrollments', [AdminAcademyEnrollmentController::class, 'index']);
    Route::get('/academy/enrollments/{id}', [AdminAcademyEnrollmentController::class, 'show']);
    Route::put('/academy/enrollments/{id}', [AdminAcademyEnrollmentController::class, 'update']);
    Route::delete('/academy/enrollments/{id}', [AdminAcademyEnrollmentController::class, 'destroy']);
});

// Internal microservice routes
    Route::post('/internal/game/{gameId}/complete', [GameController::class, 'completeGameInternal']);
    Route::post('/internal/game/create', [GameController::class, 'createGameInternal']);
    Route::post('/internal/arena/match', [GameController::class, 'createArenaMatchInternal']);
    Route::post('/internal/arena/{id}/finalize', [ArenaController::class, 'finalizeArenaInternal']);
