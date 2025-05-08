<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Level;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {

        $query = User::with(['member']);
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

        $users = $query->paginate(10)->withQueryString();

        return Inertia::render('user/Index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                // 'grade_level_id' => $gradeLevel,
                // 'section_id' => $sectionId,
            ],
            'gradeLevels' => Level::select('id', 'name')->get(),
            'sections' => Section::select('id', 'name')->get(),
        ]);
    }

    public function show(int $userID)
    {
        $user = User::find($userID)->load(['member']);

        return Inertia::render('user/Show', [
            'user' => $user
        ]);
    }

    public function edit(int $userID)
    {
        $user = User::find($userID)->load(['member']);

        return Inertia::render('user/Edit', [
            'user' => $user,
            'levels' => Level::orderBy('name')->get(),
            'sections' => Section::orderBy('name')->get()
        ]);
    }

    public function update(UserRequest $request, string $userID)
    {
        $user = User::find($userID);

        $data = $request->validated();

        // $user->update([
        //     'section_id' => $data['section_id'],
        //     'level_id' => $data['level_id']
        // ]);

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

        return redirect()->route('users.show', $userID);
    }
}
