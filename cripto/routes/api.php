<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\FeedPostController;
use App\Http\Controllers\API\GoalController;
use App\Http\Controllers\API\UserProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::fallback(function(){
    return response()->json([
        'status' => false,
        'message' => 'API route not found.',
    ], 404);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-reset-password', [AuthController::class, 'verifyAndReset']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/user/update', [AuthController::class, 'updateProfile']);
    Route::post('/reset-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/course-categories', [CourseController::class, 'activeCategories']);
    Route::get('/courses', [CourseController::class, 'activeCourses']);
    Route::get('/course/{id}', [CourseController::class, 'activeCourse']);
    Route::get('/featured-courses', [CourseController::class, 'featuredCourses']);

    Route::get('/config/stripe-key', [CourseController::class, 'getStripeKey']);
    Route::get('/user/course-counts', [CourseController::class, 'courseCounts']);

    Route::post('/course/{id}/rate', [CourseController::class, 'addCourseRate']);
    Route::get('/course/{id}/ratings', [CourseController::class, 'getRatings']);

    Route::post('/course/{id}/book', [CourseController::class, 'bookCourse']);
    Route::post('/booking/{id}/payment-status', [CourseController::class, 'updatePaymentStatus']);
    Route::get('/my-courses', [CourseController::class, 'myCourses']);
    Route::get('/my-course/{id}', [CourseController::class, 'myCourse']);

    Route::post('/courses/progress/{booking_id}/{session_id}', [CourseController::class, 'markSessionComplete']);
});
