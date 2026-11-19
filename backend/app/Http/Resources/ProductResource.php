<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'slug' => $this->slug, 'name' => $this->name, 'description' => $this->description,
            'brand' => $this->whenLoaded('brand', fn () => $this->brand?->only(['id', 'name', 'slug'])),
            'category' => $this->whenLoaded('category', fn () => $this->category?->only(['id', 'name', 'slug'])),
            'vendor' => $this->whenLoaded('vendor', fn () => ['id' => $this->vendor->id, 'store_name' => $this->vendor->vendorApplication?->store_name, 'slug' => $this->vendor->vendorApplication?->slug]),
            'price' => (float) $this->price, 'mrp' => $this->mrp === null ? null : (float) $this->mrp,
            'gender' => $this->gender, 'images' => $this->images ?? [], 'specifications' => $this->specifications ?? [],
            'status' => $this->status,
            'rating' => (float) $this->rating, 'review_count' => $this->review_count,
            'variants' => $this->whenLoaded('variants', fn () => $this->variants->map(fn ($variant) => ['id' => $variant->id, 'sku' => $variant->sku, 'size' => $variant->size, 'color' => $variant->color, 'color_hex' => $variant->color_hex, 'stock' => $variant->stock])),
            'reviews' => $this->whenLoaded('reviews', fn () => $this->reviews->map(fn ($review) => ['id' => $review->id, 'rating' => $review->rating, 'title' => $review->title, 'body' => $review->body, 'verified_purchase' => $review->is_verified_purchase, 'author' => $review->user?->name])),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
