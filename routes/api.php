<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\TacticsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{slug}', [CourseController::class, 'show']);
Route::get('/lessons/{slug}', [CourseController::class, 'getLesson']);

Route::get('/tactics/next', [TacticsController::class, 'getDailyPuzzle']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [UserProfileController::class, 'myProfile']);

    Route::post('/progress/complete-lecture', [ProgressController::class, 'completeLecture']);

    Route::post('/tactics/solve', [TacticsController::class, 'solve']);
});
