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

    protected $fillable = [
        'user_id',
        'status',
        'shipping_cost',
        'total',
        'total_usd',
        'payment_method',
        'payment_id',
        'reference',
        'refund_id',
        'refund_status',
        'refund_amount',
        'refunded_at',
        'refund_failure_reason',
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
        'refund_amount'       => 'decimal:2',
        'refunded_at'         => 'datetime',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
    ];

    /**
     * Gateway refund statuses that mean the money has actually moved back to the
     * customer (terminal success). Anything else a successful refund *request*
     * returns (pending / processing) is still in flight → refund_pending.
     */
    public const REFUND_TERMINAL_STATUSES = ['processed', 'succeeded', 'success', 'reversed', 'completed'];

    /**
     * Record the outcome of a gateway refund on the order — the single place that
     * maps a refund result to an order status, so financial status only ever
     * changes from a verified gateway outcome (never optimistically on failure).
     *
     * Expects the normalised shape used by the refund helpers:
     *   handled, success, refund_id, refund_status, message
     *
     * Returns the resulting status so callers can tailor their flash message.
     */
    public function applyRefundOutcome(array $result, ?float $amount = null): string
    {
        $amount ??= (float) $this->total;

        if (!empty($result['success'])) {
            $gatewayStatus = strtolower((string) ($result['refund_status'] ?? ''));
            $status = in_array($gatewayStatus, self::REFUND_TERMINAL_STATUSES, true)
                ? 'refunded'
                : 'refund_pending';

            $this->update([
                'status'                => $status,
                'refund_id'             => $result['refund_id']     ?? null,
                'refund_status'         => $result['refund_status'] ?? null,
                'refund_amount'         => $amount,
                'refunded_at'           => now(),
                'refund_failure_reason' => null,
            ]);

            return $status;
        }

        // Failed or unsupported gateway: never report this as refunded. Persist
        // the failure reason so the team can reconcile and refund manually.
        $this->update([
            'status'                => 'refund_failed',
            'refund_status'         => $result['refund_status'] ?? 'failed',
            'refund_amount'         => $amount,
            'refund_failure_reason' => $result['message'] ?? 'Refund failed.',
        ]);

        return 'refund_failed';
    }

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