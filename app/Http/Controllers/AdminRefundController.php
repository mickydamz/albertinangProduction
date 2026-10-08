<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Services\PaystackRefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AdminRefundController extends Controller
{
    public function index(Request $request) {
        $request->validate(['status'=>'nullable|in:all,requesting,pending,processing,processed,failed,unknown,needs-attention']);
        $query=DB::table('paystack_refunds')->join('orders','orders.id','=','paystack_refunds.order_id')
            ->select('paystack_refunds.*','orders.order_number')->selectSub(DB::table('order_request_events')->selectRaw('MAX(created_at)')->whereColumn('order_request_events.order_id','paystack_refunds.order_id')->where('action','check_refund'),'last_checked_at');
        if($request->filled('status') && $request->status!=='all') $query->where('paystack_refunds.status',$request->status);
        $refunds=$query->orderByDesc('paystack_refunds.updated_at')->paginate(20)->withQueryString();
        return view('admin.orders.refunds',compact('refunds'));
    }
    public function check(Order $order, PaystackRefundService $service) {
        $row=DB::table('paystack_refunds')->where('order_id',$order->id)->first();
        abort_unless($row,404);
        $ok=$service->reconcile($row);
        DB::table('order_request_events')->insert(['order_id'=>$order->id,'request_type'=>$row->request_type,'request_id'=>$row->request_id,
            'actor_id'=>auth()->id(),'action'=>'check_refund','notes'=>$ok?'Provider check completed':'Provider confirmation unavailable', 'created_at'=>now(),'updated_at'=>now()]);
        return back()->with($ok?'success':'error',$ok?'Refund status checked. No new refund was requested.':'Refund still needs checking. No new refund was requested.');
    }
}
