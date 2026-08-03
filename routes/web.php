<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Apps\UserController;

// ═══════════════════════════════════════════════════
// ADMIN ROUTES (Vuexy Design — Admin roles only)
// ═══════════════════════════════════════════════════

Route::get('/', function () {
    return redirect('/login');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('content.pages.pages-home');
    })->name('pages-home');

    // User Management
    Route::get('user/list', [UserController::class, 'index'])->name('app-user-list');
    Route::get('user/create', [UserController::class, 'create'])->name('app-user-create');
    Route::post('user/store', [UserController::class, 'store'])->name('app-user-store');
    Route::get('user/edit/{id}', [UserController::class, 'edit'])->name('app-user-edit');
    Route::put('user/update/{id}', [UserController::class, 'update'])->name('app-user-update');
    Route::delete('user/delete/{id}', [UserController::class, 'destroy'])->name('app-user-delete');

    // Subjects
    Route::get('subjects', [\App\Http\Controllers\Admin\SubjectController::class, 'index'])->name('admin.subjects.index');

    // Avatars (Gallery)
    Route::get('avatars', [\App\Http\Controllers\Admin\AvatarController::class, 'index'])->name('admin.avatars.index');
    Route::post('avatars', [\App\Http\Controllers\Admin\AvatarController::class, 'store'])->name('admin.avatars.store');
    Route::delete('avatars/{avatar}', [\App\Http\Controllers\Admin\AvatarController::class, 'destroy'])->name('admin.avatars.destroy');

    // Subjects
    Route::get('subjects/create', [\App\Http\Controllers\Admin\SubjectController::class, 'create'])->name('admin.subjects.create');
    Route::post('subjects', [\App\Http\Controllers\Admin\SubjectController::class, 'store'])->name('admin.subjects.store');
    Route::get('subjects/{subject}/edit', [\App\Http\Controllers\Admin\SubjectController::class, 'edit'])->name('admin.subjects.edit');
    Route::put('subjects/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'update'])->name('admin.subjects.update');
    Route::delete('subjects/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'destroy'])->name('admin.subjects.destroy');

    // Grades
    Route::get('grades', [\App\Http\Controllers\Admin\GradeController::class, 'index'])->name('admin.grades.index');
    Route::get('grades/create', [\App\Http\Controllers\Admin\GradeController::class, 'create'])->name('admin.grades.create');
    Route::post('grades', [\App\Http\Controllers\Admin\GradeController::class, 'store'])->name('admin.grades.store');
    Route::get('grades/{grade}/edit', [\App\Http\Controllers\Admin\GradeController::class, 'edit'])->name('admin.grades.edit');
    Route::put('grades/{grade}', [\App\Http\Controllers\Admin\GradeController::class, 'update'])->name('admin.grades.update');
    Route::delete('grades/{grade}', [\App\Http\Controllers\Admin\GradeController::class, 'destroy'])->name('admin.grades.destroy');

    // Questions
    Route::get('questions', [\App\Http\Controllers\Admin\QuestionController::class, 'index'])->name('admin.questions.index');
    Route::get('questions/create', [\App\Http\Controllers\Admin\QuestionController::class, 'create'])->name('admin.questions.create');
    Route::post('questions', [\App\Http\Controllers\Admin\QuestionController::class, 'store'])->name('admin.questions.store');
    Route::get('questions/{question}/edit', [\App\Http\Controllers\Admin\QuestionController::class, 'edit'])->name('admin.questions.edit');
    Route::put('questions/{question}', [\App\Http\Controllers\Admin\QuestionController::class, 'update'])->name('admin.questions.update');
    Route::delete('questions/{question}', [\App\Http\Controllers\Admin\QuestionController::class, 'destroy'])->name('admin.questions.destroy');

    // Milestones
    Route::get('milestones', [\App\Http\Controllers\Admin\MilestoneController::class, 'index'])->name('admin.milestones.index');
    Route::get('milestones/create', [\App\Http\Controllers\Admin\MilestoneController::class, 'create'])->name('admin.milestones.create');
    Route::post('milestones', [\App\Http\Controllers\Admin\MilestoneController::class, 'store'])->name('admin.milestones.store');
    Route::get('milestones/{milestone}/edit', [\App\Http\Controllers\Admin\MilestoneController::class, 'edit'])->name('admin.milestones.edit');
    Route::put('milestones/{milestone}', [\App\Http\Controllers\Admin\MilestoneController::class, 'update'])->name('admin.milestones.update');
    Route::delete('milestones/{milestone}', [\App\Http\Controllers\Admin\MilestoneController::class, 'destroy'])->name('admin.milestones.destroy');
});
