<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    protected $guarded = ['id'];

    public function classroom() : BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function user() : HasOne
    {
        return $this->hasOne(User::class);
    }

    public function paymentsDetail() : HasMany
    {
        return $this->hasMany(PaymentsDetail::class);
    }

    public function letter() : HasMany
    {
        return $this->hasMany(Letter::class);
    }
}
