<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['vendor_id', 'category_id', 'brand_id', 'name', 'slug', 'description', 'price', 'mrp', 'gender', 'images', 'specifications', 'status'];

    protected function casts(): array
    {
        return ['images' => 'array', 'specifications' => 'array', 'price' => 'decimal:2', 'mrp' => 'decimal:2', 'rating' => 'decimal:2'];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'active')->whereHas('vendor', fn (Builder $vendor) => $vendor->whereNotNull('vendor_approved_at')->where('is_suspended', false));
    }
}
