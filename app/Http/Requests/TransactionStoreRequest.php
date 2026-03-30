<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
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
