<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(PaginationRequest $request)
    {
        $query = auth()->user()->orders(); // Use relation method for query builder

        return OrderResource::collection($query->paginate($request->perPage()));
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

    public function cancel(Order $order)
    {
        $order->update(['status' => StatusEnum::CANCELLED->value]);

        return response()->noContent();
    }

    public function getUserOrders(PaginationRequest $request)
    {
        $query = Order::query();

        return OrderResource::collection($query->paginate(
            $request->perPage()
        ));
    }

    public function getDeliveredOrders(PaginationRequest $request)
    {
        $query = auth()->user()->orders()->where('status', StatusEnum::DELIVERED->value);

        if (!$query) {
            return response()->noContent();
        }

        return OrderResource::collection($query->paginate($request->perPage()));
    }
}
