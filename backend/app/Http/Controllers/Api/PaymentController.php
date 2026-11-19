<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function initiate(Request $request)
    {
        $data = $request->validate(['order_id' => ['required', 'integer', 'exists:orders,id'], 'method' => ['required', 'in:cod,upi,card,netbanking']]);
        $order = $request->user()->orders()->whereKey($data['order_id'])->firstOrFail();
        if ($order->payment_method !== $data['method']) {
            return response()->json(['message' => 'Payment method does not match this order.'], 422);
        }
        if ($order->status === 'cancelled') {
            return response()->json(['message' => 'Cancelled orders cannot be paid.'], 409);
        }
        $payment = $order->payments()->latest()->first();
        if (! $payment) {
            $payment = Payment::create(['order_id' => $order->id, 'provider' => 'pending_integration', 'method' => $data['method'], 'amount' => $order->total, 'status' => 'pending']);
        }

        return response()->json(['payment_id' => $payment->id, 'order_number' => $order->order_number, 'provider' => $payment->provider, 'status' => $payment->status, 'amount' => (float) $payment->amount, 'message' => 'Payment provider is not configured; no charge was attempted.'], 202);
    }
}
