<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartProductRequest;
use App\Http\Resources\CartProductResource;
use App\Models\CartProduct;

class CartProductController extends Controller
{
    public function index()
    {
        return CartProductResource::collection(CartProduct::all());
    }

    public function store(CartProductRequest $request)
    {
        return new CartProductResource(CartProduct::create($request->validated()));
    }

    public function show(CartProduct $cartProduct)
    {
        return new CartProductResource($cartProduct);
    }

    public function update(CartProductRequest $request, CartProduct $cartProduct)
    {
        $cartProduct->update($request->validated());

        return new CartProductResource($cartProduct);
    }

    public function destroy(CartProduct $cartProduct)
    {
        $cartProduct->delete();

        return response()->json();
    }
}
