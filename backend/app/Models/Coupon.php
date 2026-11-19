<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'min_order', 'max_discount', 'starts_at', 'expires_at', 'is_active'];

    protected function casts(): array
    {
        return ['value' => 'decimal:2', 'min_order' => 'decimal:2', 'max_discount' => 'decimal:2', 'starts_at' => 'datetime', 'expires_at' => 'datetime', 'is_active' => 'boolean'];
    }

    public function isCurrentlyActive(): bool
    {
        return $this->is_active && (! $this->starts_at || $this->starts_at->isPast()) && (! $this->expires_at || $this->expires_at->isFuture());
    }
}
