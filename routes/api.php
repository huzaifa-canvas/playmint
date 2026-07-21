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
});

    