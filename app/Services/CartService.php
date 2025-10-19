<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartProduct;
use Symfony\Component\HttpKernel\Exception\HttpException;

readonly class CartService
{

    public function getOrCreateCart(): Cart
    {
        $user = auth()->user();

        if (!$user) {
            throw new HttpException(401, 'Пользователь не авторизован');
        }

        return Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['user_id' => $user->id]
        );
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
