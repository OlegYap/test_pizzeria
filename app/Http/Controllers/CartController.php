<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request): CartResource
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->with('cartProducts.product')
            ->firstOrCreate(['user_id' => $request->user()->id]);

        return new CartResource($cart);
    }

    public function clear(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Корзина не найден'], 404);
        }

        $cart->cartProducts()->delete();

        return response()->noContent();
    }
}
