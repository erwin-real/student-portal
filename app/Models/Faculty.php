<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // public function levels()
    // {
    //     return $this->belongsToMany(related: Level::class);
    // }

    public function levelSections()
    {
        return $this->hasMany(FacultyLevel::class);
    }

    public function levels()
    {
        return $this->hasManyThrough(
            Level::class,
            FacultyLevel::class,
            'faculty_id',     // Foreign key on FacultyLevelSection
            'id',             // Local key on Level
            'id',             // Local key on Faculty
            'level_id'        // Foreign key on FacultyLevelSection
        )->distinct()->orderBy('id');
    }

}
