<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function paymentRates(): HasMany
    {
        return $this->hasMany(PaymentRate::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}
