<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Letter extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'submission_date' => 'date',
    ];

    public function student() : BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
