<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'academic_year_id' => 'required|exists:academic_years,id',
            'name'             => 'required|string|max:255',
            'start_month'      => 'required|integer|between:1,12',
            'end_month'        => 'required|integer|between:1,12',
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
}
