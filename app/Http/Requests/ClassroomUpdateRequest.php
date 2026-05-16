<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassroomUpdateRequest extends FormRequest
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
        $id = $this->route('id');
        return [
            'name' => 'required|string|max:60|unique:classrooms,name,' . $id,
            'grade_level' => 'required|integer|in:10,11,12,13',
            'major_id' => 'required|integer|exists:majors,id',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => \App\Entities\ResponseEntity::MSG_VAL_REQUIRED,
            'string' => \App\Entities\ResponseEntity::MSG_VAL_STRING,
            'max' => \App\Entities\ResponseEntity::MSG_VAL_MAX,
            'integer' => \App\Entities\ResponseEntity::MSG_VAL_INTEGER,
            'in' => \App\Entities\ResponseEntity::MSG_VAL_IN,
            'exists' => \App\Entities\ResponseEntity::MSG_VAL_EXISTS,
            'unique' => \App\Entities\ResponseEntity::MSG_VAL_UNIQUE,
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama Kelas',
            'grade_level' => 'Tingkat Kelas',
            'major_id' => 'Jurusan',
        ];
    }
}
