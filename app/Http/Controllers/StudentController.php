<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Models\FacultyLevel;
use App\Models\Level;
use App\Models\Member;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        // dd(Auth::user(), Auth::user()->role);

        switch (Auth::user()->role) {
            case 'admin':
                $query = Student::with(['member', 'level', 'section']);
                $gradeLevels = Level::select('id', 'name')->get();
                break;

            case 'faculty':
                $facultyId = Auth::user()->member->faculty->id;

                // Step 1: Get all (level_id, section_id) pairs the faculty is assigned to
                $assignments = FacultyLevel::where('faculty_id', $facultyId)
                    ->get(['level_id', 'section_id']);

                // Step 2: Build the query to match students with those pairs
                $query = Student::with(['member', 'level', 'section'])
                    ->where(function ($query) use ($assignments) {
                        foreach ($assignments as $assignment) {
                            $query->orWhere(function ($q) use ($assignment) {
                                if ($assignment->section_id == null) {
                                    $q->where('level_id', $assignment->level_id);
                                } else {
                                    $q->where('level_id', $assignment->level_id)
                                        ->where('section_id', $assignment->section_id);
                                }
                            });
                        }
                    });
                $gradeLevels = Auth::user()->member->faculty->levels;
                break;

        }

        if ($search = $request->input('search')) {
            $query->when($search, function ($query, $search) {
                $query->whereHas('member', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        }

        if ($gradeLevel = $request->input('grade_level')) {
            $query->when(
                $gradeLevel,
                fn($query) =>
                $query->where('level_id', $gradeLevel)
            );
        }

        if ($sectionId = $request->input('section_id')) {
            $query->when(
                $sectionId,
                fn($query) =>
                $query->where('section_id', $sectionId)
            );
        }

        $students = $query->paginate(10)->withQueryString();

        // $gradeLevel = $request->string('grade_level');
        // $sectionId = $request->string('section_id');


        return Inertia::render('student/Index', [
            'students' => $students,
            'filters' => [
                'search' => $search,
                'grade_level' => $gradeLevel,
                'section_id' => $sectionId,
            ],
            'gradeLevels' => $gradeLevels,
            'filteredSections' => Section::where('level_id', $gradeLevel)->select('id', 'name', 'level_id')->get()
        ]);
    }

    public function show(int $studentID)
    {

        $student = Student::find($studentID);

        if (Gate::denies('view', $student)) {
            return redirect('/students')->with('error', 'Access denied.');
        }

        $student->load([
            'member',
            'level',
            'section'
        ]);

        return Inertia::render('student/Show', [
            'student' => $student
        ]);
    }

    public function edit(int $studentID)
    {
        $student = Student::find($studentID);

        if (Gate::denies('edit', $student)) {
            return redirect('/students')->with('error', 'Access denied.');
        }

        $student->load([
            'member',
            'level',
            'section'
        ]);

        return Inertia::render('student/Edit', [
            'student' => $student,
            'levels' => Level::with('sections')->get()
        ]);
    }

    public function update(StudentRequest $request, string $studentID)
    {
        $student = Student::find($studentID);

        if (Gate::denies('update', $student)) {
            return redirect('/students')->with('error', 'Access denied.');
        }

        $data = $request->validated();

        if (array_key_exists('section_id', $data)) {
            $student->update([
                'level_id' => $data['level_id'],
                'section_id' => $data['section_id']
            ]);
        } else
            $student->update(['level_id' => $data['level_id']]);

        $student->member()->update([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'address' => $data['address'],
            'date_of_birth' => $data['date_of_birth'],
            'email' => $data['email'],
            'mobile_no' => $data['mobile_no'],
        ]);

        return redirect()->route('students.show', $studentID);
    }
}
