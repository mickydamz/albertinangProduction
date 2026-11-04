<?php
namespace App\Services;

use App\Models\{ManagedOrder, ManagerOrderActivity};
use Illuminate\Support\Facades\{DB, Http, Mail};
use Illuminate\Validation\ValidationException;

class ManagerOrderRefundService
{
    public function refund(ManagedOrder $order, $record, string $notes, ?string $reference): array
    {
        app(PaystackRefundService::class)->validateAmount(request()->all());
        if ($order->payment_method === 'paystack') {
            ManagerOrderActivity::record($order,'refund_requested','Full Paystack refund requested: '.$notes);
            return app(PaystackRefundService::class)->initiate($order, $record);
        }

        $method = $order->payment_method;
        $request = DB::transaction(function () use ($order, $record, $notes, $reference, $method) {
            $order = ManagedOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $record = $record->newQuery()->lockForUpdate()->findOrFail($record->id);
            foreach ([$order->return, $order->cancellation] as $existing) {
                if ($existing && ($existing->refund_id || $existing->refund_status || $existing->refunded_at || $existing->status === 'refunded'))
                    throw ValidationException::withMessages(['refund'=>'A refund is already recorded or awaiting reconciliation. No additional refund was requested.']);
            }
            if (DB::table('paystack_refunds')->where('order_id',$order->id)->exists() || $order->status === 'refunded')
                throw ValidationException::withMessages(['refund'=>'This order already has a refund record.']);
            if ($method === 'stripe') {
                if (!str_starts_with($order->payment_id ?: $order->reference ?: '', 'pi_') || $order->total_usd <= 0)
                    throw ValidationException::withMessages(['refund'=>'A verified Stripe payment intent and original USD amount are required. Ask admin to reconcile this record.']);
            } elseif (!in_array($method,['cash','pos','bank_transfer','other'],true) || !trim($reference ?? '')) {
                throw ValidationException::withMessages(['refund'=>'Enter the reference for the offline refund already paid to the customer.']);
            }
            $record->update(['refund_status'=>'requesting','admin_notes'=>$notes]);
            ManagerOrderActivity::record($order,'refund_requested','Full refund requested: '.$notes);
            return $record;
        });

        if ($method !== 'stripe') {
            $this->apply($order, $request, 'manual:'.$reference, 'succeeded');
            return ['success'=>true,'message'=>'Offline refund recorded.'];
        }
        try {
            $response = Http::asForm()->withToken(config('services.stripe.secret'))->timeout(20)
                ->withHeaders(['Idempotency-Key'=>'manager-order-full-refund-'.$order->id])
                ->post('https://api.stripe.com/v1/refunds',[
                    'payment_intent'=>$order->payment_id ?: $order->reference,
                    'amount'=>(int)round($order->total_usd*100),
                ]);
            $data = $response->json();
            if (!$response->successful() || empty($data['id'])) {
                $request->update(['refund_status'=>$response->serverError() ? 'unknown' : 'failed']);
                return ['success'=>false,'message'=>'Stripe did not confirm the refund. The order was not marked refunded. Ask admin to reconcile before retrying.'];
            }
            $status = $data['status'] ?? 'pending';
            $this->apply($order,$request,$data['id'],$status);
            return ['success'=>$status==='succeeded','message'=>$status==='succeeded' ? 'Stripe refund confirmed.' : 'Stripe refund status: '.$status.'. The order will require reconciliation before it is marked refunded.'];
        } catch (\Throwable $error) {
            $request->update(['refund_status'=>'unknown']);
            return ['success'=>false,'message'=>'Refund outcome is uncertain. The order was not marked refunded; ask admin to reconcile.'];
        }
    }

    private function apply(ManagedOrder $order, $record, string $id, string $status): void
    {
        DB::transaction(function () use ($order,$record,$id,$status) {
            $o=ManagedOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $record->update(['refund_id'=>$id,'refund_status'=>$status,'refunded_at'=>$status==='succeeded'?now():null,'status'=>$status==='succeeded'?'refunded':'approved']);
            if ($status==='succeeded') $o->update(['status'=>'refunded']);
            ManagerOrderActivity::record($o,'refund_updated','Refund '.$id.': '.$status);
        });
        if ($status==='succeeded' && ($recipient=$order->user?->email ?? $order->customer_email)) {
            try { Mail::to($recipient)->send(new \App\Mail\OrderRefunded($order->fresh())); } catch (\Throwable $e) { report($e); }
        }
    }
}
