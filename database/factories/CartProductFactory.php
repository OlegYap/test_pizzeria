<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartProductFactory extends Factory
{
    protected $model = CartProduct::class;

    public function definition(): array
    {
        return [
            'quantity' => $this->faker->randomNumber(),

            'product_id' => Product::factory(),
            'cart_id' => Cart::factory(),
        ];
    }
}
