<?php

namespace App\Http\Requests;

class StoreOrderRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'address' => ['required','string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
