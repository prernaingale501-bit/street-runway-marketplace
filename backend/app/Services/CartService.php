<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function forUser(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id])->load(['items.product.category', 'items.product.brand', 'items.variant']);
    }

    public function add(User $user, array $data)
    {
        $product = Product::available()->findOrFail($data['product_id']);
        $variant = isset($data['variant_id']) ? ProductVariant::where('product_id', $product->id)->findOrFail($data['variant_id']) : null;
        if (! $variant) {
            throw ValidationException::withMessages(['variant_id' => 'A valid product variant is required.']);
        }
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $item = $cart->items()->firstOrNew(['product_id' => $product->id, 'variant_id' => $variant->id]);
        $newQuantity = ($item->exists ? $item->quantity : 0) + $data['quantity'];
        if ($newQuantity > $variant->stock) {
            throw ValidationException::withMessages(['quantity' => 'The requested quantity exceeds available stock.']);
        }
        $item->fill(['quantity' => $newQuantity, 'unit_price' => $product->price])->save();

        return $item->load(['product.category', 'product.brand', 'variant']);
    }
}
