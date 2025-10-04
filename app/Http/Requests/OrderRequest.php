<?php

namespace App\Http\Requests;

class OrderRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'address' => ['required','string'],
            'delivery_time' => ['required','string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
