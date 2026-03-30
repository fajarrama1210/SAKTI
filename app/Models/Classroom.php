<?php

namespace App\Models;

use App\Models\Major;
use App\Models\Schedule;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    protected $guarded = ['id'];

    public function major() :BelongsTo
    {
        return $this->belongsTo(Major::class);
    }


   public function students() : HasMany
   {
       return $this->hasMany(Student::class);
   }

    public function schedules() : HasMany
    {
        return $this->hasMany(Schedule::class);
    }

}
