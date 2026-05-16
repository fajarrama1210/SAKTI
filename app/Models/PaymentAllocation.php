<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    protected $guarded = ['id'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function billItem(): BelongsTo
    {
        return $this->belongsTo(BillItem::class);
    }
}
