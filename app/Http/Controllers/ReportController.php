<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Member;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {

        // dd(Member::with(['student.level', 'student.section', 'faculty'])->paginate(10)->withQueryString());

        // $query = Student::with(['member', 'level', 'section']);
        $query = Member::with(['student', 'student.level', 'student.section', 'faculty']);
        $gradeLevel = $request->string('grade_level');
        $sectionId = $request->string('section_id');

        if ($search = $request->input('search')) {
            $query->when($search, function ($query, $search) {
                // $query->whereHas('member', function ($q) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
                // });
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

        $members = $query->paginate(10)->withQueryString();

        return Inertia::render('report/Index', [
            'members' => $members,
            'filters' => [
                'search' => $search,
                'grade_level' => $gradeLevel,
                'section_id' => $sectionId,
            ],
            'gradeLevels' => Level::select('id', 'name')->get(),
            'filteredSections' => Section::where('level_id', $gradeLevel)->select('id', 'name', 'level_id')->get()
        ]);
    }
}
