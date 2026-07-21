<?php

use App\Http\Controllers\CourseBookingController;
use App\Http\Controllers\CourseCategorieController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseSessionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
// Route::get('/categories', [CourseCategorieController::class, 'index'])->name('categories');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth'])->group(function(){
    Route::resource('/categories', CourseCategorieController ::class);
    Route::resource('/courses', CourseController::class);
    Route::post('/courses/{course}/sessions', [CourseSessionController::class,'store'])->name('courses.sessions.store');
    Route::put('/sessions/{session}', [CourseSessionController::class,'update'])->name('sessions.update');
    Route::delete('/sessions/{session}', [CourseSessionController::class,'destroy'])->name('sessions.destroy');

    Route::post('/courses/{course}/book', [CourseBookingController::class,'store'])->name('courses.book');
    Route::get('/bookings', [CourseBookingController::class,'index'])->name('bookings.index');
});

require __DIR__.'/auth.php';
