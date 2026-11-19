<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::available()->with(['category', 'brand', 'vendor.vendorApplication', 'variants']);
        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
        }
        if ($request->filled('brand')) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $request->string('brand')));
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->string('gender'));
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->float('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->float('max_price'));
        }
        if ($request->filled('discount')) {
            $discount = min(max($request->integer('discount'), 0), 100);
            $query->whereNotNull('mrp')->whereRaw('100.0 * (mrp - price) / mrp >= ?', [$discount]);
        }
        if ($request->filled('size')) {
            $query->whereHas('variants', fn ($q) => $q->where('size', $request->string('size'))->where('stock', '>', 0));
        }
        if ($request->filled('color')) {
            $query->whereHas('variants', fn ($q) => $q->where('color', 'like', '%'.$request->string('color').'%')->where('stock', '>', 0));
        }
        if ($request->filled('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%')->orWhere('description', 'like', '%'.$request->string('search').'%'));
        }
        match ($request->string('sort')->value()) {
            'price-low' => $query->orderBy('price'), 'price-high' => $query->orderByDesc('price'),
            'newest' => $query->latest(), 'rating' => $query->orderByDesc('rating'), 'popularity' => $query->orderByDesc('review_count'), default => $query->orderByDesc('review_count'),
        };

        return ProductResource::collection($query->paginate(min(max($request->integer('per_page', 12), 1), 60)));
    }

    public function show(string $slug)
    {
        return new ProductResource(Product::available()->with(['category', 'brand', 'vendor.vendorApplication', 'variants', 'reviews.user:id,name'])->where('slug', $slug)->firstOrFail());
    }

    public function categories()
    {
        return Category::where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug', 'parent_id']);
    }

    public function brands()
    {
        return Brand::where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug']);
    }
}
