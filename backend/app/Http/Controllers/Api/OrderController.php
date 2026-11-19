<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return OrderResource::collection($request->user()->orders()->with('items')->latest()->paginate(20));
    }

    public function store(CheckoutRequest $request, CheckoutService $service)
    {
        return (new OrderResource($service->place($request->user(), $request->validated())))->response()->setStatusCode(201);
    }

    public function show(Request $request, string $orderNumber)
    {
        $order = $request->user()->orders()->with(['items', 'payments'])->where('order_number', $orderNumber)->firstOrFail();

        return new OrderResource($order);
    }

    public function cancel(Request $request, string $orderNumber)
    {
        $order = $request->user()->orders()->with('items.variant')->where('order_number', $orderNumber)->firstOrFail();
        if (! in_array($order->status, ['placed', 'confirmed'], true) || $order->payment_status === 'paid') {
            return response()->json(['message' => 'This order can no longer be cancelled.'], 409);
        }
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->increment('stock', $item->quantity);
                }
            }
            $order->update(['status' => 'cancelled']);
            $order->payments()->update(['status' => 'cancelled']);
        });

        return new OrderResource($order->refresh()->load('items'));
    }

    public function payment(Request $request, string $orderNumber)
    {
        $order = $request->user()->orders()->with('payments')->where('order_number', $orderNumber)->firstOrFail();

        return response()->json(['order_number' => $order->order_number, 'payment_status' => $order->payment_status, 'payments' => $order->payments->map(fn ($payment) => ['id' => $payment->id, 'method' => $payment->method, 'amount' => (float) $payment->amount, 'status' => $payment->status, 'provider' => $payment->provider])]);
    }
}
