<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MajorStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role  === 'admin' ;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:majors,name',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => \App\Entities\ResponseEntity::MSG_VAL_REQUIRED,
            'string' => \App\Entities\ResponseEntity::MSG_VAL_STRING,
            'max' => \App\Entities\ResponseEntity::MSG_VAL_MAX,
            'unique' => \App\Entities\ResponseEntity::MSG_VAL_UNIQUE,
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama Jurusan',
        ];
    }
}
