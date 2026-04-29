<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $studentId = $this->route('id');
        return [
            'id_number' => 'required|string|size:16|unique:students,id_number,' . $studentId,
            'family_card_number' => 'required|string|size:16|unique:students,family_card_number,' . $studentId,
            'nisn' => 'required|string|max:10|unique:students,nisn,' . $studentId,
            'name' => 'required|string|max:100',
            'classroom_id' => 'required|integer|exists:classrooms,id',
        ];
    }

    /**
     * Get customized validation messages.
     */
    public function messages(): array
    {
        return [
            'id_number.unique' => 'NIK ini sudah terdaftar pada siswa lain.',
            'id_number.required' => 'NIK wajib diisi.',
            'id_number.size' => 'NIK harus tepat 16 digit.',
            'family_card_number.unique' => 'Nomor KK ini sudah terdaftar pada siswa lain.',
            'family_card_number.required' => 'Nomor KK wajib diisi.',
            'family_card_number.size' => 'Nomor KK harus tepat 16 digit.',
            'nisn.unique' => 'NISN ini sudah terdaftar.',
            'nisn.required' => 'NISN wajib diisi.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'classroom_id.required' => 'Kelas wajib dipilih.',
        ];
    }
}
