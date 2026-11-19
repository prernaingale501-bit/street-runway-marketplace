<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class VendorProductController extends Controller
{
    public function index(Request $request)
    {
        return ProductResource::collection($request->user()->products()->with(['category', 'brand', 'variants'])->latest()->paginate(30));
    }

    public function store(StoreProductRequest $request, ProductService $service)
    {
        $product = $service->save($request->user(), $request->validated());

        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    public function update(StoreProductRequest $request, Product $product, ProductService $service)
    {
        abort_unless($product->vendor_id === $request->user()->id, 404);

        return new ProductResource($service->save($request->user(), $request->validated(), $product));
    }

    public function destroy(Request $request, Product $product)
    {
        abort_unless($product->vendor_id === $request->user()->id, 404);
        $product->delete();

        return response()->json(['message' => 'Product removed.']);
    }
}
