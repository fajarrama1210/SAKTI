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
}
