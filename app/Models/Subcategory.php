<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'category_id',
        'markup_percent',
        'discount_percent',
        'discount_expires_at',
        'is_active',
        'requires_truck',
        'estimated_weight_kg',
    ];

    protected $casts = [
        'markup_percent'       => 'float',
        'discount_percent'     => 'float',
        'discount_expires_at'  => 'datetime',
        'is_active'            => 'boolean',
        'requires_truck'       => 'boolean',
        'estimated_weight_kg'  => 'float',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Returns the discount percent only if it has not yet expired.
     */
    public function getActiveDiscountAttribute(): ?float
    {
        if (is_null($this->discount_percent)) {
            return null;
        }
        if ($this->discount_expires_at && now()->gte($this->discount_expires_at)) {
            return null; // expired
        }
        return $this->discount_percent;
    }

    public function getDiscountExpiredAttribute(): bool
    {
        return $this->discount_expires_at !== null && now()->gte($this->discount_expires_at);
    }
}