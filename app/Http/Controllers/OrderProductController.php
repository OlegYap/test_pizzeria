<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderProductRequest;
use App\Http\Resources\OrderProductResource;
use App\Models\OrderProduct;

class OrderProductController extends Controller
{
    public function index()
    {
        return OrderProductResource::collection(OrderProduct::all());
    }

    public function store(OrderProductRequest $request)
    {
        return new OrderProductResource(OrderProduct::create($request->validated()));
    }

    public function show(OrderProduct $orderProduct)
    {
        return new OrderProductResource($orderProduct);
    }

    public function update(OrderProductRequest $request, OrderProduct $orderProduct)
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
