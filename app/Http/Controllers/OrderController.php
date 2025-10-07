<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();
        if ($request->has('page')) {
            return OrderResource::collection($query->paginate(15));
        }

        return OrderResource::collection($query->get());
    }

    public function store(OrderRequest $request): OrderResource
    {
        return new OrderResource(Order::create($request->validated()));
    }

    public function show(Order $order): OrderResource
    {
        return new OrderResource($order);
    }

    public function update(OrderRequest $request, Order $order): OrderResource
    {
        $order->update($request->validated());

        return new OrderResource($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->noContent();
    }
}
