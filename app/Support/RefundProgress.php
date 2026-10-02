<?php
namespace App\Support;

use App\Models\Order;

class RefundProgress
{
    public static function forOrder(Order $order): ?array
    {
        $request = collect([$order->cancellation, $order->return])->filter(fn ($r) => $r && $r->refund_status)->sortByDesc('updated_at')->first();
        if (!$request) {
            if ($order->status !== 'refunded') return null;
            return self::forStatus($order->payment_method === 'paystack' ? 'unknown' : 'processed');
        }
        return self::forStatus($request->refund_status);
    }

    public static function forStatus(string $status): array
    {
        [$label, $message] = match ($status) {
            'requesting' => ['Refund requested', 'We are submitting your refund request to Paystack. Completion has not yet been confirmed.'],
            'pending' => ['Refund requested', 'Paystack has accepted your refund request and is waiting for the processor. Your funds have not yet been confirmed as returned.'],
            'processing' => ['Refund processing', 'The payment processor is processing your refund. We will notify you when Paystack confirms it has been processed.'],
            'processed', 'succeeded' => ['Refund processed', 'Your payment provider has confirmed the refund was processed. Your bank may take up to 10 business days to credit your account. Receipt of funds is not yet confirmed by this store.'],
            'needs-attention' => ['Refund needs attention', 'Paystack needs additional details to continue your refund. Our team will contact you if information is required.'],
            'failed' => ['Refund failed', 'The payment provider could not process your refund. Our team needs to resolve this; funds have not been confirmed as returned.'],
            default => ['Refund awaiting confirmation', 'We are checking the refund status with the payment provider. Completion and receipt of funds are not yet confirmed.'],
        };
        return ['status'=>$status, 'label'=>$label, 'message'=>$message, 'processed'=>in_array($status, ['processed','succeeded'], true)];
    }
}
