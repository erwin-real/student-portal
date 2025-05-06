<?php

use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome');
// })->name('home');

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('students', function () {
//     return Inertia::render('Students');
// })->middleware(['auth', 'verified'])->name('members');

Route::group(['prefix' => 'members', 'middleware' => 'auth'], function () {
    Route::get('/', [MemberController::class, 'index'])->name('members');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
