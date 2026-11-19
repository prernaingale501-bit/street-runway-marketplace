<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function place(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $cart = Cart::where('user_id', $user->id)->with(['items.product.vendor', 'items.variant'])->lockForUpdate()->first();
            if (! $cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
            }
            $address = isset($data['address_id']) ? Address::where('user_id', $user->id)->findOrFail($data['address_id']) : null;
            $shippingAddress = $address?->only(['full_name', 'phone', 'line1', 'line2', 'city', 'state', 'country', 'pincode']) ?? $data['address'];
            $subtotal = 0.0;
            foreach ($cart->items as $item) {
                $variant = $item->variant()->lockForUpdate()->first();
                if (! $variant || $variant->stock < $item->quantity || $item->product->status !== 'active' || ! $item->product->vendor->vendor_approved_at || $item->product->vendor->is_suspended) {
                    throw ValidationException::withMessages(['cart' => "{$item->product->name} is no longer available in the requested quantity."]);
                }
                $subtotal += (float) $item->product->price * $item->quantity;
            }
            $coupon = null;
            $discount = 0.0;
            if (! empty($data['coupon_code'])) {
                $coupon = Coupon::where('code', strtoupper($data['coupon_code']))->first();
                if (! $coupon || ! $coupon->isCurrentlyActive() || $subtotal < (float) $coupon->min_order) {
                    throw ValidationException::withMessages(['coupon_code' => 'This coupon is invalid, expired, or the order minimum is not met.']);
                }
                $discount = $coupon->type === 'flat' ? (float) $coupon->value : $subtotal * (float) $coupon->value / 100;
                if ($coupon->max_discount !== null) {
                    $discount = min($discount, (float) $coupon->max_discount);
                }
                $discount = min($discount, $subtotal);
            }
            $shipping = $subtotal >= 999 ? 0 : 99;
            $order = Order::create([
                'user_id' => $user->id, 'address_id' => $address?->id, 'order_number' => 'SR-'.Str::upper(Str::random(10)),
                'status' => 'placed', 'payment_method' => $data['payment_method'], 'payment_status' => 'pending',
                'subtotal' => $subtotal, 'discount' => $discount, 'shipping' => $shipping,
                'total' => $subtotal - $discount + $shipping, 'coupon_code' => $coupon?->code, 'shipping_address' => $shippingAddress,
            ]);
            foreach ($cart->items as $item) {
                $variant = $item->variant;
                $order->items()->create([
                    'product_id' => $item->product_id, 'vendor_id' => $item->product->vendor_id, 'variant_id' => $variant->id,
                    'product_name' => $item->product->name, 'size' => $variant->size, 'color' => $variant->color,
                    'unit_price' => $item->product->price, 'quantity' => $item->quantity,
                ]);
                $variant->decrement('stock', $item->quantity);
            }
            Payment::create(['order_id' => $order->id, 'method' => $data['payment_method'], 'amount' => $order->total, 'status' => 'pending', 'provider' => 'pending_integration']);
            $cart->items()->delete();

            return $order->load(['items', 'payments']);
        });
    }
}
