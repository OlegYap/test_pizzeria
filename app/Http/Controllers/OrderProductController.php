<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderProductRequest;
use App\Http\Resources\OrderProductResource;
use App\Models\OrderProduct;
use Illuminate\Http\Request;

class OrderProductController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderProduct::query();
        if ($request->has('page')) {
            return OrderProductResource::collection($query->paginate(15));
        }

        return OrderProductResource::collection($query->get());
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

        return response()->noContent();
    }
}
