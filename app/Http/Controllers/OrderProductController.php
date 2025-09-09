<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderProductRequest;
use App\Http\Resources\OrderProductResource;
use App\Models\OrderProduct;

class OrderProductController extends Controller
{
    public function index()
    {
        return OrderProductResource::collection(OrderProduct::query()->paginate(15));
    }

    public function store(OrderProductRequest $request): OrderProductResource
    {
        return new OrderProductResource(OrderProduct::create($request->validated()));
    }

    public function show(OrderProduct $orderProduct): OrderProductResource
    {
        return new OrderProductResource($orderProduct);
    }

    public function update(OrderProductRequest $request, OrderProduct $orderProduct): OrderProductResource
    {
        $orderProduct->update($request->validated());

        return new OrderProductResource($orderProduct);
    }

    public function destroy(OrderProduct $orderProduct)
    {
        $orderProduct->delete();

        return response()->json();
    }
}
