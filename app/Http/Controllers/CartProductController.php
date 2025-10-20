<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartProductRequest;
use App\Http\Requests\PaginationRequest;
use App\Http\Requests\UpdateCartProductRequest;
use App\Http\Resources\CartProductResource;
use App\Models\CartProduct;
use App\Services\CartService;

final class CartProductController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index(PaginationRequest $request)
    {
        $cart = $this->cartService->getOrCreateCart();

        $query = CartProduct::query()->where('cart_id', $cart->id)
        ->with('product');

        return CartProductResource::collection(
            $query->paginate($request->perPage())
        );
    }

    public function store(AddToCartProductRequest $request)
    {
        $data = $request->validated();

        $cart = $this->cartService->addToCart($data['product_id'], $data['quantity']);
        $cartProduct = CartProduct::where('cart_id', $cart->id)
            ->where('product_id', $data['product_id'])
            ->first();

        if (! $cartProduct) {
            throw new \HttpException('Товар не найден в корзине', 404);
        }

        return (new CartProductResource($cartProduct))
            ->response()
            ->setStatusCode(201);
    }

    public function show(CartProduct $cartProduct): CartProductResource
    {
        return new CartProductResource($cartProduct);
    }

    public function update(UpdateCartProductRequest $request, CartProduct $cartProduct): CartProductResource
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
