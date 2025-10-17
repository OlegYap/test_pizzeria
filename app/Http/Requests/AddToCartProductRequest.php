<?php

namespace App\Http\Requests;

use App\Rules\ProductLimitRule;

class AddToCartProductRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id', new ProductLimitRule()],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
