<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Models\Level;
use App\Models\Member;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        // $students = Student::simplePaginate(5);

        $students->load([
            'member',
            'level',
            'section'
        ]);

        // $students = member::all()
        //     ->with('member')
        //     ->with('level')
        //     ->with('section')
        //     ->latest()
        //     ->get();

        return Inertia::render('student/Index', [
            'students' => $students
        ]);
    }

    public function show(int $studentID)
    {
        $student = Student::find($studentID);

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

        $student->load([
            'member',
            'level',
            'section'
        ]);

        return Inertia::render('student/Edit', [
            'student' => $student,
            'levels' => Level::orderBy('name')->get(),
            'sections' => Section::orderBy('name')->get()
        ]);
    }

    public function update(StudentRequest $request, string $studentID)
    {
        $student = Student::find($studentID);

        $data = $request->validated();

        $student->update([
            'section_id' => $data['section_id'],
            'level_id' => $data['level_id']
        ]);

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

        // dd($request->validated(), $student);

        // $student->load([
        //     'member',
        //     'level',
        //     'section'
        // ]);

        return redirect()->route('students.show', $studentID);
    }
}
