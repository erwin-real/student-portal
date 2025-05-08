<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacultyRequest;
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
        // $gradeLevel = $request->string('grade_level');
        // $sectionId = $request->string('section_id');

        if ($search = $request->input('search')) {
            $query->when($search, function ($query, $search) {
                $query->whereHas('member', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        }

        // if ($gradeLevel = $request->input('grade_level')) {
        //     $query->when(
        //         $gradeLevel,
        //         fn($query) =>
        //         $query->where('level_id', $gradeLevel)
        //     );
        // }

        // if ($sectionId = $request->input('section_id')) {
        //     $query->when(
        //         $sectionId,
        //         fn($query) =>
        //         $query->where('section_id', $sectionId)
        //     );
        // }

        $faculties = $query->paginate(10)->withQueryString();

        return Inertia::render('faculty/Index', [
            'faculties' => $faculties,
            'filters' => [
                'search' => $search,
                // 'grade_level_id' => $gradeLevel,
                // 'section_id' => $sectionId,
            ],
            // 'gradeLevels' => Level::select('id', 'name')->get(),
            // 'sections' => Section::select('id', 'name')->get(),
        ]);
    }

    public function show(int $facultyID)
    {
        $faculty = Faculty::find($facultyID)->load(['member']);

        return Inertia::render('faculty/Show', [
            'faculty' => $faculty
        ]);
    }

    public function edit(int $facultyID)
    {
        $faculty = Faculty::find($facultyID)->load(['member']);

        return Inertia::render('faculty/Edit', [
            'faculty' => $faculty,
            'levels' => Level::orderBy('name')->get(),
            'sections' => Section::orderBy('name')->get()
        ]);
    }

    public function update(FacultyRequest $request, string $facultyID)
    {
        $faculty = Faculty::find($facultyID);

        $data = $request->validated();

        // $faculty->update([
        //     'section_id' => $data['section_id'],
        //     'level_id' => $data['level_id']
        // ]);

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

        return redirect()->route('faculties.show', $facultyID);
    }
}
