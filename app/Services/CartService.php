<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;

class CartService
{

    public function getOrCreateCart()
    {
        $user = auth()->user();

        if (!$user) {
            throw new \Exception('Пользователь не авторизован');
        }

        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['user_id' => $user->id]
        );

        return $cart;
    }

    public function addToCart(int $productId, int $quantity): Cart
    {
        $cart = $this->getOrCreateCart();

        $cartProduct = CartProduct::where('cart_id',$cart->id)->where('product_id',$productId)->first();

        if ($cartProduct) {
            $cartProduct->update([
                'quantity' => $cartProduct->quantity + $quantity
            ]);
        } else {
            CartProduct::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
        return $cart;
    }

}
