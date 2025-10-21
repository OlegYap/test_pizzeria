<?php

namespace App\Http\Resources;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Cart */
class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'user_id' => $this->user_id,

            'products' => CartProductResource::collection($this->whenLoaded('products')),

            'total_items' => $this->whenLoaded('cartProducts', fn() => $this->cartProducts->sum('quantity')),

            'total_price' => $this->whenLoaded('cartProducts', fn() => $this->cartProducts->sum(
                fn($item) => $item->product->price * $item->quantity
            )),
        ];
    }
}
