<?php

namespace App\Http\Requests;

class CartRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
