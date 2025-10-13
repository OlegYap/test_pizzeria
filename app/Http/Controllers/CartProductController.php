<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartProductRequest;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\CartProductResource;
use App\Models\CartProduct;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartProductController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService) {
        $this->cartService = $cartService;
    }

    public function index(PaginationRequest $request)
    {
        $query = CartProduct::query();

        return CartProductResource::collection(
            $query->paginate($request->perPage())
        );
    }

    public function store(CartProductRequest $request)
    {
        $data = $request->validated();

        $cart = $this->cartService->addToCart($data['product_id'], $data['quantity']);
        $cartProduct = CartProduct::where('cart_id', $cart->id)
            ->where('product_id', $data['product_id'])
            ->first();

        if (!$cartProduct) {
            throw new \Exception('Товар не найден в корзине', 404);
        }

        return (new CartProductResource($cartProduct))
            ->response()
            ->setStatusCode(201);
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
