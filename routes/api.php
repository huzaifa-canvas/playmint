<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;


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
    Route::get('/subjects', [\App\Http\Controllers\Api\SubjectController::class, 'index']);
    Route::get('/subjects/{id}', [\App\Http\Controllers\Api\SubjectController::class, 'show']);

    Route::get('/grades', [\App\Http\Controllers\Api\GradeController::class, 'index']);
    Route::get('/grades/{id}', [\App\Http\Controllers\Api\GradeController::class, 'show']);

    Route::get('/questions', [\App\Http\Controllers\Api\QuestionController::class, 'index']);
    Route::get('/questions/{id}', [\App\Http\Controllers\Api\QuestionController::class, 'show']);

    // ─── Quiz Attempts ──────────────────────────────────────────────
    Route::post('/quiz/submit', [\App\Http\Controllers\Api\QuizAttemptController::class, 'store']);

    // ─── Avatars (Read-only list for mobile) ───────────────────────
    Route::get('/avatars', [\App\Http\Controllers\Api\AvatarController::class, 'index']);

    // ─── Children (Parent manages their children) ──────────────────
    Route::get('/children', [\App\Http\Controllers\Api\ChildController::class, 'index']);
    Route::post('/children/add', [\App\Http\Controllers\Api\ChildController::class, 'store']);
    Route::get('/children/show/{id}', [\App\Http\Controllers\Api\ChildController::class, 'show']);
    Route::post('/children/update/{id}', [\App\Http\Controllers\Api\ChildController::class, 'update']);
    Route::post('/children/settings/{id}', [\App\Http\Controllers\Api\ChildController::class, 'updateSettings']);
    Route::post('/children/delete/{id}', [\App\Http\Controllers\Api\ChildController::class, 'destroy']);
});