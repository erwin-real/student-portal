<?php

use App\Http\Controllers\FacultyController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('students', function () {
//     return Inertia::render('Students');
// })->middleware(['auth', 'verified'])->name('members');

Route::group(['middleware' => 'auth'], function () {

    Route::group(['prefix' => 'students'], function () {
        Route::get('/', [StudentController::class, 'index'])->name('students.index');
        Route::get('/{id}', [StudentController::class, 'show'])->name('students.show');
        Route::get('/{id}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::match(['put', 'patch'], '/{student}', [StudentController::class, 'update'])->name('students.update');
    });

    Route::group(['prefix' => 'faculties'], function () {
        Route::get('/', [FacultyController::class, 'index'])->name('faculties.index');
        Route::get('/{id}', [FacultyController::class, 'show'])->name('faculties.show');
        Route::get('/{id}/edit', [FacultyController::class, 'edit'])->name('faculties.edit');
        Route::match(['put', 'patch'], '/{student}', [FacultyController::class, 'update'])->name('faculties.update');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::match(['put', 'patch'], '/{student}', [UserController::class, 'update'])->name('users.update');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
    });

    Route::group(['prefix' => 'levels'], function () {
        Route::get('/', [LevelController::class, 'index'])->name('levels.index');
        Route::get('/create', [LevelController::class, 'create'])->name(name: 'levels.create');
        Route::post('/', [LevelController::class, 'store'])->name('levels.store');
        // Route::get('/{id}', [LevelController::class, 'show'])->name('levels.show');
        // Route::get('/{id}/edit', [LevelController::class, 'edit'])->name('levels.edit');
        // Route::match(['put', 'patch'], '/{student}', [LevelController::class, 'update'])->name('levels.update');
    });

    Route::group(['prefix' => 'reports'], function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        // Route::get('/create', [ReportController::class, 'create'])->name(name: 'reports.create');
        // Route::post('/', [ReportController::class, 'store'])->name('reports.store');
        // Route::get('/{id}', [ReportController::class, 'show'])->name('reports.show');
        // Route::get('/{id}/edit', [ReportController::class, 'edit'])->name('levels.edit');
        // Route::match(['put', 'patch'], '/{student}', [ReportController::class, 'update'])->name('reports.update');
    });

});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
