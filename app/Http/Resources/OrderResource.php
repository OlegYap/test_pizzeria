<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'address' => $this->address,
            'delivery_time' => $this->delivery_time?->format('Y-m-d H:i:s'),
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->labels(),
            ],
            'order_products' => $this->whenLoaded('orderProducts', fn() => $this->orderProducts),
        ];
    }
}
