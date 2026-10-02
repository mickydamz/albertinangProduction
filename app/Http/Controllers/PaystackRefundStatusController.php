<?php
namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Support\Facades\{DB,Http};
class PaystackRefundStatusController extends Controller
{
    public function readiness()
    {
        $secret=config('services.paystack.secret');
        if (!is_string($secret) || !str_starts_with($secret,'sk_test_')) return response()->json(['message'=>'Paystack test-mode key is required.'],409);
        if (!\Illuminate\Support\Facades\Schema::hasTable('paystack_refunds')) return response()->json(['message'=>'Deploy the refund migration first.'],503);
        return response()->json(['test_mode'=>true,'refund_status_endpoint'=>true])->header('Cache-Control','no-store');
    }

    /** Read-only sandbox verification. Secret keys and customer details never leave the server. */
    public function show(Order $order)
    {
        $secret=config('services.paystack.secret');
        if (!is_string($secret) || !str_starts_with($secret,'sk_test_')) {
            return response()->json(['message'=>'Paystack test-mode key is required.'],409);
        }
        $row=DB::table('paystack_refunds')->where('order_id',$order->id)->first();
        if (!$row || !$row->refund_id) return response()->json(['message'=>'No confirmed gateway refund identifier.','order_id'=>$order->id,'test_mode'=>true],202);
        try {
            $response=Http::withToken($secret)->timeout(20)->get('https://api.paystack.co/refund/'.urlencode($row->refund_id));
            $data=$response->json('data');
            if (!$response->successful() || !$response->json('status') || !is_array($data)) return response()->json(['message'=>'Paystack refund lookup failed.'],502);
            $reference=$data['transaction_reference'] ?? (is_array($data['transaction'] ?? null) ? ($data['transaction']['reference'] ?? null) : null);
            if (!$reference && isset($data['transaction']) && !is_array($data['transaction'])) {
                $transaction=Http::withToken($secret)->timeout(20)->get('https://api.paystack.co/transaction/verify/'.urlencode($row->transaction_reference));
                if (!$transaction->successful() || !$transaction->json('status') || $transaction->json('data.status')!=='success'
                    || (string)$transaction->json('data.id')!==(string)$data['transaction']) return response()->json(['message'=>'Refund transaction could not be verified.'],502);
                $reference=$transaction->json('data.reference');
            }
            if (($data['domain'] ?? null)!=='test' || $reference!==$row->transaction_reference
                || (string)($data['id'] ?? '')!==(string)$row->refund_id
                || !isset($data['amount']) || (float)$data['amount']!==(float)$row->amount || ($data['currency'] ?? null)!==$row->currency) {
                return response()->json(['message'=>'Gateway refund identity, environment or amount mismatch.'],409);
            }
            return response()->json([
                'test_mode'=>true,'order_id'=>$order->id,'reference'=>$reference,
                'gateway'=>['refund_id'=>(string)$data['id'],'status'=>$data['status'] ?? null,'amount'=>$data['amount'],'currency'=>$data['currency'],'domain'=>$data['domain']],
                'application'=>['refund_status'=>$row->status,'processed_at'=>$row->processed_at,'order_status'=>$order->fresh()->status],
            ])->header('Cache-Control','no-store');
        } catch (\Throwable $e) { return response()->json(['message'=>'Paystack lookup is temporarily unavailable.'],503); }
    }
}
