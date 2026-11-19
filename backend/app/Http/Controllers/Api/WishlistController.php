<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        return ProductResource::collection($request->user()->wishlistProducts()->available()->with(['category', 'brand', 'vendor.vendorApplication', 'variants'])->paginate(24));
    }

    public function store(Request $request)
    {
        $request->validate(['product_id' => ['required', 'exists:products,id']]);
        $product = Product::available()->findOrFail($request->integer('product_id'));
        $request->user()->wishlistProducts()->syncWithoutDetaching([$product->id]);

        return response()->json(['message' => 'Added to wishlist.'], 201);
    }

    public function destroy(Request $request, Product $product)
    {
        $request->user()->wishlistProducts()->detach($product->id);

        return response()->json(['message' => 'Removed from wishlist.']);
    }
}
