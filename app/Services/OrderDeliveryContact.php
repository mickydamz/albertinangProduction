<?php
namespace App\Services;

use App\Models\{Order, PendingCheckout};
use App\Support\DeliveryMobile;
use Illuminate\Support\Facades\DB;

final class OrderDeliveryContact
{
    /** Recover only the contact captured for this exact fulfilled checkout. */
    public function restore(int $orderId): bool
    {
        return DB::transaction(function () use ($orderId) {
            $order = Order::whereKey($orderId)->lockForUpdate()->firstOrFail();
            if ($order->delivery_mobile || !$order->reference) return false;
            $pending = PendingCheckout::where('reference', $order->reference)->whereNotNull('fulfilled_at')->first();
            if (!$pending || $pending->user_id !== $order->user_id
                || (int) round((float) $pending->total_ngn * 100) !== (int) round((float) $order->total * 100)) return false;
            $number = DeliveryMobile::normalize($pending->delivery_mobile);
            if (!$number) return false;
            $order->forceFill(['delivery_mobile' => $number])->save();
            return true;
        });
    }
}
