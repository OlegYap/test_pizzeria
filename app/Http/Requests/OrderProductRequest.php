<?php

namespace App\Http\Requests;


class OrderProductRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'exists:orders'],
            'product_id' => ['required', 'exists:products'],
            'quantity' => ['required', 'integer'],
            'price' => ['required', 'numeric'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
