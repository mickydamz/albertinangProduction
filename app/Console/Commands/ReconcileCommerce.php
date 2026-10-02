<?php

namespace App\Console\Commands;

use App\Models\PendingCheckout;
use App\Models\RefundAttempt;
use App\Services\PaystackOrderService;
use App\Services\StripeOrderService;
use App\Services\RefundService;
use App\Services\OrderNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReconcileCommerce extends Command
{
    protected $signature = 'commerce:reconcile {--reference= : Recover a specific checkout}';
    protected $description = 'Recover payments without orders, reconcile refunds and retry unsent order emails';

    public function handle(): int
    {
        PendingCheckout::whereNull('fulfilled_at')
            ->when($this->option('reference'), fn ($q, $ref) => $q->where('reference', $ref))
            ->where('created_at', '>=', now()->subDays(30))
            ->chunkById(100, function ($checkouts) {
                foreach ($checkouts as $checkout) {
                    try {
                        if ($checkout->gateway === 'stripe' && !$checkout->payment_intent_id) continue;
                        $result = $checkout->gateway === 'stripe'
                            ? app(StripeOrderService::class)->fulfil($checkout->reference, $checkout->payment_intent_id, null)
                            : app(PaystackOrderService::class)->fulfil($checkout->reference, null);
                        $checkout->update(['recovery_error' => $result['success'] ? null : $result['message']]);
                    } catch (\Throwable $e) {
                        Log::error('Checkout reconciliation failed', ['reference' => $checkout->reference, 'exception' => $e]);
                    }
                }
            });
        RefundAttempt::whereIn('status', ['processing', 'unknown'])->chunkById(100, function ($attempts) {
            foreach ($attempts as $attempt) {
                try { app(RefundService::class)->reconcile($attempt); }
                catch (\Throwable $e) { Log::error('Refund reconciliation failed', ['reference' => $attempt->request_key, 'exception' => $e]); }
            }
        });
        DB::table('order_notifications')->whereNull('sent_at')->orderBy('id')->chunkById(100, function ($rows) {
            foreach ($rows as $row) app(OrderNotificationService::class)->deliver($row->id);
        });
        $this->info('Reconciliation finished. Unknown refund requests require gateway review before resubmission.');
        return self::SUCCESS;
    }
}
