<?php

namespace App\Services;

use App\Models\Order;
use App\Models\RefundAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RefundService
{
    public function request(Order $order, float $amount, string $key): RefundAttempt
    {
        $attempt = DB::transaction(function () use ($order, $amount, $key) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $existing = RefundAttempt::where('request_key', $key)->first();
            if ($existing) {
                if ($existing->order_id !== $order->id || (float) $existing->amount !== $amount) {
                    throw ValidationException::withMessages(['request_key' => 'This refund reference is already in use.']);
                }
                return $existing;
            }
            $reserved = RefundAttempt::where('order_id', $order->id)->where('status', '!=', 'failed')->sum('amount');
            $legacy = RefundAttempt::where('order_id', $order->id)->exists() ? 0 : (float) $order->refund_amount;
            if ($amount <= 0 || round($reserved + $legacy + $amount, 2) > (float) $order->total) {
                throw ValidationException::withMessages(['amount' => 'Refund exceeds the remaining refundable amount.']);
            }
            if (!in_array($order->payment_method, ['paystack', 'stripe'], true)) {
                throw ValidationException::withMessages(['amount' => 'This payment must be refunded manually.']);
            }
            $gatewayAmount = $order->payment_method === 'stripe'
                ? (int) round($amount / (float) $order->total * (float) $order->total_usd * 100)
                : (int) round($amount * 100);
            $order->update(['refund_requested_at' => now()]);
            return RefundAttempt::create([
                'order_id' => $order->id, 'request_key' => $key, 'gateway' => $order->payment_method,
                'amount' => $amount, 'gateway_amount' => $gatewayAmount,
            ]);
        }, 3);

        // A committed claim survives process crashes. Unknown outcomes are reconciled,
        // never blindly submitted again (Paystack does not promise idempotent POSTs).
        if (RefundAttempt::whereKey($attempt->id)->where('status', 'requested')->update(['status' => 'processing']) !== 1) {
            return $attempt->fresh();
        }
        if (($attempt->gateway === 'stripe' && !str_starts_with($order->payment_id ?? '', 'pi_'))
            || ($attempt->gateway === 'paystack' && !$order->reference)) {
            $attempt->update(['status' => 'failed', 'failure_reason' => 'Missing payment reference; please process manually.']);
            $this->syncOrder($attempt->order_id);
            return $attempt->fresh();
        }
        try {
            if ($attempt->gateway === 'stripe') {
                $response = Http::withToken(config('services.stripe.secret'))->timeout(20)
                    ->withHeaders(['Idempotency-Key' => $key])->asForm()
                    ->post('https://api.stripe.com/v1/refunds', [
                        'payment_intent' => $order->payment_id, 'amount' => $attempt->gateway_amount,
                        'metadata' => ['refund_reference' => $key],
                    ]);
                $data = $response->json();
            } else {
                $response = Http::withToken(config('services.paystack.secret'))->timeout(20)
                    ->post('https://api.paystack.co/refund', [
                        'transaction' => $order->reference, 'amount' => $attempt->gateway_amount,
                        'currency' => 'NGN', 'merchant_note' => $key,
                    ]);
                $data = $response->json('data') ?? [];
            }
            if ($response->serverError()) {
                $attempt->update(['status' => 'unknown', 'failure_reason' => 'Gateway response is uncertain; reconciliation required.']);
            } elseif (!$response->successful() || empty($data['id'])) {
                $attempt->update(['status' => 'failed', 'failure_reason' => 'Gateway rejected the refund request.']);
            } else {
                $attempt->update(['gateway_id' => (string) $data['id']]);
                $this->record($attempt, $data['status'] ?? 'pending');
            }
        } catch (\Throwable $e) {
            Log::error('Refund request failed', ['reference' => $key, 'exception' => $e]);
            $attempt->update(['status' => 'unknown', 'failure_reason' => 'Confirmation delayed. The existing request will be reconciled.']);
        }
        $this->syncOrder($attempt->order_id);
        return $attempt->fresh();
    }

    public function reconcile(RefundAttempt $attempt): void
    {
        if (in_array($attempt->status, ['completed', 'failed'], true)) return;
        if (!$attempt->gateway_id) {
            // An ambiguous POST is deliberately retained for manual reconciliation.
            return;
        }
        $url = $attempt->gateway === 'stripe'
            ? 'https://api.stripe.com/v1/refunds/' . $attempt->gateway_id
            : 'https://api.paystack.co/refund/' . $attempt->gateway_id;
        $response = Http::withToken(config('services.' . $attempt->gateway . '.secret'))->timeout(20)->get($url);
        if (!$response->successful()) return;
        $data = $attempt->gateway === 'stripe' ? $response->json() : $response->json('data');
        if ((string) ($data['id'] ?? '') !== (string) $attempt->gateway_id
            || (int) ($data['amount'] ?? -1) !== (int) $attempt->gateway_amount) return;
        $this->record($attempt, $data['status'] ?? 'pending');
        $this->syncOrder($attempt->order_id);
    }

    private function record(RefundAttempt $attempt, string $status): void
    {
        $completed = in_array(strtolower($status), Order::REFUND_TERMINAL_STATUSES, true);
        $failed = in_array($status, ['failed', 'canceled', 'cancelled'], true);
        $attempt->update([
            'gateway_status' => $status,
            'status' => $completed ? 'completed' : ($failed ? 'failed' : 'processing'),
            'completed_at' => $completed ? now() : null,
            'failure_reason' => $failed ? 'Gateway confirmed the refund failed.' : null,
        ]);
    }

    private function syncOrder(int $orderId): void
    {
        DB::transaction(function () use ($orderId) {
            $order = Order::whereKey($orderId)->lockForUpdate()->firstOrFail();
            $attempts = RefundAttempt::where('order_id', $orderId)->get();
            $completed = (float) $attempts->where('status', 'completed')->sum('amount');
            $pending = $attempts->whereIn('status', ['requested', 'processing', 'unknown']);
            $latest = $attempts->last();
            $fullRequest = $completed + (float) $pending->sum('amount') >= (float) $order->total;
            $status = $completed >= (float) $order->total ? 'refunded'
                : ($completed > 0 ? 'partially_refunded' : ($pending->isNotEmpty() ? ($fullRequest ? 'refund_pending' : $order->status) : 'refund_failed'));
            $wasCompleted = $order->status === 'refunded';
            $order->update([
                'status' => $status, 'refund_id' => $latest->gateway_id,
                'refund_status' => $latest->status, 'refund_amount' => $completed,
                'refunded_at' => $status === 'refunded' ? now() : null,
                'refund_failure_reason' => $latest->failure_reason,
            ]);
            if ($status === 'refunded' && !$wasCompleted) {
                app(OrderNotificationService::class)->send($order, \App\Mail\OrderRefunded::class, 'refund-completed');
            }
        });
    }
}
