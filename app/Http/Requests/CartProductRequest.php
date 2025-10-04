<?php

namespace App\Http\Requests;

class CartProductRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'cart_id' => ['required', 'exists:carts,id'],
            'quantity' => ['required', 'integer'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
