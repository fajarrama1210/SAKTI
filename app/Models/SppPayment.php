<?php

namespace App\Models;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SppPayment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'payment_date' => 'date_time',
    ];

    public function paymentsDetails() : HasMany
    {
        return $this->hasMany(PaymentsDetail::class);
    }

    public function transaction() : HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
