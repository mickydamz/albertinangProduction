<?php

namespace App\Services;

use App\Models\{Coupon, CouponUsage, Order};

/** Called inside the same database transaction as paid order creation. */
class CheckoutAllocationService
{
    public function allocate(array $items, ?array $couponData, ?int $userId): ?Coupon
    {
        // Stock is confirmed by the admin; purchases do not reserve or decrement it.
        $subtotal = 0;
        foreach ($items as $item) {
            $quantity = (int) $item['quantity'];
            if ($quantity < 1) throw new \DomainException('Invalid checkout quantity.');
            $subtotal += (float) $item['effective_price_ngn'] * $quantity;
        }
        if (empty($couponData['id'])) return null;
        $coupon = Coupon::whereKey($couponData['id'])->lockForUpdate()->first();
        if (!$coupon || $coupon->validate($subtotal, $userId ?? 0) !== null) {
            throw new \DomainException('Coupon eligibility changed before payment confirmation.');
        }
        $query = Coupon::whereKey($coupon->id);
        if ($coupon->max_uses !== null) $query->where('used_count', '<', $coupon->max_uses);
        if ($query->increment('used_count') !== 1) {
            throw new \DomainException('Coupon usage limit reached before payment confirmation.');
        }
        return $coupon;
    }

    public function recordUsage(?Coupon $coupon, Order $order, ?int $userId): void
    {
        if ($coupon) CouponUsage::create(['coupon_id'=>$coupon->id, 'user_id'=>$userId, 'order_id'=>$order->id]);
    }
}
