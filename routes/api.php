<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\QuizAttemptController;
use App\Http\Controllers\Api\AvatarController;
use App\Http\Controllers\Api\ChildController;


Route::fallback(function(){
    return response()->json([
        'status' => false,
        'message' => 'API route not found.',
    ], 404);
});

// ─── Public (Guest) Routes ──────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// OTP-based Forgot Password (no auth needed)
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-reset-password', [AuthController::class, 'verifyAndResetPassword']);

// ─── Authenticated Routes ───────────────────────────────────────
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/user/reset-password', [AuthController::class, 'resetPassword']);

    // Parent PIN
    Route::post('/user/set-pin', [AuthController::class, 'setPin']);
    Route::post('/user/verify-pin', [AuthController::class, 'verifyPin']);
    Route::post('/user/reset-pin', [AuthController::class, 'resetPin']);

    // OTP-based Forgot PIN (auth needed, user must be logged in)
    Route::post('/user/forgot-pin', [AuthController::class, 'forgotPin']);
    Route::post('/user/verify-reset-pin', [AuthController::class, 'verifyAndResetPin']);

    // ─── Quiz Engine (Read-Only) ────────────────────────────────────
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::get('/subjects/{id}', [SubjectController::class, 'show']);

    Route::get('/grades', [GradeController::class, 'index']);
    Route::get('/grades/{id}', [GradeController::class, 'show']);

    Route::get('/questions', [QuestionController::class, 'index']);
    Route::get('/questions/{id}', [QuestionController::class, 'show']);

    // ─── Quiz Attempts ──────────────────────────────────────────────
    Route::post('/quiz/submit', [QuizAttemptController::class, 'store']);

    // ─── Avatars (Read-only list for mobile) ───────────────────────
    Route::get('/avatars', [AvatarController::class, 'index']);

    // ─── Children (Parent manages their children) ──────────────────
    Route::get('/children', [ChildController::class, 'index']);
    Route::post('/children/add', [ChildController::class, 'store']);
    Route::get('/children/show/{id}', [ChildController::class, 'show']);
    Route::post('/children/update/{id}', [ChildController::class, 'update']);
    Route::post('/children/settings/{id}', [ChildController::class, 'updateSettings']);
    Route::post('/children/delete/{id}', [ChildController::class, 'destroy']);
    
    Route::get('/children/dashboard/{id?}', [ChildController::class, 'dashboard']);
    Route::get('/leaderboard', [ChildController::class, 'leaderboard']);
});