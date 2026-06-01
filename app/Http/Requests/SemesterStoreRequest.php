<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SemesterStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */                                           
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('id');
        return [
            'academic_year_id' => 'required|exists:academic_years,id',
            'name'             => [
                'required',
                'string',
                'max:255',
                Rule::unique('semesters', 'name')
                    ->where('academic_year_id', $this->academic_year_id)
                    ->ignore($id),
            ],
            'start_month'      => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('semesters', 'start_month')
                    ->where('academic_year_id', $this->academic_year_id)
                    ->ignore($id),
            ],
            'end_month'        => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('semesters', 'end_month')
                    ->where('academic_year_id', $this->academic_year_id)
                    ->ignore($id),
            ],
            'is_active'        => 'nullable|boolean',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'academic_year_id' => 'Tahun Ajaran',
            'name'             => 'Nama Semester',
            'start_month'      => 'Bulan Mulai',
            'end_month'        => 'Bulan Akhir',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => \App\Entities\ResponseEntity::MSG_VAL_REQUIRED,
            'string'   => \App\Entities\ResponseEntity::MSG_VAL_STRING,
            'max'      => \App\Entities\ResponseEntity::MSG_VAL_MAX,
            'exists'   => \App\Entities\ResponseEntity::MSG_VAL_EXISTS,
            'integer'  => \App\Entities\ResponseEntity::MSG_VAL_INTEGER,
            'between'  => \App\Entities\ResponseEntity::MSG_VAL_BETWEEN,
            'boolean'  => \App\Entities\ResponseEntity::MSG_VAL_BOOLEAN,
            'unique'   => \App\Entities\ResponseEntity::MSG_VAL_UNIQUE,
        ];
    }
}
