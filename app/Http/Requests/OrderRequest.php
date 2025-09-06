<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users'],
            'address' => ['nullable'],
            'delivery_time' => ['nullable'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
