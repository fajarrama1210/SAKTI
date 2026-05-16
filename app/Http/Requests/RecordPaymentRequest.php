<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,qris',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => \App\Entities\ResponseEntity::MSG_VAL_REQUIRED,
            'integer' => \App\Entities\ResponseEntity::MSG_VAL_INTEGER,
            'min' => \App\Entities\ResponseEntity::MSG_VAL_MIN,
            'in' => \App\Entities\ResponseEntity::MSG_VAL_IN,
            'date' => \App\Entities\ResponseEntity::MSG_VAL_DATE,
            'string' => \App\Entities\ResponseEntity::MSG_VAL_STRING,
            'max' => \App\Entities\ResponseEntity::MSG_VAL_MAX,
        ];
    }

    public function attributes(): array
    {
        return [
            'amount' => 'Nominal Pembayaran',
            'payment_method' => 'Metode Pembayaran',
            'payment_date' => 'Tanggal Pembayaran',
            'reference_number' => 'Nomor Referensi',
            'notes' => 'Catatan',
        ];
    }
}
