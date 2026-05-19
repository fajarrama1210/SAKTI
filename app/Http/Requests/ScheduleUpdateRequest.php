<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'classroom_id' => 'required|integer|exists:classrooms,id',
            'day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required|string',
            'duration' => 'required|integer|min:1',
            'subject' => 'required|string|max:100',
            'room' => 'required|string|max:100',
            'teacher_name' => 'required|string|max:150',
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
            'min' => \App\Entities\ResponseEntity::MSG_VAL_MIN,
        ];
    }

    public function attributes(): array
    {
        return [
            'classroom_id' => 'Kelas',
            'day' => 'Hari',
            'start_time' => 'Jam Mulai',
            'duration' => 'Durasi (Menit)',
            'subject' => 'Mata Kuliah',
            'room' => 'Ruang',
            'teacher_name' => 'Guru',
        ];
    }
}
