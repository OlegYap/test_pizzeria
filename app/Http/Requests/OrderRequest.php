<?php

namespace App\Http\Requests;

class OrderRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'address' => ['required','string'],
            'delivery_time' => ['required','date_format:Y-m-d H:i:s'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
