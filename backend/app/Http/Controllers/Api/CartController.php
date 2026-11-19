<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddCartItemRequest;
use App\Http\Resources\ProductResource;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request)
    {
        $cart = app(CartService::class)->forUser($request->user());
        $items = $cart->items->map(fn ($item) => [
            'id' => $item->id, 'quantity' => $item->quantity, 'unit_price' => (float) $item->unit_price,
            'product' => (new ProductResource($item->product))->resolve(),
            'variant' => $item->variant?->only(['id', 'size', 'color', 'color_hex', 'stock']),
            'line_total' => (float) $item->unit_price * $item->quantity,
        ]);

        return response()->json(['items' => $items, 'subtotal' => round($items->sum('line_total'), 2)]);
    }

    public function store(AddCartItemRequest $request, CartService $service)
    {
        $item = $service->add($request->user(), $request->validated());

        return response()->json(['id' => $item->id, 'product_id' => $item->product_id, 'variant_id' => $item->variant_id, 'quantity' => $item->quantity, 'unit_price' => (float) $item->unit_price], 201);
    }

    public function update(Request $request, CartItem $item)
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        abort_unless($item->cart()->where('user_id', $request->user()->id)->exists(), 404);
        if (! $item->variant || $request->integer('quantity') > $item->variant->stock) {
            return response()->json(['message' => 'The requested quantity exceeds available stock.'], 422);
        }
        $item->update(['quantity' => $request->integer('quantity')]);

        return response()->json(['message' => 'Cart updated.']);
    }

    public function destroy(Request $request, CartItem $item)
    {
        abort_unless($item->cart()->where('user_id', $request->user()->id)->exists(), 404);
        $item->delete();

        return response()->json(['message' => 'Item removed from cart.']);
    }
}
