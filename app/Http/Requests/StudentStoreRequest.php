<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentStoreRequest extends FormRequest
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
        return [
            'id_number' => 'required|numeric|digits:16|unique:students,id_number',
            'family_card_number' => 'required|numeric|digits:16|unique:students,family_card_number',
            'nisn' => 'required|numeric|digits:10|unique:students,nisn',
            'name' => 'required|string|max:60',
            'classroom_id' => 'required|exists:classrooms,id',
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
            'id_number.numeric' => 'NIK harus berupa angka.',
            'id_number.digits' => 'NIK harus tepat 16 digit.',
            'family_card_number.unique' => 'Nomor KK ini sudah terdaftar pada siswa lain.',
            'family_card_number.required' => 'Nomor KK wajib diisi.',
            'family_card_number.numeric' => 'Nomor KK harus berupa angka.',
            'family_card_number.digits' => 'Nomor KK harus tepat 16 digit.',
            'nisn.unique' => 'NISN ini sudah terdaftar.',
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.numeric' => 'NISN harus berupa angka.',
            'nisn.digits' => 'NISN harus tepat 10 digit.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'classroom_id.required' => 'Kelas wajib dipilih.',
        ];
    }
}
