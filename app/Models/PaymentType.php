<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentType extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_monthly' => 'boolean',
    ];

    public function paymentRates(): HasMany
    {
        return $this->hasMany(PaymentRate::class);
    }

    public function billItems(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }
}
