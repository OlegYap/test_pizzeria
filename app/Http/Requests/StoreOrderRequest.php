<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;

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
