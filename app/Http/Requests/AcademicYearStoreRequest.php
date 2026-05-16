<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcademicYearStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => \App\Entities\ResponseEntity::MSG_VAL_REQUIRED,
            'string' => \App\Entities\ResponseEntity::MSG_VAL_STRING,
            'max' => \App\Entities\ResponseEntity::MSG_VAL_MAX,
            'date' => \App\Entities\ResponseEntity::MSG_VAL_DATE,
            'after' => \App\Entities\ResponseEntity::MSG_VAL_AFTER,
            'boolean' => \App\Entities\ResponseEntity::MSG_VAL_BOOLEAN,
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama Tahun Ajaran',
            'start_date' => 'Tanggal Mulai',
            'end_date' => 'Tanggal Akhir',
            'is_active' => 'Status Aktif',
        ];
    }
}
