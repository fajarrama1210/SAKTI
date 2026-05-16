<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentTypeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:60',
            'is_monthly' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => \App\Entities\ResponseEntity::MSG_VAL_REQUIRED,
            'string' => \App\Entities\ResponseEntity::MSG_VAL_STRING,
            'max' => \App\Entities\ResponseEntity::MSG_VAL_MAX,
            'boolean' => \App\Entities\ResponseEntity::MSG_VAL_BOOLEAN,
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama Jenis Pembayaran',
            'is_monthly' => 'Bulanan (Ya/Tidak)',
        ];
    }
}
