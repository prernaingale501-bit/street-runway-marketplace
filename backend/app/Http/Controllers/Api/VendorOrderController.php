<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorOrderController extends Controller
{
    public function index(Request $request)
    {
        return Order::whereHas('items', fn ($q) => $q->where('vendor_id', $request->user()->id))
            ->with(['items' => fn ($q) => $q->where('vendor_id', $request->user()->id)])
            ->latest()->paginate(30);
    }

    public function updateItem(Request $request, Order $order, OrderItem $item)
    {
        abort_unless($item->order_id === $order->id && $item->vendor_id === $request->user()->id, 404);
        $data = $request->validate(['fulfillment_status' => ['required', Rule::in(['processing', 'shipped', 'delivered', 'cancelled'])]]);
        $item->update($data);
        $statuses = $order->items()->pluck('fulfillment_status')->unique();
        if ($statuses->count() === 1) {
            $order->update(['status' => $statuses->first()]);
        }

        return response()->json(['message' => 'Item shipping status updated.', 'fulfillment_status' => $item->fulfillment_status]);
    }
}
