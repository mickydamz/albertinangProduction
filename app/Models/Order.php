<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use App\Traits\Auditable;   // at top

class Order extends Model
{

use Auditable;  

    public function canCancel(): bool
    {
        $allowed = ['pending', 'paid', 'processing'];
        if ($this->fulfillment_method === 'pickup') $allowed[] = 'ready_for_pickup';
        return in_array($this->status, $allowed, true);
    }

    public function canReturn(): bool
    {
        $received = $this->fulfillment_method === 'delivery'
            ? $this->status === 'delivered'
            : ($this->fulfillment_method === 'pickup'
                ? $this->status === 'completed'
                : in_array($this->status, ['delivered', 'completed'], true));
        // Legacy orders without receipt evidence remain eligible for admin review.
        return $received && (!$this->received_at || now()->lte($this->received_at->copy()->addDays(30)));
    }

    protected $fillable = [
        'user_id',
        'status',
        'received_at',
        'shipping_cost',
        'total',
        'total_usd',
        'payment_method',
        'payment_id',
        'reference',
        'fulfillment_method',
        'pickup_location',
        'pickup_location_id',
        'pickup_point_id',
        'pickup_point_name',
        'pickup_point_address',
        'delivery_state_id',
        'delivery_state_name',
        'delivery_location_id',
        'delivery_location_name',
        'shipping_address',
        'customer_email',
        'order_number',
        'coupon_id',
        'coupon_code_used',
        'coupon_discount_ngn',
    ];

    protected $casts = [
        'total'               => 'decimal:2',
        'total_usd'           => 'decimal:2',
        'coupon_discount_ngn' => 'decimal:2',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
        'received_at'         => 'datetime',
    ];

    // ── Boot ────────────────────────────────────────────────────────────────

    // protected static function booted()
    // {
    //     static::creating(function ($order) {
    //         $cityCode = 'ENUGU';

    //         if ($order->pickup_location) {
    //             $cityCode = strtoupper(substr($order->pickup_location, 0, 3));
    //         }

    //         $order->order_number = 'ALB-' . $cityCode . '-' . strtoupper(Str::random(4)) . '-' . now()->format('His');
    //     });
    // }


    protected static function booted()
{
    static::saving(function ($order) {
        if (!$order->received_at && $order->isDirty('status') && in_array($order->status, ['delivered', 'completed'], true)) {
            $order->received_at = now();
        }
    });
    static::creating(function ($order) {
        $cityCode = 'ENU';

        if ($order->pickup_location) {
            $cityCode = strtoupper(substr($order->pickup_location, 0, 3));
        }

        do {
            $number = 'ALB-' . $cityCode . '-' . random_int(100000, 999999);
        } while (static::where('order_number', $number)->exists());

        $order->order_number = $number;
    });
}
    // ── Relationships ─────────────────────────────────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function pickupPoint(): BelongsTo
    {
        return $this->belongsTo(PickupPoint::class);
    }

    public function return(): HasOne
    {
        return $this->hasOne(OrderReturn::class);
    }

    public function cancellation(): HasOne
    {
        return $this->hasOne(OrderCancellation::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * The pre-discount total (total + coupon_discount_ngn).
     * Useful for displaying the original price on invoices.
     */
    public function getSubtotalBeforeDiscountAttribute(): float
    {
        return (float) $this->total + (float) $this->coupon_discount_ngn;
    }

    public function hasCoupon(): bool
    {
        return !empty($this->coupon_code_used);
    }

  public function isEligibleForReturn(): bool
{
    return in_array($this->status, ['shipped', 'delivered', 'completed'])
        && $this->created_at->diffInDays(now()) <= 14;
}
}