<?php

namespace App\Services;

use App\Models\PendingCheckout;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Validation\ValidationException;

class CheckoutAvailability
{
    // Call inside a database transaction, with product rows locked in ID order.
    public function check(array $items, ?Coupon $coupon, int $userId): void
    {
        $reservations = PendingCheckout::whereNull('fulfilled_at')
            ->where('expires_at', '>', now())->get();
        $quantities = [];
        foreach ($items as $item) {
            $id = $item['id'];
            $quantities[$id] = ($quantities[$id] ?? 0) + $item['quantity'];
        }
        foreach ($quantities as $id => $quantity) {
            $product = Product::findOrFail($id);
            $reserved = $reservations->sum(fn ($pending) => collect($pending->items)
                ->where('id', $id)->sum('quantity'));
            if ($quantity > max(0, $product->stock - $reserved)) {
                throw ValidationException::withMessages(['items' => "Insufficient stock for {$product->name}. Please update your cart."]);
            }
        }
        if ($coupon) {
            $reserved = $reservations->filter(fn ($pending) => ($pending->coupon['id'] ?? null) === $coupon->id);
            if (($coupon->max_uses !== null && $coupon->used_count + $reserved->count() >= $coupon->max_uses)
                || (!$coupon->multi_use && $reserved->contains(fn ($pending) => (int) $pending->user_id === $userId))) {
                throw ValidationException::withMessages(['coupon_code' => 'This coupon is already reserved or has reached its redemption limit.']);
            }
        }
    }
}
