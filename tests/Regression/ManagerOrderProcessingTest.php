<?php
namespace Tests\Regression;
use App\Models\{ManagedOrder,OrderCancellation,OrderReturn,ManagerOrderActivity};
use Illuminate\Support\Facades\{Http,Mail};

class ManagerOrderProcessingTest extends RegressionTestCase {
 private $manager; private $other; private $mine; private $foreign;
 protected function setUp(): void {
  parent::setUp(); $this->manager=$this->customer('manager'); $this->other=$this->customer('manager');
  $this->mine=$this->product(); $this->mine->forceFill(['manager_id'=>$this->manager->id,'sku'=>'MY-UNIQUE'])->save();
  $this->foreign=$this->product(); $this->foreign->update(['manager_id'=>$this->other->id]); $this->actingAs($this->manager);
 }
 private function sale(string $status='paid',string $payment='cash'): ManagedOrder {
  $base=$this->order($this->customer(),$status,$payment,$this->mine);
  $o=ManagedOrder::findOrFail($base->id); $o->update(['fulfillment_method'=>'pickup','customer_email'=>'fictional@example.test']); return $o;
 }
 private function review($o,string $type,string $action,array $extra=[]) {
  return $this->postJson('/manager/orders/'.$o->id.'/reviews/'.$type,array_merge(['action'=>$action,'reason'=>'Fictional review findings'],$extra));
 }
 public function test_unassigned_and_mixed_orders_are_accessible_to_each_product_manager(): void {
  $o=$this->sale(); $o->items()->create(['product_id'=>$this->foreign->id,'name'=>$this->foreign->name,'price'=>2000,'quantity'=>1]);
  foreach([$this->manager,$this->other] as $manager) {
   $this->actingAs($manager); $this->get('/manager/orders')->assertOk()->assertSee($o->order_number); $this->get('/manager/orders/'.$o->id)->assertOk()->assertSee('whole order');
  }
  $this->patch('/manager/orders/'.$o->id.'/status',['status'=>'processing','stock_checked'=>1,'reason'=>'All order stock checked'])->assertRedirect();
  $this->assertSame('processing',$o->fresh()->status);
  $stranger=$this->customer('manager');$this->actingAs($stranger)->get('/manager/orders/'.$o->id)->assertNotFound();
  $this->review($o,'cancellation','approve')->assertNotFound();
 }
 public function test_legacy_unique_match_and_ownership_revocation(): void {
  $o=$this->sale(); $o->items()->update(['product_id'=>null,'sku'=>'MY-UNIQUE']);
  $this->get('/manager/orders/'.$o->id)->assertOk();
  $this->patch('/manager/orders/'.$o->id.'/status',['status'=>'processing','stock_checked'=>1,'reason'=>'Check legacy stock'])->assertRedirect();
  $this->mine->update(['manager_id'=>$this->other->id]); $this->get('/manager/orders/'.$o->id)->assertNotFound();
  $this->actingAs($this->other)->get('/manager/orders/'.$o->id)->assertOk();
 }
 public function test_manager_confirms_full_offline_payment_without_admin_and_duplicate_is_blocked(): void {
  $o=$this->sale('pending');$o->forceFill(['manager_created'=>true])->save();
  $payload=['payment_reference'=>'TEST-RECEIPT-1','reason'=>'Verified full receipt','payment_received'=>1];
  $url='/manager/orders/'.$o->id.'/payment'; $this->postJson($url,[])->assertUnprocessable();
  $this->post($url,$payload)->assertRedirect(); $o=$o->fresh();
  $this->assertSame('paid',$o->status);$this->assertSame($this->manager->id,$o->payment_verified_by);$this->assertSame('TEST-RECEIPT-1',$o->payment_id);
  $this->postJson($url,$payload)->assertUnprocessable();$this->assertSame(1,ManagerOrderActivity::where('action','payment_confirmed')->count());
  Mail::assertSent(\App\Mail\OrderConfirmation::class,1);
  $online=$this->sale('pending','paystack');$this->postJson('/manager/orders/'.$online->id.'/payment',$payload)->assertUnprocessable();$this->assertSame('pending',$online->fresh()->status);
 }
 public function test_cancellation_review_and_offline_refund_require_evidence_and_are_idempotent(): void {
  $o=$this->sale(); $c=OrderCancellation::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'status'=>'pending','reason'=>'Customer requested']);
  $this->get('/manager/orders/'.$o->id)->assertOk()->assertSee('Cancellation review');
  $this->review($o,'cancellation','approve')->assertRedirect();$this->assertSame('cancelled',$o->fresh()->status);
  $this->review($o,'cancellation','refund')->assertUnprocessable();
  $this->review($o,'cancellation','refund',['refund_confirmed'=>1])->assertUnprocessable();$this->assertNull($c->fresh()->refund_status);
  $body=['refund_confirmed'=>1,'refund_reference'=>'TEST-OFFLINE-REFUND'];
  $this->review($o,'cancellation','refund',$body)->assertRedirect();$this->assertSame('refunded',$o->fresh()->status);$this->assertNotNull($c->fresh()->refunded_at);
  $this->review($o,'cancellation','refund',$body)->assertUnprocessable(); Http::assertNothingSent();
 }
 public function test_return_workflow_requires_instructions_receipt_inspection_and_never_refunds_on_approval(): void {
  $o=$this->sale('completed');$r=OrderReturn::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'status'=>'pending','workflow_stage'=>'requested','reason'=>'Damaged goods']);
  $this->review($o,'return','refund',['refund_confirmed'=>1])->assertUnprocessable();$this->review($o,'return','approve')->assertUnprocessable();
  $this->review($o,'return','approve',['return_instructions'=>'Return to original pickup point'])->assertRedirect();$this->assertSame('completed',$o->fresh()->status);Http::assertNothingSent();
  $this->review($o,'return','receive')->assertRedirect();$this->review($o,'return','inspect')->assertRedirect();$this->assertSame('inspected',$r->fresh()->stage());
  $this->review($o,'return','refund',['refund_confirmed'=>1,'refund_reference'=>'TEST-RETURN-REFUND'])->assertRedirect();$this->assertSame('refunded',$o->fresh()->status);
 }
 public function test_foreign_review_ids_and_financial_access_are_denied(): void {
  $foreign=$this->order($this->customer(),'pending','cash',$this->foreign);$o=ManagedOrder::find($foreign->id);
  $this->postJson('/manager/orders/'.$o->id.'/payment',['payment_received'=>1,'payment_reference'=>'X','reason'=>'Try'])->assertNotFound();
  $this->review($o,'return','approve')->assertNotFound();
  $this->actingAs($this->customer())->postJson('/manager/orders/'.$o->id.'/payment',[])->assertForbidden();
  $this->get('/admin/orders')->assertForbidden();
 }
 public function test_stripe_failure_or_pending_does_not_mark_refunded_or_send_a_second_request(): void {
  $outcome='failed'; Http::swap(new \Illuminate\Http\Client\Factory()); Http::preventStrayRequests(); Http::fake(['api.stripe.com/*'=>function()use(&$outcome){return Http::response($outcome==='failed'?['error'=>['message'=>'Declined']]:['id'=>'re_test_'.$outcome,'status'=>$outcome],$outcome==='failed'?400:200);}]);
  foreach(['failed','pending','succeeded'] as $outcome) {
   $o=$this->sale('cancelled','stripe');$o->update(['payment_id'=>'pi_test_'.$outcome,'total_usd'=>10]);
   $c=OrderCancellation::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'status'=>'approved','reason'=>'Cancelled']);
   $body=['refund_confirmed'=>1];$this->review($o,'cancellation','refund',$body)->assertRedirect();
   $this->assertSame($outcome==='succeeded'?'refunded':'cancelled',$o->fresh()->status);
   $this->review($o,'cancellation','refund',$body)->assertUnprocessable();
   Http::assertSent(fn($r)=>$r->hasHeader('Idempotency-Key','manager-order-full-refund-'.$o->id) && $r['amount']===1000);
  }
 }
 public function test_paystack_refunds_use_existing_service_without_forging_refunded_status(): void {
  $o=$this->sale('cancelled','paystack');OrderCancellation::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'status'=>'approved','reason'=>'Cancelled']);
  $this->mock(\App\Services\PaystackRefundService::class,function($mock)use($o){$mock->shouldReceive('validateAmount')->twice();$mock->shouldReceive('initiate')->once()->withArgs(fn($order,$record)=>$order->id===$o->id && $record->order_id===$o->id)->andReturn(['success'=>true,'message'=>'Refund pending gateway confirmation.']);});
  $this->review($o,'cancellation','refund',['refund_confirmed'=>1])->assertRedirect();$this->assertSame('cancelled',$o->fresh()->status);
 }
}
