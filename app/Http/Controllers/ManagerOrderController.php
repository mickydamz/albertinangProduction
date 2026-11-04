<?php
namespace App\Http\Controllers;
use App\Models\{ManagedOrder, ManagerOrderActivity, ManagerApprovalRequest, Product, User, Location, PickupPoint, Setting, OrderCancellation, OrderReturn};
use App\Support\DeliveryMobile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Mail, Log};
use Illuminate\Validation\ValidationException;

class ManagerOrderController extends Controller {
 public function __construct() { $this->middleware(['auth','manager']); }
 private function orders() {
  return \App\Support\ManagerOrderAccess::query();
 }
 private function owned(ManagedOrder $order, bool $lock=false): ManagedOrder {
  return $this->orders()->when($lock,fn($q)=>$q->lockForUpdate())->findOrFail($order->id);
 }
 private function fail(string $field,string $message): void { throw ValidationException::withMessages([$field=>$message]); }
 public function index(Request $r) {
  $q=$this->orders(); $stats=[];
  foreach(['pending','paid','processing','ready_for_pickup','shipped','delivered','completed'] as $s) $stats[$s]=(clone $q)->where('status',$s)->count();
  $search=substr((string)$r->query('search',''),0,100); $status=$r->query('status');
  $orders=$q->when($search,fn($q)=>$q->where(fn($q)=>$q->where('order_number','like',"%$search%")
    ->orWhere('customer_email','like',"%$search%")->orWhere('delivery_mobile','like',"%$search%")))
    ->when($status,fn($q)=>$q->where('status',$status))->latest()->paginate(15)->withQueryString();
  return view('manager.orders.index',compact('orders','stats','search','status'));
 }
 public function create() {
  $products=Product::where('manager_id',auth()->id())->where('is_active',true)->with(['category','Subcategory'])->orderBy('name')->get();
  $points=PickupPoint::with('location','state')->orderBy('name')->get();
  $locations=Location::where('is_active',true)->with('state')->orderBy('name')->get();
  return view('manager.orders.create',compact('products','points','locations'));
 }
 public function store(Request $r) {
  $r->merge(['delivery_mobile'=>DeliveryMobile::normalize($r->input('delivery_mobile'))]);
  $v=$r->validate([
   'customer_email'=>'required|email|max:255','delivery_mobile'=>'required|string|max:16',
   'payment_method'=>'required|in:cash,pos,bank_transfer', 'fulfillment_method'=>'required|in:pickup,delivery',
   'dispatch_point_id'=>'nullable|integer|exists:pickup_points,id',
   'pickup_point_id'=>'required_if:fulfillment_method,pickup|nullable|integer|exists:pickup_points,id',
   'delivery_location_id'=>'required_if:fulfillment_method,delivery|nullable|integer|exists:locations,id',
   'shipping_address'=>'required_if:fulfillment_method,delivery|nullable|string|max:500',
   'notes'=>'nullable|string|max:1000', 'items'=>'required|array|min:1|max:50',
   'items.*.product_id'=>'required|integer|distinct','items.*.quantity'=>'required|integer|min:1|max:1000',
   'items.*.installation_option'=>'nullable|string|max:255',
   'price'=>'prohibited','total'=>'prohibited','shipping_cost'=>'prohibited','coupon_discount_ngn'=>'prohibited',
   'status'=>'prohibited','assigned_manager_id'=>'prohibited','user_id'=>'prohibited',
   'items.*.price'=>'prohibited',
  ],['delivery_mobile.required'=>'Enter a valid Nigerian mobile number.']);
  $order=DB::transaction(function() use($v) {
   $items=[]; $subtotal=0; $weight=0; $truck=false;
   $products=Product::where('manager_id',auth()->id())->where('is_active',true)->with(['category','Subcategory'])
    ->whereIn('id',array_column($v['items'],'product_id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
   foreach($v['items'] as $row) {
    $p=$products->get($row['product_id']); if(!$p) $this->fail('items','Only active products assigned to you can be ordered.');
    $qty=(int)$row['quantity'];
    if($qty < max(1,(int)$p->moq)) $this->fail('items',"Minimum quantity for {$p->name} is {$p->moq}.");
    $extra=0; $option=$row['installation_option']??null;
    if($option) {
     $match=collect($p->installation_options??[])->first(fn($o)=>is_array($o)&&($o['label']??null)===$option);
     if(!$match) $this->fail('items','Invalid installation option.');
     $extra=(int)round((float)($match['price']??0)*100);
    }
    $price=(int)round((float)$p->sell_price*100)+$extra; if($price<0) $this->fail('items','Invalid catalogue price.');
    $subtotal+=$price*$qty; $truck=$truck||$p->requires_truck;
    $weight+=(float)($p->weight_kg??$p->Subcategory?->estimated_weight_kg??$p->category?->estimated_weight_kg??5)*$qty;
    $items[]=['product_id'=>$p->id,'name'=>$p->name,'sku'=>$p->sku,'quantity'=>$qty,'price'=>$price/100,
      'installation_option'=>$option,'installation_extra_ngn'=>$extra/100];
   }
   $truck=$truck||$weight>=(float)Setting::get('truck_weight_threshold_kg',30)||$subtotal/100>=(float)Setting::get('truck_order_value_threshold_ngn',1000000);
   $originId=$v['fulfillment_method']==='pickup' ? $v['pickup_point_id'] : ($v['dispatch_point_id']??null);
   $origin=$originId ? \App\Services\ShoppingLocation::point((int)$originId) : null;
   if(!$origin)$this->fail('dispatch_point_id','Choose an active dispatch store.');
   $f=\App\Services\ShoppingLocation::resolve(['method'=>$v['fulfillment_method'],'pickup_point_id'=>$v['pickup_point_id']??null,'delivery_location_id'=>$v['delivery_location_id']??null,'shipping_address'=>$v['shipping_address']??null],array_column($v['items'],'product_id'),$truck,$origin);
   \App\Services\CollectionTransfer::applyItems($items,$f);
   $data=$f;unset($data['method'],$data['delivery_fee_ngn'],$data['area_delivery_fee_ngn']);$data['fulfillment_method']=$v['fulfillment_method'];$data['shipping_cost']=$f['delivery_fee_ngn'];
   $u=User::where('email',$v['customer_email'])->whereIn('role',['user','customer'])->first();
   $order=new ManagedOrder(); $order->forceFill($data+[
    'user_id'=>$u?->id,'customer_email'=>$v['customer_email'],'delivery_mobile'=>$v['delivery_mobile'],
    'payment_method'=>$v['payment_method'],'reference'=>'mgr_'.\Illuminate\Support\Str::uuid(),'status'=>'pending','total'=>($subtotal+(int)round($data['shipping_cost']*100))/100,
    'total_usd'=>0,'assigned_manager_id'=>auth()->id(),'created_by_staff_id'=>auth()->id(),'manager_created'=>true,
   ])->save();
   $order->items()->createMany($items);
   ManagerOrderActivity::record($order,'created','Manual order created; payment awaiting staff verification. Products follow admin visibility and location assignments.');
   if(!empty($v['notes'])) ManagerOrderActivity::record($order,'note',$v['notes']);
   return $order;
  });
  // No paid-order confirmation is sent for an unpaid manual order.
  return redirect()->route('manager.orders.show',$order)->with('success','Order created. Confirm the full offline payment when received, then process the order.');
 }
 public function show(ManagedOrder $order) {
  $order=$this->owned($order)->load(['items','activities','approvals','manager','return','cancellation']);
  $points=PickupPoint::with('location','state')->get();
  return view('manager.orders.show',compact('order','points'));
 }
 public function details(Request $r,ManagedOrder $order) {
  $this->owned($order);
  $r->merge(['delivery_mobile'=>DeliveryMobile::normalize($r->input('delivery_mobile'))]);
  $v=$r->validate(['delivery_mobile'=>'required|string|max:16','shipping_address'=>'nullable|string|max:500',
    'pickup_point_id'=>'nullable|integer|exists:pickup_points,id','reason'=>'required|string|max:500',
    'customer_email'=>'prohibited','total'=>'prohibited','status'=>'prohibited','payment_method'=>'prohibited']);
  DB::transaction(function()use($v,$order) {
   $o=$this->owned($order,true);
   if(!in_array($o->status,['pending','paid','processing','ready_for_pickup'])) $this->fail('order','Details can only be corrected before dispatch or collection.');
   $data=['delivery_mobile'=>$v['delivery_mobile']];
   if($o->fulfillment_method==='delivery') {
    if(empty($v['shipping_address'])) $this->fail('shipping_address','A delivery address is required.');
    $data['shipping_address']=$v['shipping_address'];
   } elseif(!empty($v['pickup_point_id'])) {
    $p=PickupPoint::with('location','state')->findOrFail($v['pickup_point_id']);
    if($o->dispatch_point_id && (int)$o->dispatch_point_id !== (int)$p->id) $this->fail('pickup_point_id','This order is tied to its original store. Ask admin to review an origin change.');
    if(!$p->location?->is_active) $this->fail('pickup_point_id','This pickup point is unavailable.');
    $data+=['pickup_point_id'=>$p->id,'pickup_point_name'=>$p->name,'pickup_point_address'=>$p->address,'pickup_location_id'=>$p->location_id,'pickup_location'=>$p->state?->name];
   }
   $before=$o->only(array_keys($data)); $o->forceFill($data)->save();
   ManagerOrderActivity::record($o,'details_updated',$v['reason'].' | Before: '.json_encode($before).' | After: '.json_encode($data));
  });
  return back()->with('success','Order details updated. Customer email changes require admin review.');
 }
 public function status(Request $r,ManagedOrder $order) {
  $this->owned($order);
  $v=$r->validate(['status'=>'required|string','reason'=>'required|string|max:500','courier_name'=>'nullable|string|max:120','tracking_number'=>'nullable|string|max:120','stock_checked'=>'sometimes|accepted']);
  $o=DB::transaction(function()use($v,$order) {
   $o=$this->owned($order,true);
   if(!in_array($v['status'],$o->nextManagerStatuses(),true)) $this->fail('status','This status change is not allowed. Confirm payment and follow the fulfilment sequence.');
   if($o->cancellation()->where('status','pending')->exists()) $this->fail('status','A pending cancellation must be reviewed before fulfilment.');
   $data=['status'=>$v['status']];
   if($v['status']==='shipped') {
    if(empty($v['courier_name'])||empty($v['tracking_number'])) $this->fail('tracking_number','Courier and tracking/dispatch reference are required.');
    $data+=['courier_name'=>$v['courier_name'],'tracking_number'=>$v['tracking_number']];
   }
   $old=$o->status; $o->forceFill($data)->save();
   ManagerOrderActivity::record($o,'status_changed',"$old → {$v['status']}: {$v['reason']}");
   return $o;
  });
  $this->sendStatusMail($o);
  return back()->with('success','Order status updated.');
 }
 public function note(Request $r,ManagedOrder $order) {
  $o=$this->owned($order); $v=$r->validate(['note'=>'required|string|max:2000']);
  ManagerOrderActivity::record($o,'note',$v['note']); return back()->with('success','Internal note added.');
 }
 public function requestApproval(Request $r,ManagedOrder $order) {
  $this->owned($order); $v=$r->validate(['type'=>'required|in:payment,cancellation,return','reason'=>'required|string|max:2000','payment_reference'=>'nullable|string|max:255']);
  DB::transaction(function()use($v,$order) {
   $o=$this->owned($order,true);
   if($v['type']==='payment') {
    if(!$o->manager_created||$o->status!=='pending'||$o->paymentConfirmed()) $this->fail('type','Only unpaid manager-created orders can be submitted for offline payment review.');
    if(empty($v['payment_reference'])) $this->fail('payment_reference','Enter the receipt or transfer reference.');
    if($o->approvals()->where('type','payment')->where('status','pending')->exists()) $this->fail('type','Payment review is already pending.');
    ManagerApprovalRequest::create(['order_id'=>$o->id,'requested_by'=>auth()->id(),'type'=>'payment','reason'=>$v['reason'],
     'payload'=>['payment_method'=>$o->payment_method,'payment_reference'=>$v['payment_reference'],'amount'=>$o->total]]);
   } elseif($v['type']==='cancellation') {
    if(!$o->canCancel()||$o->cancellation()->exists()) $this->fail('type','This order cannot accept another cancellation request.');
    OrderCancellation::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'status'=>'pending','reason'=>$v['reason']]);
   } else {
    if(!$o->canReturn()||$o->return()->exists()) $this->fail('type','This order is not eligible for another return request.');
    OrderReturn::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'status'=>'pending','reason'=>$v['reason'],'workflow_stage'=>'requested']);
   }
   ManagerOrderActivity::record($o,'approval_requested',ucfirst($v['type']).' logged for review: '.$v['reason']);
  });
  return back()->with('success','Request logged. You can review it on this order.');
 }
 public function notify(ManagedOrder $order) {
  $o=$this->owned($order);
  if(!$o->paymentConfirmed()) $this->fail('order','A paid-order notification cannot be sent before payment confirmation.');
  $recent=$o->activities()->where('action','notification_sent')->where('created_at','>',now()->subMinutes(5))->exists();
  if($recent) $this->fail('order','Please wait five minutes before resending an update.');
  if(!$this->sendStatusMail($o)) return back()->with('error','The update could not be sent. Ask admin to check email delivery.');
  return back()->with('success','Current order update sent.');
 }
 private function sendStatusMail(ManagedOrder $o): bool {
  $class=match($o->status) {
   'paid'=>\App\Mail\OrderConfirmation::class,'processing'=>\App\Mail\OrderProcessing::class,
   'ready_for_pickup'=>\App\Mail\OrderReadyForPickup::class,'shipped'=>\App\Mail\OrderShipped::class,
   'delivered'=>\App\Mail\OrderDelivered::class,'completed'=>\App\Mail\OrderCompleted::class,default=>null
  };
  $recipient=$o->user?->email??$o->customer_email;
  if(!$class||!$recipient) return false;
  try { Mail::to($recipient)->send(new $class($o)); ManagerOrderActivity::record($o,'notification_sent','Standard '.$o->status.' email sent.'); return true; }
  catch(\Throwable $e) { Log::error('Manager order notification failed for order '.$o->id.': '.$e->getMessage()); ManagerOrderActivity::record($o,'notification_failed','Email delivery failed; contact admin.'); return false; }
 }
 public function invoice(ManagedOrder $order) {
  $order=$this->owned($order)->load(['items','user']);
  if(!$order->paymentConfirmed()) $this->fail('order','Invoices are available after payment confirmation.');
  $downloadUrl=route('manager.orders.invoice.download',$order);
  return view('sims.invoice',array_merge(compact('order','downloadUrl'),AdminInvoiceSettingsController::templateVars($order->fulfillment_method)));
 }
 public function downloadInvoice(ManagedOrder $order) {
  $order=$this->owned($order)->load(['items','user']);
  if(!$order->paymentConfirmed()) $this->fail('order','Invoices are available after payment confirmation.');
  return \Barryvdh\DomPDF\Facade\Pdf::loadView('sims.invoice-pdf',array_merge(compact('order'),AdminInvoiceSettingsController::templateVars($order->fulfillment_method)))
    ->setPaper('a4','portrait')->download('invoice-'.$order->order_number.'.pdf');
 }
}
