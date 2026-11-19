<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate(['rating' => ['required', 'integer', 'between:1,5'], 'title' => ['nullable', 'string', 'max:160'], 'body' => ['nullable', 'string', 'max:5000'], 'order_item_id' => ['nullable', 'integer', 'exists:order_items,id']]);
        $verified = false;
        if (! empty($data['order_item_id'])) {
            $verified = OrderItem::whereKey($data['order_item_id'])->where('product_id', $product->id)->whereHas('order', fn ($q) => $q->where('user_id', $request->user()->id)->where('status', 'delivered'))->exists();
            if (! $verified) {
                return response()->json(['message' => 'The selected order item does not belong to you or is not eligible for review.'], 422);
            }
        }
        $review = Review::updateOrCreate(['user_id' => $request->user()->id, 'product_id' => $product->id], [...$data, 'is_verified_purchase' => $verified]);
        $product->forceFill(['rating' => $product->reviews()->avg('rating') ?? 0, 'review_count' => $product->reviews()->count()])->save();

        return response()->json($review, $review->wasRecentlyCreated ? 201 : 200);
    }
}
