<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Faculty;
use App\Models\FacultyLevel;
use App\Models\Level;
use App\Models\Member;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{

    public function index(Request $request)
    {

        $query = User::with(['member']);

        if ($search = $request->input('search')) {
            $query->when($search, function ($query, $search) {
                $query->whereHas('member', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        }

        $users = $query->paginate(10)->withQueryString();

        return Inertia::render('user/Index', [
            'users' => $users,
            'filters' => ['search' => $search],
            'gradeLevels' => Level::select('id', 'name')->get(),
            'sections' => Section::select('id', 'name')->get(),
            'loggedInUser' => $request->user()
        ]);
    }

    public function show(int $userID)
    {
        $user = User::with(['member', 'member.faculty.levelSections.level', 'member.faculty.levelSections.section'])->find($userID);

        return Inertia::render('user/Show', [
            'user' => $user
        ]);
    }

    public function edit(int $userID)
    {
        $user = User::with(['member', 'member.faculty.levelSections.level', 'member.faculty.levelSections.section'])->find($userID);

        $existingItems = [];

        if ($user->member->faculty?->levelSections) {
            foreach ($user->member->faculty->levelSections as $levelSection) {
                array_push($existingItems, [
                    "gradeLevel" => $levelSection->level,
                    "section" => $levelSection->section
                ]);
            }
        }

        return Inertia::render('user/Edit', [
            'user' => $user,
            'gradeLevels' => Level::select('id', 'name')->with('sections')->get(),
            'existingItems' => $existingItems
        ]);
    }

    public function update(UserRequest $userRequest, string $userID)
    {
        $user = User::find($userID);

        $data = $userRequest->validated();

        if (isset($data['password']) && !empty($data['password'])) {
            $user->update([
                'username' => $data['username'],
                'name' => $data['first_name'] . " " . $data['last_name'],
                'password' => Hash::make($data['password']),
            ]);
        } else {
            $user->update([
                'username' => $data['username'],
                'name' => $data['first_name'] . " " . $data['last_name'],
            ]);
        }

        $user->member()->update([
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

        FacultyLevel::where('faculty_id', $user->member->faculty->id)->delete();

        foreach ($data['grade_section_mappings'] as $gradeSection) {
            array_push($values, [
                "faculty_id" => $user->member->faculty->id,
                "level_id" => $gradeSection['grade_level_id'],
                "section_id" => $gradeSection['section_id']
            ]);
        }

        FacultyLevel::insert($values);

        return redirect()->route('users.show', $userID);
    }

    public function create()
    {
        return Inertia::render('user/Create', [
            'members' => Member::orderBy('first_name')->get(),
            'gradeLevels' => Level::select('id', 'name')->with('sections')->get()
        ]);
    }

    public function store(UserRequest $userRequest)
    {
        $data = $userRequest->validated();

        $member = Member::find($data['id']);

        $user = User::create([
            'member_id' => $data['id'],
            'name' => $data['first_name'] . " " . $data['last_name'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);

        $values = [];

        foreach ($data['grade_section_mappings'] as $gradeSection) {
            array_push($values, [
                "faculty_id" => $member->faculty->id,
                "level_id" => $gradeSection['grade_level_id'],
                "section_id" => $gradeSection['section_id']
            ]);
        }

        FacultyLevel::insert($values);

        return redirect()->route('users.show', $user->id);
    }
}
