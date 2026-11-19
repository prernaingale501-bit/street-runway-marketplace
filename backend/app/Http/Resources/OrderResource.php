<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'order_number' => $this->order_number, 'status' => $this->status,
            'payment_method' => $this->payment_method, 'payment_status' => $this->payment_status,
            'subtotal' => (float) $this->subtotal, 'discount' => (float) $this->discount,
            'shipping' => (float) $this->shipping, 'total' => (float) $this->total, 'coupon_code' => $this->coupon_code,
            'shipping_address' => $this->shipping_address,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => ['id' => $item->id, 'product_id' => $item->product_id, 'product_name' => $item->product_name, 'vendor_id' => $item->vendor_id, 'size' => $item->size, 'color' => $item->color, 'unit_price' => (float) $item->unit_price, 'quantity' => $item->quantity])),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
