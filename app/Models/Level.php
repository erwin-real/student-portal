<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Level extends Model
{
    use HasFactory;

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function faculties(): BelongsToMany
    {
        return $this->belongsToMany(Faculty::class);
    }

    // public function faculties(): BelongsToMany
    // {
    //     return $this->belongsToMany(Faculty::class)
    //         ->using(FacultyLevel::class)
    //         ->withPivot(['section_id']);
    // }
}
