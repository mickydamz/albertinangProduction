<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use \App\Traits\Auditable;

    protected $fillable = [
        'code',
        'discount_type',
        'value',
        'max_discount_amount',
        'min_order_amount',
        'max_uses',
        'multi_use',
        'used_count',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active'           => 'boolean',
        'multi_use'           => 'boolean',
        'expires_at'          => 'datetime',
        'value'               => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'min_order_amount'    => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    // ── Business logic ────────────────────────────────────────────────────────

    public function calculateDiscount(float $subtotalNgn): float
    {
        if ($this->discount_type === 'percent') {
            $discount = $subtotalNgn * ($this->value / 100);
            if ($this->max_discount_amount) {
                $discount = min($discount, (float) $this->max_discount_amount);
            }
            return round($discount, 2);
        }

        // Fixed: never discount more than the cart total
        return min((float) $this->value, $subtotalNgn);
    }

    /**
     * Returns an error string if invalid, or null if the coupon is good to go.
     */
    public function validate(float $subtotalNgn, int $userId): ?string
    {
        if (!$this->is_active) {
            return 'This coupon is no longer active.';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'This coupon has expired.';
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return 'This coupon has reached its usage limit.';
        }

        if ($subtotalNgn < (float) $this->min_order_amount) {
            return 'Your order must be at least ₦' . number_format($this->min_order_amount, 0) . ' to use this coupon.';
        }

        if (!$this->multi_use && $this->usages()->where('user_id', $userId)->exists()) {
            return 'You have already used this coupon.';
        }

        return null;
    }

    public function describeDiscount(): string
    {
        if ($this->discount_type === 'percent') {
            $label = number_format($this->value, 0) . '% off';
            if ($this->max_discount_amount) {
                $label .= ' (up to ₦' . number_format($this->max_discount_amount, 0) . ')';
            }
            return $label;
        }

        return '₦' . number_format($this->value, 0) . ' off';
    }
}