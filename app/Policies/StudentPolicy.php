<?php

namespace App\Policies;

use App\Models\FacultyLevel;
use Illuminate\Auth\Access\Response;
use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function view(User $user, Student $student): bool
    {
        // dd($user, $student);

        if ($user->role === 'admin')
            return true;

        return $this->isStudentUnderFaculty($user, $student);
    }

    public function edit(User $user, Student $student): bool
    {
        if ($user->role === 'admin')
            return true;

        return $this->isStudentUnderFaculty($user, $student);
    }

    public function update(User $user, Student $student): bool
    {
        if ($user->role === 'admin')
            return true;

        return $this->isStudentUnderFaculty($user, $student);
    }

    private function isStudentUnderFaculty(User $user, Student $student): bool
    {
        // dd($user->member->faculty, $user->member->first_name);
        if (!$user->member->faculty)
            return false; // Only faculty should check this

        return FacultyLevel::where('faculty_id', $user->member->faculty->id)
            ->where('level_id', $student->level_id)
            ->where('section_id', $student->section_id)
            ->exists();
    }
}
