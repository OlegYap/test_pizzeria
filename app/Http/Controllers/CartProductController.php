<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartProductRequest;
use App\Http\Resources\CartProductResource;
use App\Models\CartProduct;
use Illuminate\Http\Request;

class CartProductController extends Controller
{
    public function index(Request $request)
    {
        $query = CartProduct::query();

        if ($request->has('page')) {
            return CartProductResource::collection($query->paginate(15));
        }

        return CartProductResource::collection($query->get());
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
