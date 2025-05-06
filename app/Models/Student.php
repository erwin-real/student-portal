<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'level_id',
        'section_id',
        'student_no',
        'guardian',
        'mobile'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
