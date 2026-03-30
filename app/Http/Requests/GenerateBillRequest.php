<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateBillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'semester_id' => 'required|integer|exists:semesters,id',
            'due_date'    => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'semester_id.required' => 'Semester wajib dipilih.',
            'semester_id.exists'   => 'Semester tidak ditemukan.',
            'due_date.required'    => 'Tanggal jatuh tempo wajib diisi.',
        ];
    }
}
