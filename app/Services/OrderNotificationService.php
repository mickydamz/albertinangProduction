<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderNotificationService
{
    public function send(Order $order, string $mailable, string $event): void
    {
        $recipient = $order->user?->email ?? $order->customer_email;
        if (!$recipient) return;
        DB::table('order_notifications')->insertOrIgnore([
            'order_id' => $order->id, 'mailable' => $mailable, 'recipient' => $recipient,
            'event_key' => $order->id . ':' . $event, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $row = DB::table('order_notifications')->where('event_key', $order->id . ':' . $event)->first();
        $this->deliver($row->id);
    }

    public function deliver(int $id): void
    {
        DB::transaction(function () use ($id) {
            $row = DB::table('order_notifications')->where('id', $id)->lockForUpdate()->first();
            if (!$row || $row->sent_at) return;
            try {
                Mail::to($row->recipient)->send(new $row->mailable(Order::findOrFail($row->order_id)));
                DB::table('order_notifications')->where('id', $id)->update(['sent_at' => now(), 'last_error' => null, 'attempts' => $row->attempts + 1]);
            } catch (\Throwable $e) {
                Log::error('Order email delivery failed', ['notification_id' => $id, 'exception' => $e]);
                DB::table('order_notifications')->where('id', $id)->update(['last_error' => 'Delivery failed; queued for retry.', 'attempts' => $row->attempts + 1]);
            }
        });
    }
}
