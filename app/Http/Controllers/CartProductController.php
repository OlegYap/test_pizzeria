<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartProductRequest;
use App\Http\Resources\CartProductResource;
use App\Models\CartProduct;

class CartProductController extends Controller
{
    public function index()
    {
        return CartProductResource::collection(CartProduct::query()->paginate(15));
    }

    public function store(CartProductRequest $request): CartProductResource
    {
        return new CartProductResource(CartProduct::create($request->validated()));
    }

    public function show(CartProduct $cartProduct): CartProductResource
    {
        return new CartProductResource($cartProduct);
    }

    public function update(CartProductRequest $request, CartProduct $cartProduct): CartProductResource
    {
        $cartProduct->update($request->validated());

        return new CartProductResource($cartProduct);
    }

    public function destroy(CartProduct $cartProduct)
    {
        $cartProduct->delete();

        return response()->noContent();
    }
}
