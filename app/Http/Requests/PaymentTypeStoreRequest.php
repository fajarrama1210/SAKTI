<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentTypeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'name' => [
                'required',
                'string',
                'max:60',
                Rule::unique('payment_types', 'name')->ignore($id),
            ],
            'is_monthly' => 'nullable|boolean',
            'semester_id' => 'nullable|integer|exists:semesters,id',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => \App\Entities\ResponseEntity::MSG_VAL_REQUIRED,
            'string' => \App\Entities\ResponseEntity::MSG_VAL_STRING,
            'max' => \App\Entities\ResponseEntity::MSG_VAL_MAX,
            'boolean' => \App\Entities\ResponseEntity::MSG_VAL_BOOLEAN,
            'unique' => \App\Entities\ResponseEntity::MSG_VAL_UNIQUE,
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
