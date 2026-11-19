<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\VendorApplication;
use Illuminate\Http\Request;

class VendorStoreController extends Controller
{
    public function show(string $slug, Request $request)
    {
        $store = VendorApplication::where('slug', $slug)->where('status', 'approved')->with('user')->firstOrFail();
        $products = $store->user->products()->available()->with(['category', 'brand', 'vendor.vendorApplication', 'variants'])->paginate(min(max($request->integer('per_page', 12), 1), 60));

        return response()->json(['store' => ['name' => $store->store_name, 'slug' => $store->slug, 'description' => $store->description, 'city' => $store->city, 'categories' => $store->categories], 'products' => ProductResource::collection($products)]);
    }
}
