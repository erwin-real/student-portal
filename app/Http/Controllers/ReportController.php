<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FacultyLevel;
use App\Models\Level;
use App\Models\Member;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {

        switch (Auth::user()->role) {
            case 'admin':
                $gradeLevels = Level::select('id', 'name')->get();

                $query = Member::with(['student', 'student.level', 'student.section', 'faculty', 'user']);

                $query->where(function ($query) {
                    $query->whereDoesntHave('user') // no user relation
                        ->orWhereHas('user', function ($q) {
                            $q->where('role', '!=', 'admin'); // user exists but not admin
                        });
                });
                break;

            case 'faculty':
                $gradeLevels = Auth::user()->member->faculty->levels;

                $facultyId = Auth::user()->member->faculty->id;

                // Step 1: Get all (level_id, section_id) pairs the faculty is assigned to
                $assignments = FacultyLevel::where('faculty_id', $facultyId)
                    ->get(['level_id', 'section_id']);

                $query = Member::with(['student.level', 'student.section', 'faculty', 'user'])
                    ->whereHas('student', function ($query) use ($assignments) {
                        $query->where(function ($subQuery) use ($assignments) {
                            foreach ($assignments as $assignment) {
                                $subQuery->orWhere(function ($q) use ($assignment) {
                                    if ($assignment->section_id == null) {
                                        $q->where('level_id', $assignment->level_id);
                                    } else {
                                        $q->where('level_id', $assignment->level_id)
                                            ->where('section_id', $assignment->section_id);
                                    }
                                });
                            }
                        });
                    });
                break;
        }

        if ($search = $request->input('search')) {
            $query->when($search, function ($query, $search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($gradeLevel = $request->input('grade_level')) {
            $query->when($gradeLevel, function ($query, $gradeLevel) {
                $query->whereHas('student', function ($q) use ($gradeLevel) {
                    $q->where('level_id', $gradeLevel);
                });
            });
        }

        if ($sectionId = $request->input('section_id')) {
            $query->when($sectionId, function ($query, $sectionId) {
                $query->whereHas('student', function ($q) use ($sectionId) {
                    $q->where('section_id', $sectionId);
                });
            });
        }

        $members = $query->orderBy('first_name')->paginate(10)->withQueryString();

        return Inertia::render('report/Index', [
            'members' => $members,
            'filters' => [
                'search' => $search,
                'grade_level' => $gradeLevel,
                'section_id' => $sectionId,
            ],
            'gradeLevels' => $gradeLevels,
            'filteredSections' => Section::where('level_id', $gradeLevel)->select('id', 'name', 'level_id')->get()
        ]);
    }

    public function dailyReport(Request $request)
    {
        $memberID = $request->input('memberID');
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $member = Member::with(['student', 'student.level', 'student.section'])
            ->where('linked_member_id', $memberID)
            ->first();

        DB::unprepared("
            DROP TEMPORARY TABLE IF EXISTS grpAttLogsResults;
            CREATE TEMPORARY TABLE grpAttLogsResults AS (
                SELECT MEMBERID, TIMERECORD, DATERECORD, TYPE AS STATUS
                FROM tblotherstationattnlogs
                WHERE MEMBERID = " . $memberID . "
            );

            DROP TEMPORARY TABLE IF EXISTS tmpResultstbl;
            CREATE TEMPORARY TABLE tmpResultstbl AS (
                SELECT MEMBERID, MIN(TIMERECORD) AS TIMERECORD, DATERECORD, 'INITIAL' AS STATUS
                FROM grpAttLogsResults
                GROUP BY MEMBERID, DATERECORD

                UNION ALL

                SELECT MEMBERID, MAX(TIMERECORD) AS TIMERECORD, DATERECORD, 'LAST' AS STATUS
                FROM grpAttLogsResults
                GROUP BY MEMBERID, DATERECORD
            );

            INSERT INTO tmpResultstbl (MEMBERID, TIMERECORD, DATERECORD, STATUS)
            SELECT DISTINCT " . $memberID . ", 0, DATERECORD, 'ABSENT'
            FROM tblotherstationattnlogs
            WHERE DATERECORD NOT IN (
                SELECT DISTINCT DATERECORD FROM grpAttLogsResults
            );
        ");

        $query = "
                SELECT
                    UNIX_TIMESTAMP(CONCAT(r.DATERECORD, ' ', r.TIMERECORD)) AS TIMESTAMPVAL,
                    r.MEMBERID,
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
                        LIMIT 1
                    ) AS GRADE
                FROM tmpResultstbl r
                LEFT JOIN members m ON r.MEMBERID = m.linked_member_id
                WHERE TRUE
            ";

        if (!empty($fromDate) && !empty($toDate)) {
            $query .= " AND DATERECORD BETWEEN '" . $fromDate . "' AND '" . $toDate . "' ";
        } elseif (!empty($fromDate)) {
            $query .= " AND DATERECORD >= '" . $fromDate . "' ";
        } elseif (!empty($toDate)) {
            $query .= " AND DATERECORD <= '" . $toDate . "' ";
        }

        $query .= " ORDER BY TIMESTAMPVAL";

        // Step 2: Run the SELECT query and dump the result
        $results = DB::select($query);

        // 2. Convert to Laravel collection
        $collection = collect($results);

        // 3. Group by DATERECORD
        $grouped = $collection->groupBy('DATERECORD');

        // 4. Map into merged TIMEIN/TIMEOUT records with STATUS
        $final = $grouped->map(function ($records, $date) {
            // Filter valid time records
            $validTimes = $records->where('TIMERECORD', '!=', '------------');

            $timeIn = optional($validTimes->sortBy('TIMESTAMPVAL')->first())->TIMERECORD ?? '------------';
            $timeOut = optional($validTimes->sortByDesc('TIMESTAMPVAL')->first())->TIMERECORD ?? '------------';

            return (object) [
                'DATERECORD' => $date,
                'TIMEIN' => $timeIn,
                'TIMEOUT' => $timeOut,
                'STATUS' => ($timeIn !== '------------' || $timeOut !== '------------') ? 'PRESENT' : 'ABSENT',
            ];
        })->values(); // Reset keys

        $pdf = Pdf::loadView('reports.template', [
            'reportType' => $request->input('reportType'),
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'records' => $final,
            'query' => $query,
            'student_name' => $member['first_name'] . " " . $member['last_name'] . " " . $member['middle_name'],
            'grade' => $member->student->level->name,
            'school_year' => '2024-2025',
            'type' => 'daily'
        ]);

        return $pdf->stream('daily.pdf');
    }

    public function detailedReport(Request $request)
    {
        $memberID = $request->input('memberID');
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $member = Member::with(['student', 'student.level', 'student.section'])
            ->where('linked_member_id', $memberID)
            ->first();

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
                WHERE memberid = " . $memberID . "
            );

            INSERT INTO grpAttLogsResults (TIMESTAMPVAL, MEMBERID, TIMERECORD, DATERECORD, STATUS)
            SELECT DISTINCT
                UNIX_TIMESTAMP(CONCAT(DATERECORD, ' ', 0)),
                " . $memberID . ",
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

        if (!empty($fromDate) && !empty($toDate)) {
            $query .= " AND DATERECORD BETWEEN '" . $fromDate . "' AND '" . $toDate . "' ";
        } elseif (!empty($fromDate)) {
            $query .= " AND DATERECORD >= '" . $fromDate . "' ";
        } elseif (!empty($toDate)) {
            $query .= " AND DATERECORD <= '" . $toDate . "' ";
        }

        $query .= " ORDER BY TIMESTAMPVAL";

        // Step 2: Run the SELECT query and dump the result
        $results = DB::select($query);

        // 2. Convert to Laravel collection
        $collection = collect($results);

        // 3. Group by DATERECORD
        $grouped = $collection->groupBy('DATERECORD');

        // 4. Map into merged TIMEIN/TIMEOUT records with STATUS
        $final = $grouped->map(function ($records, $date) {
            // Filter valid time records
            $validTimes = $records->where('TIMERECORD', '!=', '------------');

            $timeIn = optional($validTimes->sortBy('TIMESTAMPVAL')->first())->TIMERECORD ?? '------------';
            $timeOut = optional($validTimes->sortByDesc('TIMESTAMPVAL')->first())->TIMERECORD ?? '------------';

            return (object) [
                'DATERECORD' => $date,
                'TIMEIN' => $timeIn,
                'TIMEOUT' => $timeOut,
                'STATUS' => ($timeIn !== '------------' || $timeOut !== '------------') ? 'PRESENT' : 'ABSENT',
            ];
        })->values(); // Reset keys

        $pdf = Pdf::loadView('reports.template', [
            'reportType' => $request->input('reportType'),
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'records' => $final,
            'query' => $query,
            'student_name' => $member['first_name'] . " " . $member['last_name'] . " " . $member['middle_name'],
            'grade' => $member->student->level->name,
            'school_year' => '2024-2025',
            'type' => 'detailed'
        ]);

        return $pdf->stream('daily.pdf');
    }

}
