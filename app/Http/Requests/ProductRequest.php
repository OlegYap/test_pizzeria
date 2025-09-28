<?php

namespace App\Http\Requests;

class ProductRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'description' => ['nullable'],
            'price' => ['nullable', 'numeric'],
            'type' => ['sometimes'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
