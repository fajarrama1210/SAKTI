<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    protected function prepareForValidation()
    {
        if ($this->has('amount')) {
            $this->merge([
                'amount' => preg_replace('/[^0-9]/', '', $this->amount),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'payment_type_id' => 'required|integer|exists:payment_types,id',
            'grade_level' => 'required|integer|in:10,11,12,13',
            'major_id' => 'nullable|integer|exists:majors,id',
            'amount' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => \App\Entities\ResponseEntity::MSG_VAL_REQUIRED,
            'integer' => \App\Entities\ResponseEntity::MSG_VAL_INTEGER,
            'exists' => \App\Entities\ResponseEntity::MSG_VAL_EXISTS,
            'in' => \App\Entities\ResponseEntity::MSG_VAL_IN,
            'min' => \App\Entities\ResponseEntity::MSG_VAL_MIN,
        ];
    }

    public function attributes(): array
    {
        return [
            'academic_year_id' => 'Tahun Ajaran',
            'payment_type_id' => 'Jenis Pembayaran',
            'grade_level' => 'Tingkat Kelas',
            'major_id' => 'Jurusan',
            'amount' => 'Nominal Tarif',
        ];
    }
}
