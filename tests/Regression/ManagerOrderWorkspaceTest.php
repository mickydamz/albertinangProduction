<?php
namespace Tests\Regression;
use App\Models\{ManagedOrder,ManagerApprovalRequest,ManagerOrderActivity,Location,State,PickupPoint,OrderCancellation,OrderReturn};
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
class ManagerOrderWorkspaceTest extends RegressionTestCase {
 protected $manager; protected $other; protected $admin; protected $item; protected $point; protected $location;
 protected function setUp(): void {
  parent::setUp();$this->withoutMiddleware(ThrottleRequests::class);
  $this->manager=$this->customer('manager');$this->other=$this->customer('manager');$this->admin=$this->customer('admin');
  $this->item=$this->product();$this->item->update(['manager_id'=>$this->manager->id]);
  $s=State::create(['name'=>'Test state','is_active'=>true,'delivery_enabled'=>true]);
  $this->location=Location::create(['state_id'=>$s->id,'name'=>'Test area','is_active'=>true,'shipping_cost'=>500,'truck_shipping_cost'=>2500]);
  $this->point=PickupPoint::create(['location_id'=>$this->location->id,'name'=>'Test pickup','address'=>'Test road']);$this->item->locations()->sync([$this->location->id]);$this->item->pickupPoints()->sync([$this->point->id]);\Illuminate\Support\Facades\DB::table('origin_delivery_areas')->insert(['pickup_point_id'=>$this->point->id,'location_id'=>$this->location->id,'is_active'=>true]);$this->actingAs($this->manager);
 }
 private function payload(array $replace=[]): array {return array_replace_recursive(['customer_email'=>'fictional@example.test','delivery_mobile'=>'08012345678','payment_method'=>'cash','fulfillment_method'=>'pickup','dispatch_point_id'=>$this->point->id,'pickup_point_id'=>$this->point->id,'items'=>[['product_id'=>$this->item->id,'quantity'=>1]]],$replace);}
 private function manual(): ManagedOrder {$this->post('/manager/orders',$this->payload())->assertRedirect();return ManagedOrder::latest('id')->firstOrFail();}
 private function assigned(string $status='paid',string $method='pickup'): ManagedOrder {
  $base=$this->order($this->customer(),$status,'cash',$this->item);$o=ManagedOrder::findOrFail($base->id);
  $o->forceFill(['assigned_manager_id'=>$this->manager->id,'fulfillment_method'=>$method,'customer_email'=>'fictional@example.test'])->save();return $o;
 }
 public function test_dashboard_creation_and_manual_order(): void {
  $this->get('/manager/dashboard')->assertRedirect('/manager/orders');$this->get('/manager/orders')->assertOk()->assertSee('Order workspace');$this->get('/manager/orders/create')->assertOk();
  $o=$this->manual();$this->assertSame('pending',$o->status);$this->assertEquals(10000,$o->total);$this->assertSame('+2348012345678',$o->delivery_mobile);$this->assertFalse($o->paymentConfirmed());$this->assertEquals(10,$this->item->fresh()->stock);Mail::assertNothingSent();$this->get('/manager/orders/'.$o->id)->assertOk();
 }
 public function test_foreign_products_and_hidden_overrides_rejected(): void {
  $foreign=$this->product();$this->postJson('/manager/orders',$this->payload(['items'=>[['product_id'=>$foreign->id]]]))->assertStatus(422);
  foreach(['status'=>'paid','total'=>1,'shipping_cost'=>0,'assigned_manager_id'=>$this->other->id,'user_id'=>$this->admin->id] as $key=>$value)$this->postJson('/manager/orders',$this->payload([$key=>$value]))->assertStatus(422);
  $this->postJson('/manager/orders',$this->payload(['items'=>[['price'=>1]]]))->assertStatus(422);$this->assertSame(0,ManagedOrder::count());
 }
 public function test_numeric_stock_does_not_limit_orders_but_mobile_and_minimum_quantity_are_enforced(): void {
  $this->postJson('/manager/orders',$this->payload(['items'=>[['quantity'=>11]]]))->assertRedirect();$this->postJson('/manager/orders',$this->payload(['delivery_mobile'=>'+447123456789']))->assertStatus(422);
  $this->item->update(['moq'=>2]);$this->postJson('/manager/orders',$this->payload())->assertStatus(422);
 }
 public function test_delivery_and_installation_calculated(): void {
  $this->item->update(['installation_options'=>[['label'=>'Install','price'=>2000]]]);
  $this->post('/manager/orders',$this->payload(['fulfillment_method'=>'delivery','delivery_location_id'=>$this->location->id,'shipping_address'=>'Fictional street','items'=>[['installation_option'=>'Install']]]))->assertRedirect();
  $o=ManagedOrder::latest('id')->first();$this->assertEquals(12500,$o->total);$this->assertEquals(500,$o->shipping_cost);$this->assertSame('Install',$o->items->first()->installation_option);
 }
 public function test_cross_manager_access_is_denied_everywhere(): void {
  $o=$this->manual();$this->actingAs($this->other);$this->get('/manager/orders')->assertOk()->assertDontSee($o->order_number);
  foreach(['','/invoice','/invoice/download'] as $suffix)$this->get('/manager/orders/'.$o->id.$suffix)->assertNotFound();
  foreach(['status'=>['status'=>'processing','reason'=>'Try'],'details'=>['delivery_mobile'=>'08012345678','reason'=>'Try']] as $path=>$body)$this->patchJson('/manager/orders/'.$o->id.'/'.$path,$body)->assertNotFound();
  foreach(['notes'=>['note'=>'Try'],'requests'=>['type'=>'payment','reason'=>'Try'],'notify'=>[]] as $path=>$body)$this->postJson('/manager/orders/'.$o->id.'/'.$path,$body)->assertNotFound();
 }
 public function test_role_boundaries_and_unpaid_controls(): void {
  $o=$this->manual();$this->get('/admin/order-management')->assertForbidden();$this->patchJson('/manager/orders/'.$o->id.'/status',['status'=>'processing','reason'=>'Try','stock_checked'=>1])->assertStatus(422);
  $this->getJson('/manager/orders/'.$o->id.'/invoice')->assertStatus(422);$this->postJson('/manager/orders/'.$o->id.'/notify')->assertStatus(422);Mail::assertNothingSent();
  $this->actingAs($this->customer());$this->get('/manager/orders')->assertForbidden();$this->get('/admin/order-management')->assertForbidden();
 }
 public function test_payment_verification_admin_only_and_idempotent(): void {
  $o=$this->manual();$body=['type'=>'payment','reason'=>'Full receipt','payment_reference'=>'TEST-CASH-001'];$this->post('/manager/orders/'.$o->id.'/requests',$body)->assertRedirect();$this->postJson('/manager/orders/'.$o->id.'/requests',$body)->assertStatus(422);
  $a=ManagerApprovalRequest::first();$review=['decision'=>'approved','review_notes'=>'Independently verified'];$this->patchJson('/admin/order-management/approvals/'.$a->id,$review)->assertForbidden();
  $this->actingAs($this->admin);$this->get('/admin/order-management')->assertOk()->assertSee('TEST-CASH-001');$this->patch('/admin/order-management/approvals/'.$a->id,$review)->assertRedirect();$this->assertTrue($o->fresh()->paymentConfirmed());$this->assertSame('paid',$o->fresh()->status);$this->assertStringStartsWith('mgr_',$o->fresh()->reference);$this->assertSame('TEST-CASH-001',$o->fresh()->payment_id);$this->patchJson('/admin/order-management/approvals/'.$a->id,$review)->assertStatus(422);
 }
 public function test_changed_amount_cannot_be_approved(): void {
  $o=$this->manual();$this->post('/manager/orders/'.$o->id.'/requests',['type'=>'payment','reason'=>'Receipt','payment_reference'=>'TEST-001']);$o->update(['total'=>20000]);$this->actingAs($this->admin);$a=ManagerApprovalRequest::first();
  $this->patchJson('/admin/order-management/approvals/'.$a->id,['decision'=>'approved','review_notes'=>'Check'])->assertStatus(422);$this->assertSame('pending',$o->fresh()->status);
 }
 public function test_pickup_sequence_blocks_skips_and_financial_changes(): void {
  $o=$this->assigned();$url='/manager/orders/'.$o->id.'/status';foreach(['completed','refunded','cancelled','pending','shipped'] as $s)$this->patchJson($url,['status'=>$s,'reason'=>'Try'])->assertStatus(422);
  $this->patch($url,['status'=>'processing','reason'=>'Stock checked','stock_checked'=>1])->assertRedirect();$this->patch($url,['status'=>'ready_for_pickup','reason'=>'Packed'])->assertRedirect();$this->patch($url,['status'=>'completed','reason'=>'Customer collected'])->assertRedirect();$this->assertSame('completed',$o->fresh()->status);$this->assertNotNull($o->fresh()->received_at);
 }
 public function test_delivery_tracking_and_status_sequence(): void {
  $o=$this->assigned('processing','delivery');$url='/manager/orders/'.$o->id.'/status';$this->patchJson($url,['status'=>'shipped','reason'=>'Dispatch'])->assertStatus(422);
  $this->patch($url,['status'=>'shipped','reason'=>'Handover','courier_name'=>'Test courier','tracking_number'=>'TEST-TRACK'])->assertRedirect();$this->patch($url,['status'=>'delivered','reason'=>'Receipt confirmed'])->assertRedirect();$this->assertSame('delivered',$o->fresh()->status);$this->assertSame('TEST-TRACK',$o->fresh()->tracking_number);
 }
 public function test_zero_stock_allows_processing_but_pending_cancellation_blocks_it(): void {
  $o=$this->assigned();$this->item->update(['stock'=>0]);$url='/manager/orders/'.$o->id.'/status';$body=['status'=>'processing','reason'=>'Check','stock_checked'=>1];$this->patch($url,$body)->assertRedirect();$this->assertSame('processing',$o->fresh()->status);$o->refresh()->update(['status'=>'paid']);
  $this->post('/manager/orders/'.$o->id.'/requests',['type'=>'cancellation','reason'=>'Customer requested'])->assertRedirect();$this->patchJson($url,$body)->assertStatus(422);$this->assertSame('paid',$o->fresh()->status);$this->assertSame(1,OrderCancellation::count());
 }
 public function test_details_audited_and_post_dispatch_edits_blocked(): void {
  $o=$this->assigned('processing','delivery');$url='/manager/orders/'.$o->id.'/details';$body=['delivery_mobile'=>'08012345678','shipping_address'=>'Fictional corrected street','reason'=>'Customer correction'];$this->patch($url,$body)->assertRedirect();$this->assertSame('Fictional corrected street',$o->fresh()->shipping_address);$this->assertTrue(ManagerOrderActivity::where('action','details_updated')->exists());$o->update(['status'=>'shipped']);$this->patchJson($url,$body)->assertStatus(422);
 }
 public function test_explicit_assignment_and_product_ownership_both_grant_access(): void {
  $o=$this->assigned();$body=['assigned_manager_id'=>$this->other->id,'reason'=>'Shift handover'];$this->patchJson('/admin/order-management/'.$o->id.'/assign',$body)->assertForbidden();$this->actingAs($this->admin);$this->patch('/admin/order-management/'.$o->id.'/assign',$body)->assertRedirect();$this->actingAs($this->manager);$this->get('/manager/orders/'.$o->id)->assertOk();$this->actingAs($this->other);$this->get('/manager/orders/'.$o->id)->assertOk();
 }
 public function test_manager_can_set_own_product_price_directly(): void {
  $this->put('/manager/products/'.$this->item->id,['name'=>$this->item->name,'price'=>8000,'stock'=>9])->assertRedirect();$this->assertEquals(8000,$this->item->fresh()->price);$this->assertEquals(9,$this->item->fresh()->stock);$this->assertSame(0,ManagerApprovalRequest::where('type','product_price')->count());
 }
 public function test_return_request_without_refund(): void {
  $o=$this->assigned('completed');$this->post('/manager/orders/'.$o->id.'/requests',['type'=>'return','reason'=>'Fictional damaged packaging'])->assertRedirect();$this->assertSame('completed',$o->fresh()->status);$this->assertSame('pending',OrderReturn::first()->status);
 }
 public function test_invoice_notes_and_notification_throttle(): void {
  $o=$this->assigned();$this->get('/manager/orders/'.$o->id.'/invoice')->assertOk();$this->post('/manager/orders/'.$o->id.'/notes',['note'=>'Fictional internal note'])->assertRedirect();$this->get('/manager/orders/'.$o->id)->assertOk()->assertSee('Fictional internal note');$this->post('/manager/orders/'.$o->id.'/notify')->assertRedirect();$this->postJson('/manager/orders/'.$o->id.'/notify')->assertStatus(422);
 }
}
