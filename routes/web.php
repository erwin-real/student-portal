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
    DB::unprepared("
            DROP TABLE IF EXISTS grpAttLogsResults;

            CREATE TEMPORARY TABLE IF NOT EXISTS grpAttLogsResults AS (
                SELECT
                    UNIX_TIMESTAMP(CONCAT(DATERECORD, ' ', TIMERECORD)) AS TIMESTAMPVAL,
                    MEMBERID,
                    TIMERECORD,
                    DATERECORD,
                    TYPE AS STATUS
                FROM tblotherstationattnlogs
                WHERE memberid = 42
            );

            INSERT INTO grpAttLogsResults (TIMESTAMPVAL, MEMBERID, TIMERECORD, DATERECORD, STATUS)
            SELECT DISTINCT
                UNIX_TIMESTAMP(CONCAT(DATERECORD, ' ', 0)),
                42,
                0,
                DATERECORD,
                'ABSENT'
            FROM tblotherstationattnlogs
            WHERE daterecord NOT IN (
                SELECT DISTINCT DATERECORD
                FROM tblotherstationattnlogs
            );
            ");

    // Step 4: Final SELECT query
    $query = "
                SELECT
                    TIMESTAMPVAL,
                    MEMBERID,
                    CASE
                        WHEN STATUS = 'ABSENT' THEN '------------'
                        ELSE TIME_FORMAT(TIMERECORD, '%h:%i %p')
                    END AS TIMERECORD,
                    DATE_FORMAT(DATERECORD, '%m-%d-%Y') AS DATERECORD,
                    STATUS,
                    CONCAT(m.last_name, ' , ', m.first_name) AS FULLNAME,
                    (
                        SELECT l.name
                        FROM levels l
                        LEFT JOIN students s ON l.id = s.level_id
                        WHERE s.member_id = m.id
                    ) AS GRADE
                FROM grpAttLogsResults a
                LEFT JOIN members m ON a.memberid = m.linked_member_id
                WHERE TRUE
            ";

    $query .= " ORDER BY TIMESTAMPVAL";


    // Step 2: Run the SELECT query and dump the result
    $results = DB::select($query);

    // 2. Convert to Laravel collection
    $collection = collect($results);

    // 3. Group by DATERECORD
    $grouped = $collection->groupBy('DATERECORD');

    // 4. Map into merged TIMEIN/TIMEOUT records with STATUS
    $final = $grouped->map(function ($records, $date) {
        // Extract IN and OUT records
        $timeInRecord = $records->firstWhere('STATUS', 'IN');
        $timeOutRecord = $records->firstWhere('STATUS', 'OUT');

        return (object) [
            'DATERECORD' => $date,
            'TIMEIN' => $timeInRecord?->TIMERECORD ?? '',
            'TIMEOUT' => $timeOutRecord?->TIMERECORD ?? '',
            'STATUS' => ($timeInRecord || $timeOutRecord) ? 'PRESENT' : 'ABSENT',
        ];
    })->values();
    // $final = $grouped->map(function ($records, $date) {
    //     // Filter valid time records
    //     $validTimes = $records->where('TIMERECORD', '!=', '------------');

    //     $timeIn = optional($validTimes->sortBy('TIMESTAMPVAL')->first())->TIMERECORD ?? '------------';
    //     $timeOut = optional($validTimes->sortByDesc('TIMESTAMPVAL')->first())->TIMERECORD ?? '------------';

    //     return (object) [
    //         'DATERECORD' => $date,
    //         'TIMEIN' => $timeIn,
    //         'TIMEOUT' => $timeOut,
    //         'STATUS' => ($timeIn !== '------------' || $timeOut !== '------------') ? 'PRESENT' : 'ABSENT',
    //     ];
    // })->values(); // Reset keys

    dd($collection, $grouped, $grouped->first(), $final);

});

Route::get('/3ca60d0867d667964632dce', function () {
    $records = DB::select("SELECT linked_member_id as Id, rfid, last_name as Lastname, first_name as Firstname,
        middle_name as Middlename,
        (SELECT name FROM levels WHERE id=s.level_id) as GradeLevel,
        photo as PhotoFileName, mobile_no as Mobile,
        s.can_notify = 1 as IsNotify
        FROM members m LEFT JOIN students s ON m.id=s.member_id
    ");

    $records = array_map(function ($record) {
        $record->IsNotify = (bool) $record->IsNotify;
        return $record;
    }, $records);

    return response()->json($records);
})->name('test');

Route::get('/', function () {
    if (Auth::check())
        return redirect('/students'); // or whatever route you want

    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    return redirect('/students');
})->middleware(['auth'])->name('dashboard');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', function () {
        return redirect('/students');
    })->name('students');

    Route::prefix('students')
        ->controller(StudentController::class)
        ->group(function () {
            Route::get('/', 'index')->name('students.index');
            Route::get('/{id}', 'show')->name('students.show');
            Route::get('/{id}/edit', 'edit')->name('students.edit');
            Route::match(['put', 'patch'], '/{student}', 'update')->name('students.update');
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
            Route::match(['put', 'patch'], '/{id}', 'update')->name('levels.update');
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

    Route::prefix('reports')
        ->controller(ReportController::class)
        ->group(function () {
            Route::get('/', 'index')->name('reports.index');
            Route::post('/preview-pdf-daily', 'dailyReport')->name('reports.daily');
            Route::post('/preview-pdf-detailed', 'detailedReport')->name('reports.detailed');
        });

});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
