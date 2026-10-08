<?php
namespace App\Services;
use App\Models\OrderReturn;
use App\Mail\ReturnStageUpdated;
use Illuminate\Support\Facades\{DB, Mail};
use Illuminate\Validation\ValidationException;
class ReturnWorkflowService
{
    public function transition(OrderReturn $return, string $action, ?string $notes, ?string $instructions): array
    {
        $notify=false;
        $return=DB::transaction(function () use ($return,$action,$notes,$instructions,&$notify) {
            $r=OrderReturn::whereKey($return->id)->lockForUpdate()->firstOrFail();
            $stage=$r->stage();
            $allowed=['approve'=>['requested'],'reject'=>['requested','approved','received'],
                'receive'=>['approved'],'inspect'=>['received'],'refund'=>['inspected'],'refund_without_return'=>['requested','approved','received','inspected']];
            if (!isset($allowed[$action]) || !in_array($stage,$allowed[$action],true) || $r->refund_status) {
                throw ValidationException::withMessages(['action'=>'This action is not available at the current return stage.']);
            }
            if ($action==='approve' && !trim($instructions ?? '')) throw ValidationException::withMessages(['return_instructions'=>'Provide instructions for returning the goods.']);
            if (in_array($action,['reject','inspect','refund_without_return'],true) && !trim($notes ?? '')) throw ValidationException::withMessages(['admin_notes'=>'Record the reason or inspection outcome.']);
            $next=['approve'=>'approved','reject'=>'rejected','receive'=>'received','inspect'=>'inspected','refund'=>'refund_requested','refund_without_return'=>'refund_requested'][$action];
            $r->update(['workflow_stage'=>$next,'status'=>$action==='reject'?'rejected':'approved',
                'admin_notes'=>$notes,'return_instructions'=>$instructions ?: $r->return_instructions,'reviewed_at'=>now()]);
            DB::table('order_request_events')->insert(['order_id'=>$r->order_id,'request_type'=>'return','request_id'=>$r->id,
                'actor_id'=>auth()->id(),'action'=>$action,'notes'=>$notes,'created_at'=>now(),'updated_at'=>now()]);
            $notify=!in_array($action,['refund','refund_without_return'],true);
            return $r;
        });
        if (in_array($action,['refund','refund_without_return'],true)) return app(PaystackRefundService::class)->initiate($return->order,$return);
        if ($notify) Mail::to($return->user->email)->queue(new ReturnStageUpdated($return,$return->stageLabel(),$action==='approve'?$return->return_instructions:$notes));
        return ['success'=>true,'message'=>$return->stageLabel().'.'];
    }
}
