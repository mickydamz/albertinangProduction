<?php
namespace App\Http\Controllers;

use App\Models\{ManagedOrder, ManagerOrderActivity, OrderReturn, OrderCancellation};
use App\Support\ManagerOrderAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Mail};
use Illuminate\Validation\ValidationException;

class ManagerOrderReviewController extends Controller
{
    public function payment(Request $request, ManagedOrder $order)
    {
        ManagerOrderAccess::find($order->id);
        $data=$request->validate(['payment_reference'=>'required|string|max:255','reason'=>'required|string|max:1000','payment_received'=>'required|accepted']);
        $order=DB::transaction(function () use ($order,$data) {
            $o=ManagerOrderAccess::find($order->id,true);
            if ($o->status!=='pending' || $o->payment_verified_at || !in_array($o->payment_method,['cash','pos','bank_transfer'],true))
                throw ValidationException::withMessages(['payment'=>'Only pending offline payments can be confirmed here. Online payments require gateway verification.']);
            if ($o->cancellation()->where('status','pending')->exists()) throw ValidationException::withMessages(['payment'=>'Review the pending cancellation before confirming payment.']);
            $o->forceFill(['status'=>'paid','payment_verified_at'=>now(),'payment_verified_by'=>auth()->id(),'payment_id'=>$data['payment_reference']])->save();
            $o->approvals()->where('type','payment')->where('status','pending')->update(['status'=>'approved','reviewed_by'=>auth()->id(),'reviewed_at'=>now(),'review_notes'=>$data['reason']]);
            ManagerOrderActivity::record($o,'payment_confirmed','Full offline payment confirmed. Reference: '.$data['payment_reference'].' | '.$data['reason']);
            return $o;
        });
        $this->notify($order,\App\Mail\OrderConfirmation::class);
        return back()->with('success','Payment confirmed. This order is ready for processing.');
    }

    public function review(Request $request, ManagedOrder $order, string $type)
    {
        ManagerOrderAccess::find($order->id);
        abort_unless(in_array($type,['cancellation','return'],true),404);
        $data=$request->validate(['action'=>'required|in:approve,reject,receive,inspect,refund','reason'=>'required|string|max:1000','return_instructions'=>'nullable|string|max:2000','refund_reference'=>'nullable|string|max:255','refund_confirmed'=>'exclude_unless:action,refund|required|accepted']);
        app(\App\Services\PaystackRefundService::class)->validateAmount($request->all());
        $record=DB::transaction(function () use ($order,$type,$data) {
            $o=ManagerOrderAccess::find($order->id,true);
            $record=$o->$type()->lockForUpdate()->firstOrFail();
            $stage=$type==='return'?$record->stage():$record->status;
            $allowed=$type==='return' ? ['approve'=>['requested'],'reject'=>['requested','approved','received'],'receive'=>['approved'],'inspect'=>['received'],'refund'=>['inspected']] : ['approve'=>['pending'],'reject'=>['pending'],'refund'=>['approved']];
            if (!in_array($stage,$allowed[$data['action']]??[],true) || $record->refund_status)
                throw ValidationException::withMessages(['action'=>'This review action is not available at the current stage.']);
            if ($data['action']==='refund') return $record;
            if ($type==='return') {
                if ($data['action']==='approve' && !trim($data['return_instructions']??'')) throw ValidationException::withMessages(['return_instructions'=>'Provide instructions for returning the goods.']);
                $next=['approve'=>'approved','reject'=>'rejected','receive'=>'received','inspect'=>'inspected'][$data['action']];
                $record->update(['workflow_stage'=>$next,'status'=>$next==='rejected'?'rejected':'approved','admin_notes'=>$data['reason'],'return_instructions'=>$data['return_instructions']??$record->return_instructions,'reviewed_at'=>now()]);
            } else {
                if ($data['action']==='approve' && !$o->canCancel()) throw ValidationException::withMessages(['action'=>'This order has already been dispatched or completed and cannot be cancelled.']);
                $record->update(['status'=>$data['action']==='approve'?'approved':'rejected','admin_notes'=>$data['reason']]);
                if ($data['action']==='approve') $o->update(['status'=>'cancelled']);
            }
            ManagerOrderActivity::record($o,$type.'_reviewed',$data['action'].': '.$data['reason']);
            return $record;
        });
        if ($data['action']==='refund') {
            $result=app(\App\Services\ManagerOrderRefundService::class)->refund(ManagerOrderAccess::find($order->id),$record,$data['reason'],$data['refund_reference']??null);
            return back()->with($result['success']?'success':'error',$result['message']);
        }
        $order=$order->fresh();
        if ($type==='cancellation') $this->notify($order,$data['action']==='approve'?\App\Mail\OrderCancelled::class:\App\Mail\OrderCancellationRejected::class);
        elseif ($recipient=$order->user?->email ?? $order->customer_email) {
            try { Mail::to($recipient)->send(new \App\Mail\ReturnStageUpdated($record,$record->stageLabel(),$data['action']==='approve'?$record->return_instructions:$data['reason'])); } catch (\Throwable $e) { report($e); }
        }
        return back()->with('success',ucfirst($type).' review saved.');
    }

    private function notify(ManagedOrder $order, string $mail): void
    {
        if ($recipient=$order->user?->email ?? $order->customer_email) {
            try { Mail::to($recipient)->send(new $mail($order)); } catch (\Throwable $e) { report($e); }
        }
    }
}
