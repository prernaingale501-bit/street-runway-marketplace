<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public function save(User $vendor, array $data, ?Product $product = null): Product
    {
        return DB::transaction(function () use ($vendor, $data, $product) {
            $variants = $data['variants'];
            unset($data['variants']);
            $data['vendor_id'] = $vendor->id;
            $data['slug'] = $product?->slug ?? $this->uniqueSlug($data['name']);
            $data['status'] ??= 'draft';
            $product ??= new Product;
            $product->fill($data)->save();
            $product->variants()->delete();
            $product->variants()->createMany($variants);

            return $product->load(['variants', 'category', 'brand']);
        });
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        for ($suffix = 2; Product::where('slug', $slug)->exists(); $suffix++) {
            $slug = $base.'-'.$suffix;
        }

        return $slug;
    }
}
