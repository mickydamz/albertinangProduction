<?php
namespace App\Http\Controllers;
use App\Models\{ManagedOrder, ManagerOrderActivity, ManagerApprovalRequest, User, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class AdminManagerOrdersController extends Controller {
 public function __construct() { $this->middleware(['auth','role:admin']); }
 public function index(Request $r) {
  $search=substr((string)$r->query('search',''),0,100);
  $orders=ManagedOrder::with('manager')->when($search,fn($q)=>$q->where('order_number','like',"%$search%"))
   ->when($r->boolean('unassigned'),fn($q)=>$q->whereNull('assigned_manager_id'))->latest()->paginate(20)->withQueryString();
  $managers=User::where('role','manager')->orderBy('name')->get(['id','name','email']);
  $approvals=ManagerApprovalRequest::with(['order','product','requester'])->where('status','pending')->where('type','payment')->oldest()->get();
  return view('admin.order-management.index',compact('orders','managers','approvals','search'));
 }
 public function assign(Request $r,ManagedOrder $order) {
  $v=$r->validate(['assigned_manager_id'=>'nullable|integer|exists:users,id','reason'=>'required|string|max:500']);
  DB::transaction(function()use($v,$order) {
   $o=ManagedOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();
   $manager=empty($v['assigned_manager_id'])?null:User::where('role','manager')->findOrFail($v['assigned_manager_id']);
   $old=$o->assigned_manager_id; $o->forceFill(['assigned_manager_id'=>$manager?->id])->save();
   ManagerOrderActivity::record($o,'assigned','Manager '.($old??'unassigned').' → '.($manager?->id??'unassigned').': '.$v['reason']);
  });
  return back()->with('success','Order assignment updated.');
 }
 public function review(Request $r,ManagerApprovalRequest $approval) {
  $v=$r->validate(['decision'=>'required|in:approved,rejected','review_notes'=>'required|string|max:1000']);
  DB::transaction(function()use($v,$approval) {
   $a=ManagerApprovalRequest::whereKey($approval->id)->lockForUpdate()->firstOrFail();
   if($a->status!=='pending') throw ValidationException::withMessages(['decision'=>'This request has already been reviewed.']);
   $o=$a->order_id?ManagedOrder::whereKey($a->order_id)->lockForUpdate()->firstOrFail():null;
   if($v['decision']==='approved') {
    if($a->type==='payment') {
     if(!$o->manager_created||$o->status!=='pending'||$o->payment_verified_at||!in_array($o->payment_method,['cash','pos','bank_transfer'],true)
       ||(int)round($o->total*100)!==(int)round(($a->payload['amount']??-1)*100)||$o->payment_method!==($a->payload['payment_method']??null))
      throw ValidationException::withMessages(['decision'=>'The order changed or is not eligible for offline payment confirmation.']);
     if($o->cancellation()->where('status','pending')->exists()) throw ValidationException::withMessages(['decision'=>'Review the pending cancellation first.']);
     $o->forceFill(['status'=>'paid','payment_verified_at'=>now(),'payment_verified_by'=>auth()->id(),'payment_id'=>$a->payload['payment_reference']])->save();
    } elseif($a->type==='product_price') {
     throw ValidationException::withMessages(['decision'=>'Managers now set product prices directly. This old price request can only be rejected.']);
    } else throw ValidationException::withMessages(['decision'=>'Unsupported approval type.']);
   }
   $a->update(['status'=>$v['decision'],'review_notes'=>$v['review_notes'],'reviewed_by'=>auth()->id(),'reviewed_at'=>now()]);
   if($o) ManagerOrderActivity::record($o,'approval_reviewed',ucfirst($a->type).' '.$v['decision'].': '.$v['review_notes']);
  });
  return back()->with('success','Request reviewed.');
 }
}
