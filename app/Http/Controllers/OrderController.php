<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Http\Requests\PaginationRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(PaginationRequest $request)
    {
        $query = auth()->user()->orders();

        return OrderResource::collection($query->paginate($request->perPage()));
    }

    public function store(StoreOrderRequest $request): OrderResource
    {
        return new OrderResource(Order::create($request->validated()));
    }

    public function show(Order $order): OrderResource
    {
        if (auth()->user()->hasRole('user') && $order->user_id !== auth()->id()) {
            abort(403, 'Access denied');
        }

        return new OrderResource($order);
    }

    public function update(UpdateOrderRequest $request, Order $order): OrderResource
    {
        $order->update($request->validated());

        return new OrderResource($order);
    }

    public function cancel(Order $order)
    {
        $order->update(['status' => StatusEnum::CANCELLED->value]);

        return response()->noContent();
    }

    public function adminIndex(PaginationRequest $request)
    {
        $query = Order::query()->orderByDesc('id');

        return OrderResource::collection(
            $query->paginate($request->perPage())
        );
    }

    public function getDeliveredOrders(PaginationRequest $request)
    {
        $query = auth()->user()
            ->orders()
            ->where('status', StatusEnum::DELIVERED->value)
            ->orderByDesc('id');

        return OrderResource::collection(
            $query->paginate($request->perPage())
        );
    }
}
