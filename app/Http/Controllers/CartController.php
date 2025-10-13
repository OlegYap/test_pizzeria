<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(PaginationRequest $request)
    {
        $query = Cart::query();

        return CartResource::collection(
            $query->paginate($request->perPage())
        );
    }

    public function store(CartRequest $request): CartResource
    {
        return new CartResource(Cart::create($request->validated()));
    }

    public function show(Cart $cart): CartResource
    {
        return new CartResource($cart);
    }

    public function update(CartRequest $request, Cart $cart): CartResource
    {
        $cart->update($request->validated());

        return new CartResource($cart);
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();

        return response()->noContent();
    }
}
