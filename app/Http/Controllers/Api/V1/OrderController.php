<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Orders\CreateOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
        ]);

        $query = Order::query()->with(['customer', 'user', 'items.product']);

        if (isset($data['customer_id'])) {
            $query->where('customer_id', $data['customer_id']);
        }

        if (isset($data['user_id'])) {
            $query->where('user_id', $data['user_id']);
        }

        if (isset($data['order_id'])) {
            $query->where('id', $data['order_id']);
        }

        $orders = $query->latest()->paginate(15);

        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request, CreateOrderAction $action)
    {
        $data = $request->validated();

        $customerId = $data['customer_id'] ?? null;
        $customer = $customerId ? Customer::find($customerId) : null;

        $order = $action->execute($request->user(), $customer, $data['items']);

        return (new OrderResource($order))->response()->setStatusCode(201);
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'user', 'items.product']);

        return new OrderResource($order);
    }
}
