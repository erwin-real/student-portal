<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfid',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'address',
        'date_of_birth',
        'email',
        'mobile_no',
        'photo'
    ];

    public function faculty()
    {
        return $this->hasOne(Faculty::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
