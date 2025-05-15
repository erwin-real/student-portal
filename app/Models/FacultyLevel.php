<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class FacultyLevel extends Pivot
{
    // public function sections(): BelongsToMany
    // {
    //     return $this->belongsToMany(Section::class);
    // }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
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
