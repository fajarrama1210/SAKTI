<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleStoreRequest extends FormRequest
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
            'schedules' => 'required|array|min:1',
            'schedules.*.day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'schedules.*.start_time' => 'required|string',
            'schedules.*.duration' => 'required|integer|min:1',
            'schedules.*.subject' => 'required|string|max:100',
            'schedules.*.room' => 'required|string|max:100',
            'schedules.*.teacher_name' => 'required|string|max:150',
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
            'schedules.*.day' => 'Hari',
            'schedules.*.start_time' => 'Jam Mulai',
            'schedules.*.duration' => 'Durasi (Jam)',
            'schedules.*.subject' => 'Mata Pelajaran',
            'schedules.*.room' => 'Ruang',
            'schedules.*.teacher_name' => 'Guru',
        ];
    }
}
