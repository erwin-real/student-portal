<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id',
        'name',
        'code',
        'description'
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
