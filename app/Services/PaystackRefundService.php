<?php
namespace App\Services;

use App\Models\{Order, OrderReturn, OrderCancellation};
use Illuminate\Support\Facades\{DB, Http, Mail, Log};
use Illuminate\Validation\ValidationException;
use App\Mail\{OrderRefunded, OrderRefundStatusUpdated};

class PaystackRefundService
{
    // Partial refunds are deliberately unsupported until a multiple-refund ledger/UI exists.
    public function validateAmount(array $input): void
    {
        foreach (['amount_ngn','refund_amount','amount'] as $field) {
            if (array_key_exists($field, $input)) {
                throw ValidationException::withMessages([$field=>'Amount overrides are unsupported. This action requests one full refund only.']);
            }
        }
    }

    public function initiate(Order $order, $request): array
    {
        $created = false;
        $row = DB::transaction(function () use ($order,$request,&$created) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $existing = DB::table('paystack_refunds')->where('order_id',$order->id)->first();
            if ($existing) return $existing;
            if ($request instanceof OrderReturn && !in_array($request->stage(), ['inspected','refund_requested'], true)) {
                throw ValidationException::withMessages(['refund'=>'Receive and inspect the returned goods before requesting a refund.']);
            }
            // Never issue another refund for a legacy record until it has been reconciled.
            foreach ([$order->cancellation,$order->return] as $legacy) {
                if ($legacy && ($legacy->refund_id || $legacy->refunded_at || $legacy->status === 'refunded')) {
                    throw ValidationException::withMessages(['refund'=>'An existing refund needs reconciliation; another refund was not requested.']);
                }
            }
            if (!$order->reference || $order->payment_method !== 'paystack' || $order->total <= 0 || in_array($order->status,['pending','refunded'],true)) {
                throw ValidationException::withMessages(['refund'=>'This order is not eligible for an automatic Paystack refund.']);
            }
            $id = DB::table('paystack_refunds')->insertGetId([
                'order_id'=>$order->id,'request_type'=>$request instanceof OrderReturn ? 'return' : 'cancellation',
                'request_id'=>$request->id,'transaction_reference'=>$order->reference,
                'amount'=>(int)round($order->total*100),'currency'=>'NGN','status'=>'requesting',
                'created_at'=>now(),'updated_at'=>now(),
            ]);
            $request->update(['status'=>'approved','refund_status'=>'requesting','refunded_at'=>null]);
            $created = true;
            return DB::table('paystack_refunds')->find($id);
        });
        if (!$created) return ['success'=>!in_array($row->status,['failed','unknown'],true),'message'=>'Refund already requested; current status: '.$row->status.'. No additional request was sent.'];
        try {
            $response = Http::withToken(config('services.paystack.secret'))->timeout(20)->post('https://api.paystack.co/refund',[
                'transaction'=>$row->transaction_reference,'amount'=>$row->amount,'currency'=>$row->currency,
                'customer_note'=>'Refund for order #'.$order->order_number,
            ]);
            $body = $response->json();
            if (!$response->successful() || !($body['status'] ?? false)) {
                // A server error can occur after Paystack accepted the request. Never automatically resend.
                $this->uncertain($row->id,$response->serverError() ? 'unknown' : 'failed','Paystack declined or could not confirm the refund request.');
                return ['success'=>false,'message'=>'Refund not confirmed. Admin review is required.'];
            }
            $data = $body['data'] ?? [];
            if (empty($data['id'])) throw new \RuntimeException('Refund response has no identifier.');
            // Some create responses omit transaction details. We know which request this response belongs to.
            $data += ['transaction_reference'=>$row->transaction_reference,'amount'=>$row->amount,'currency'=>$row->currency];
            if (!$this->apply($data,$row->id)) throw new \RuntimeException('Refund response could not be matched.');
            return ['success'=>true,'message'=>'Refund requested. Paystack status: '.($data['status'] ?? 'pending').'.'];
        } catch (\Throwable $e) {
            $this->uncertain($row->id,'unknown','Request outcome is uncertain; reconcile before retrying.');
            Log::warning('Paystack refund requires reconciliation',['refund_record'=>$row->id]);
            return ['success'=>false,'message'=>'Refund outcome is uncertain. No automatic retry will be sent.'];
        }
    }

    private function uncertain(int $id,string $status,string $message): void
    {
        $notify=DB::transaction(function () use ($id,$status,$message) {
            $row=DB::table('paystack_refunds')->where('id',$id)->lockForUpdate()->first();
            if ($row->status === 'processed' || $row->refund_id) return null;
            DB::table('paystack_refunds')->where('id',$id)->update(['status'=>$status,'last_error'=>$message,'updated_at'=>now()]);
            $this->request($row)->update(['refund_status'=>$status,'refunded_at'=>null]);
            return $row->status !== $status ? Order::findOrFail($row->order_id) : null;
        });
        if ($notify) $this->notify($notify,$status);
    }

    private function notify(Order $order, string $status): void
    {
        try {
            $email=$order->user?->email ?? $order->customer_email;
            if ($email) Mail::to($email)->send($status === 'processed' ? new OrderRefunded($order,$status) : new OrderRefundStatusUpdated($order,$status));
        } catch (\Throwable $e) {
            Log::warning('Refund status updated but notification failed',['order_id'=>$order->id]);
        }
    }

    private function request($row)
    {
        return ($row->request_type === 'return' ? OrderReturn::class : OrderCancellation::class)::findOrFail($row->request_id);
    }

    public function apply(array $data,?int $recordId=null): bool
    {
        $reference=$data['transaction_reference'] ?? $data['transaction']['reference'] ?? null;
        $status=$data['status'] ?? '';
        if (!in_array($status,['pending','processing','needs-attention','failed','processed'],true)) return false;
        $notify=null;
        $ok=DB::transaction(function () use ($data,$reference,$status,$recordId,&$notify) {
            $query=DB::table('paystack_refunds');
            $row=($recordId ? $query->where('id',$recordId) : $query->where('transaction_reference',$reference))->lockForUpdate()->first();
            if (!$row || !$reference || $reference !== $row->transaction_reference
                || !isset($data['amount']) || !is_numeric($data['amount']) || (float)$data['amount'] !== (float)$row->amount
                || ($data['currency'] ?? null) !== $row->currency
                || (isset($data['id']) && $row->refund_id && (string)$data['id'] !== (string)$row->refund_id)) {
                Log::warning('Unmatched Paystack refund update'); return false;
            }
            if ($row->status === 'processed') return true; // duplicates/out-of-order events cannot undo settlement
            if ($row->status === 'processing' && $status === 'pending') return true;
            if ($row->status === 'failed' && in_array($status,['pending','processing'],true)) return true;
            $done=$status === 'processed';
            DB::table('paystack_refunds')->where('id',$row->id)->update([
                'refund_id'=>$row->refund_id ?? ($data['id'] ?? null),'status'=>$status,
                'processed_at'=>$done ? now() : null,'last_error'=>null,'updated_at'=>now(),
            ]);
            $request=$this->request($row);
            $request->update(['status'=>$done ? 'refunded' : 'approved','refund_id'=>$row->refund_id ?? ($data['id'] ?? null),
                'refund_status'=>$status,'refunded_at'=>$done ? now() : null]);
            $order=Order::findOrFail($row->order_id);
            // Keep fulfilment history separate from the payment provider's refund status.
            if ($row->status !== $status) {
                DB::table('order_request_events')->insert(['order_id'=>$order->id,'request_type'=>$row->request_type,'request_id'=>$row->request_id,
                    'actor_id'=>null,'action'=>'refund_'.$status,'notes'=>'Payment provider status update','created_at'=>now(),'updated_at'=>now()]);
                $notify=$order;
            }
            return true;
        });
        if ($notify) $this->notify($notify,$status);
        return $ok;
    }

    // Paystack fetch/list responses may contain a numeric transaction ID, not its reference.
    private function verifiedTransaction($row): ?array
    {
        $response=Http::withToken(config('services.paystack.secret'))->timeout(20)->get('https://api.paystack.co/transaction/verify/'.urlencode($row->transaction_reference));
        $data=$response->json('data');
        if (!$response->successful() || !$response->json('status') || !is_array($data)
            || ($data['reference'] ?? null)!==$row->transaction_reference || ($data['status'] ?? null)!=='success'
            || ($data['currency'] ?? null)!==$row->currency || ($data['amount'] ?? 0)<$row->amount || empty($data['id'])) return null;
        return $data;
    }

    // Read-only gateway lookup; never creates or retries a refund.
    public function reconcile($row): bool
    {
        if ($row->refund_id) {
            $response=Http::withToken(config('services.paystack.secret'))->timeout(20)->get('https://api.paystack.co/refund/'.urlencode($row->refund_id));
            if (!$response->successful() || !$response->json('status')) return false;
            $data=$response->json('data') ?? [];
            if (!isset($data['transaction_reference']) && !is_array($data['transaction'] ?? null)) {
                $transaction=$this->verifiedTransaction($row);
                if (!$transaction || (string)($data['transaction'] ?? '')!==(string)$transaction['id']) return false;
                $data['transaction_reference']=$row->transaction_reference;
            }
            return $this->apply($data,$row->id);
        }
        $transaction=$this->verifiedTransaction($row);
        if (!$transaction) return false;
        $matches=[];
        for ($page=1; $page<=20; $page++) {
            $response=Http::withToken(config('services.paystack.secret'))->timeout(20)->get('https://api.paystack.co/refund',['transaction'=>$transaction['id'],'perPage'=>100,'page'=>$page]);
            if (!$response->successful() || !$response->json('status')) return false;
            $data=$response->json('data') ?? [];
            foreach ($data as $refund) {
                $ref=$refund['transaction_reference'] ?? (is_array($refund['transaction'] ?? null) ? ($refund['transaction']['reference'] ?? null) : null);
                $id=is_array($refund['transaction'] ?? null) ? ($refund['transaction']['id'] ?? null) : ($refund['transaction'] ?? null);
                if ($ref===$row->transaction_reference || ($id!==null && (string)$id===(string)$transaction['id'])) {
                    $refund['transaction_reference']=$row->transaction_reference;
                    $matches[]=$refund;
                }
            }
            if (count($matches)>1) return false;
            if (count($data)<100) return count($matches)===1 && $this->apply($matches[0],$row->id);
        }
        return false; // page limit reached: outcome remains uncertain
    }
}
