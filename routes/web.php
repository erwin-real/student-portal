<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome');
// })->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('students', function () {
//     return Inertia::render('Students');
// })->middleware(['auth', 'verified'])->name('members');

Route::group(['prefix' => 'students', 'middleware' => 'auth'], function () {
    Route::get('/', [StudentController::class, 'index'])->name('students.index');
    Route::get('/{id}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/{id}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::match(['put', 'patch'], '/{student}', [StudentController::class, 'update'])->name('students.update');
    // Route::post('/{id}', [StudentController::class, 'update'])->name('students.update');

});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
