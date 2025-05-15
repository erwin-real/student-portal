<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacultyRequest;
use App\Models\FacultyLevel;
use App\Models\Level;
use App\Models\Member;
use App\Models\Section;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacultyController extends Controller
{
    public function index(Request $request)
    {

        $query = Faculty::with(['member']);

        if ($search = $request->input('search')) {
            $query->when($search, function ($query, $search) {
                $query->whereHas('member', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        }

        $faculties = $query->paginate(10)->withQueryString();

        return Inertia::render('faculty/Index', [
            'faculties' => $faculties,
            'filters' => [
                'search' => $search
            ]
        ]);
    }

    public function show(int $facultyID)
    {
        $faculty = Faculty::with(['member', 'levelSections.level', 'levelSections.section'])->find($facultyID);

        return Inertia::render('faculty/Show', [
            'faculty' => $faculty
        ]);
    }

    public function edit(int $facultyID)
    {
        $faculty = Faculty::with(['member', 'levelSections.level', 'levelSections.section'])->find($facultyID);

        $existingItems = [];

        foreach ($faculty->levelSections as $levelSection) {
            array_push($existingItems, [
                "gradeLevel" => $levelSection->level,
                "section" => $levelSection->section
            ]);
        }

        return Inertia::render('faculty/Edit', [
            'faculty' => $faculty,
            'gradeLevels' => Level::select('id', 'name')->with('sections')->get(),
            'existingItems' => $existingItems
        ]);
    }

    public function update(FacultyRequest $request, string $facultyID)
    {
        $faculty = Faculty::find($facultyID);

        $data = $request->validated();

        $faculty->member()->update([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'address' => $data['address'],
            'date_of_birth' => $data['date_of_birth'],
            'email' => $data['email'],
            'mobile_no' => $data['mobile_no'],
        ]);

        $values = [];

        FacultyLevel::where('faculty_id', $facultyID)->delete();

        foreach ($data['grade_section_mappings'] as $gradeSection) {
            array_push($values, [
                "faculty_id" => $facultyID,
                "level_id" => $gradeSection['grade_level_id'],
                "section_id" => $gradeSection['section_id']
            ]);
        }

        FacultyLevel::insert($values);

        return redirect()->route('faculties.show', $facultyID);
    }
}
