<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;

class OrderRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'address' => ['required','string'],
            'delivery_time' => ['required','date_format:Y-m-d H:i:s'],
            'status' => ['sometimes', Rule::in(StatusEnum::values())],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
