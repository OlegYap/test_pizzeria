<?php

namespace App\Http\Requests;

class OrderRequest extends BaseRequest
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
