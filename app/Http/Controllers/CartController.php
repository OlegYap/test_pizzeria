<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;

class CartController extends Controller
{
    public function index()
    {
        return CartResource::collection(Cart::all());
    }

    public function store(CartRequest $request)
    {
        return new CartResource(Cart::create($request->validated()));
    }

    public function show(Cart $cart)
    {
        return new CartResource($cart);
    }

    public function update(CartRequest $request, Cart $cart)
    {
        $cart->update($request->validated());

        return new CartResource($cart);
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();

        return response()->json();
    }
}
