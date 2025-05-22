<?php

use App\Http\Controllers\FacultyController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\IsAdmin;
use App\Models\Member;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

Route::get('/test', function () {
    $records = DB::select("SELECT linked_member_id as Id, rfid, last_name as Lastname, first_name as Firstname,
        middle_name as Middlename,
        (SELECT name FROM levels WHERE id=s.level_id) as GradeLevel,
        photo as PhotoFileName, mobile_no as Mobile,
        s.can_notify = 1 as IsNotify
        FROM members m LEFT JOIN students s ON m.id=s.member_id
    ");

    return $records;
})->name('test');

// Route::get('/preview-pdf', [ReportController::class, 'preview']);

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/get-current-user', [UserController::class, 'getCurrentUser'])->name('getCurrentUser');

    Route::group(['prefix' => 'students'], function () {
        Route::get('/', [StudentController::class, 'index'])->name('students.index');
        Route::get('/{id}', [StudentController::class, 'show'])->name('students.show');
        Route::get('/{id}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::match(['put', 'patch'], '/{student}', [StudentController::class, 'update'])->name('students.update');
    });

    Route::prefix('faculties')
        ->middleware(IsAdmin::class)
        ->controller(FacultyController::class)
        ->group(function () {
            Route::get('/', 'index')->name('faculties.index');
            Route::get('/{id}', 'show')->name('faculties.show');
            Route::get('/{id}/edit', 'edit')->name('faculties.edit');
            Route::match(['put', 'patch'], '/{student}', 'update')->name('faculties.update');
        });

    Route::prefix('users')
        ->middleware(IsAdmin::class)
        ->controller(UserController::class)
        ->group(function () {
            Route::get('/', 'index')->name('users.index');
            Route::post('/', 'store')->name('users.store');
            Route::get('/create', 'create')->name('users.create');
            Route::get('/{id}', 'show')->name('users.show');
            Route::get('/{id}/edit', 'edit')->name('users.edit');
            Route::match(['put', 'patch'], '/{student}', 'update')->name('users.update');
        });

    Route::prefix('levels')
        ->middleware(IsAdmin::class)
        ->controller(LevelController::class)
        ->group(function () {
            Route::get('/', 'index')->name('levels.index');
            Route::get('/create', 'create')->name(name: 'levels.create');
            Route::post('/', 'store')->name('levels.store');
            Route::get('/{id}', 'show')->name('levels.show');
            Route::get('/{id}/edit', 'edit')->name('levels.edit');
        });

    Route::group(['prefix' => 'reports'], function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/preview-pdf-daily', [ReportController::class, 'dailyReport'])->name('reports.daily');
        Route::post('/preview-pdf-detailed', [ReportController::class, 'detailedReport'])->name('reports.detailed');
        // Route::get('/create', [ReportController::class, 'create'])->name(name: 'reports.create');
        // Route::post('/', [ReportController::class, 'store'])->name('reports.store');
        // Route::get('/{id}', [ReportController::class, 'show'])->name('reports.show');
        // Route::get('/{id}/edit', [ReportController::class, 'edit'])->name('levels.edit');
        // Route::match(['put', 'patch'], '/{student}', [ReportController::class, 'update'])->name('reports.update');
    });

});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
