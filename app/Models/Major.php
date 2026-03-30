<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
    protected $guarded = ['id'];
    public function classrooms() : HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
