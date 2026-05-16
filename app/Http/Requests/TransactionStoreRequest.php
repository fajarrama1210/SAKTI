<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    protected function prepareForValidation()
    {
        if ($this->has('amount')) {
            $this->merge([
                'amount' => preg_replace('/[^0-9]/', '', $this->amount),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category' => 'nullable|string|max:60',
            'description' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => \App\Entities\ResponseEntity::MSG_VAL_REQUIRED,
            'date' => \App\Entities\ResponseEntity::MSG_VAL_DATE,
            'in' => \App\Entities\ResponseEntity::MSG_VAL_IN,
            'string' => \App\Entities\ResponseEntity::MSG_VAL_STRING,
            'max' => \App\Entities\ResponseEntity::MSG_VAL_MAX,
            'integer' => \App\Entities\ResponseEntity::MSG_VAL_INTEGER,
            'min' => \App\Entities\ResponseEntity::MSG_VAL_MIN,
        ];
    }

    public function attributes(): array
    {
        return [
            'date' => 'Tanggal Transaksi',
            'type' => 'Tipe Transaksi',
            'category' => 'Kategori',
            'description' => 'Keterangan',
            'amount' => 'Nominal',
        ];
    }
}
