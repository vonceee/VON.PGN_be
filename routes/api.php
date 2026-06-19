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

use App\Http\Controllers\Api\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Api\Admin\ChapterController as AdminChapterController;
use App\Http\Controllers\Api\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Api\Admin\TournamentController as AdminTournamentController;
use App\Http\Controllers\Api\Admin\CoachController as AdminCoachController;


use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\MapsUrlResolverController;
use App\Http\Controllers\Api\UserTournamentController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TournamentBookmarkController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\LichessProxyController;
use App\Http\Controllers\Api\MediaProxyController;

use App\Http\Controllers\Api\StudyController;
use App\Http\Controllers\Api\AcademyEnrollmentController;
use App\Http\Controllers\Api\Admin\AcademyEnrollmentController as AdminAcademyEnrollmentController;
use App\Http\Controllers\Api\CoachController;
use App\Http\Controllers\Api\NotificationController;

use App\Http\Controllers\Api\WoodpeckerController;



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
Route::get('/tactics/themes', [TacticsController::class, 'themes']);



Route::get('/tournaments', [TournamentController::class, 'index']);
Route::get('/tournaments/bookmarks', [TournamentBookmarkController::class, 'index'])
    ->middleware('auth:sanctum');
Route::get('/tournaments/{slug}', [TournamentController::class, 'show']);
Route::get('/users/{id}/tournaments', [TournamentController::class, 'userTournaments']);



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



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/email/update', [AuthController::class, 'updateEmail']);
    Route::get('/profile', [UserProfileController::class, 'myProfile']);
    Route::put('/profile/bio', [UserProfileController::class, 'updateBio']);
    Route::put('/profile/preferences', [UserProfileController::class, 'updatePreferences']);

    Route::post('/progress/complete-lecture', [ProgressController::class, 'completeLecture']);

    Route::post('/tactics/solve', [TacticsController::class, 'solve']);
    Route::get('/tactics/history', [TacticsController::class, 'history']);

    // Woodpecker Method routes
    Route::get('/tactics/woodpecker', [WoodpeckerController::class, 'index']);
    Route::post('/tactics/woodpecker', [WoodpeckerController::class, 'store']);
    Route::get('/tactics/woodpecker/{id}', [WoodpeckerController::class, 'show']);
    Route::post('/tactics/woodpecker/{id}/solve', [WoodpeckerController::class, 'solve']);
    Route::post('/tactics/woodpecker/{id}/abandon', [WoodpeckerController::class, 'abandon']);
    Route::delete('/tactics/woodpecker/{id}', [WoodpeckerController::class, 'destroy']);

    // Follow routes
    Route::post('/users/{id}/follow', [FollowController::class, 'follow']);
    Route::delete('/users/{id}/follow', [FollowController::class, 'unfollow']);
    Route::get('/users/{id}/follow-status', [FollowController::class, 'status']);

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead']);



    Route::middleware('admin')->group(function () {
        // User tournament management
        Route::get('/my/tournaments', [UserTournamentController::class, 'index']);
        Route::post('/my/tournaments', [UserTournamentController::class, 'store']);
        Route::get('/my/tournaments/{id}', [UserTournamentController::class, 'show']);
        Route::put('/my/tournaments/{id}', [UserTournamentController::class, 'update']);
        Route::delete('/my/tournaments/{id}', [UserTournamentController::class, 'destroy']);
        Route::post('/my/tournaments/media', [UserTournamentController::class, 'uploadMedia']);


    });



    // Tournament bookmark routes
    Route::post('/tournaments/{slug}/bookmark', [TournamentBookmarkController::class, 'toggle']);

    // Tournament bookmark routes
    Route::post('/tournaments/{slug}/bookmark', [TournamentBookmarkController::class, 'toggle']);



});

// Public/Guest accessible Study routes
Route::get('/studies', [StudyController::class, 'index']);
Route::get('/studies/{study}', [StudyController::class, 'show']);
Route::get('/studies/{study}/export-pgn', [StudyController::class, 'exportPgn']);

Route::middleware('auth:sanctum')->group(function () {
    // Authenticated Study routes
    Route::post('/studies', [StudyController::class, 'store']);
    Route::post('/studies/{study}/import-pgn', [StudyController::class, 'importPgn']);
    Route::put('/studies/{study}', [StudyController::class, 'update']);
    Route::delete('/studies/{study}', [StudyController::class, 'destroy']);
    Route::post('/studies/{study}/chapters', [StudyController::class, 'addChapter']);
    Route::post('/studies/{study}/chapters/reorder', [StudyController::class, 'reorderChapters']);
    Route::put('/studies/{study}/chapters/{chapter}', [StudyController::class, 'updateChapter']);
    Route::delete('/studies/{study}/chapters/{chapter}', [StudyController::class, 'deleteChapter']);
    Route::post('/studies/{study}/collaborators', [StudyController::class, 'addCollaborator']);
    Route::delete('/studies/{study}/collaborators/{userId}', [StudyController::class, 'removeCollaborator']);
    Route::put('/studies/{study}/collaborators/{userId}', [StudyController::class, 'updateCollaborator']);
    Route::get('/studies/{study}/messages', [StudyController::class, 'messages']);
    Route::post('/studies/{study}/messages', [StudyController::class, 'sendMessage']);
    Route::delete('/studies/{study}/messages', [StudyController::class, 'clearMessages']);
});

use App\Http\Controllers\Api\Admin\UserController as AdminUserController;

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::apiResource('courses', AdminCourseController::class);
    Route::apiResource('chapters', AdminChapterController::class);
    Route::apiResource('lessons', AdminLessonController::class);
    Route::apiResource('tournaments', AdminTournamentController::class);
    Route::apiResource('coaches', AdminCoachController::class);

    Route::post('tournaments/media', [AdminTournamentController::class, 'uploadMedia']);
    Route::post('resolve-maps-url', [MapsUrlResolverController::class, 'resolve']);
    
    // User management
    Route::get('users', [AdminUserController::class, 'index']);
    Route::get('users/{id}', [AdminUserController::class, 'show']);
    Route::post('users/{id}/toggle-admin', [AdminUserController::class, 'toggleAdmin']);
    Route::post('users/{id}/toggle-organizer', [AdminUserController::class, 'toggleOrganizer']);
    Route::delete('users/{id}', [AdminUserController::class, 'destroy']);
    
    Route::post('users/{id}/toggle-verified-organizer', [UserProfileController::class, 'toggleVerifiedOrganizer']);



    // Academy Enrollments management
    Route::get('/academy/enrollments', [AdminAcademyEnrollmentController::class, 'index']);
    Route::get('/academy/enrollments/{id}', [AdminAcademyEnrollmentController::class, 'show']);
    Route::put('/academy/enrollments/{id}', [AdminAcademyEnrollmentController::class, 'update']);
    Route::delete('/academy/enrollments/{id}', [AdminAcademyEnrollmentController::class, 'destroy']);
});


