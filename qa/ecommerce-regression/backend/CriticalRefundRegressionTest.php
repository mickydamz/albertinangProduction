<?php
namespace Tests\Regression;
use App\Models\{Order,OrderCancellation,OrderReturn};
use Illuminate\Support\Facades\Http;
/** A queued refund is not settlement. All gateway replies here are synthetic. */
class CriticalRefundRegressionTest extends CriticalTestCase
{
    private function fakeRefund(string $status='pending',int $http=200,bool $success=true): void
    {Http::swap(new \Illuminate\Http\Client\Factory());Http::fake(['api.paystack.co/refund'=>Http::response(['status'=>$success,'message'=>$success?'Refund queued':'Refund rejected','data'=>['id'=>91001,'status'=>$status,'amount'=>1000000,'currency'=>'NGN']],$http),'*'=>Http::response(['status'=>false],503)]);}
    private function cancel($o,array $extra=[])
    {return $this->actingAs($o->user)->post("/account/orders/{$o->id}/cancel",array_merge(['reason'=>'REGRESSION customer cancellation with refund requested'],$extra));}
    private function returned()
    {$o=$this->order($this->customer(),'delivered','paystack');$r=OrderReturn::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'status'=>'approved','workflow_stage'=>'inspected','reason'=>'REGRESSION damaged dummy product']);$this->actingAs($this->customer('admin'));return [$o,$r];}
    private function approve($r,array $extra=[])
    {return $this->patch("/admin/returns/{$r->id}/review",array_merge(['action'=>'refund','admin_notes'=>'REGRESSION dummy refund'],$extra));}
    private function assertInFlight($o,$r,string $status): void
    {$this->assertNotNull($r->fresh()->refund_id);$this->assertSame($status,$r->fresh()->refund_status);$this->assertNull($r->fresh()->refunded_at,'Settlement timestamp must remain empty while Paystack processes the refund.');$this->assertNotSame('refunded',$o->fresh()->status,'Queued/processing is not a completed financial refund.');}
    public function test_CR_REFUND_01_cancel_full_refund_is_queued_not_settled(): void
    {$o=$this->order($this->customer(),'paid','paystack');$this->fakeRefund('pending');$this->cancel($o)->assertSessionHasNoErrors();Http::assertSent(fn($r)=>$r['transaction']===$o->reference&&$r['amount']===1000000&&$r['currency']==='NGN');$this->assertInFlight($o,OrderCancellation::where('order_id',$o->id)->firstOrFail(),'pending');}
    public function test_CR_REFUND_02_approved_return_refund_can_remain_processing(): void
    {[$o,$r]=$this->returned();$this->fakeRefund('processing');$this->approve($r)->assertSessionHasNoErrors();$this->assertInFlight($o,$r,'processing');}
    public function test_CR_REFUND_03_partial_amount_must_not_silently_issue_full_refund(): void
    {[$o,$r]=$this->returned();$this->fakeRefund('pending');$response=$this->approve($r,['amount_ngn'=>2000,'refund_amount'=>2000]);$sent=Http::recorded(fn($req)=>$req->url()==='https://api.paystack.co/refund');if($sent->isEmpty()){$this->assertTrue($response->status()===422||session()->has('errors'),'Unsupported partial refund must be explicitly rejected.');return;}$this->assertEquals(200000,$sent->first()[0]['amount'],'Partial request must not refund the whole order.');$this->assertNotSame('refunded',$o->fresh()->status);}
    public function test_CR_REFUND_04_gateway_rejection_never_marks_order_refunded(): void
    {[$o,$r]=$this->returned();$this->fakeRefund('failed',400,false);$this->approve($r);$this->assertNotSame('refunded',$o->fresh()->status);$this->assertNull($r->fresh()->refund_id);$this->assertNull($r->fresh()->refunded_at);}
    public function test_CR_REFUND_05_timeout_preserves_uncertain_outcome(): void
    {[$o,$r]=$this->returned();Http::swap(new \Illuminate\Http\Client\Factory());Http::fake(fn()=>throw new \Illuminate\Http\Client\ConnectionException('Synthetic refund timeout'));$this->approve($r);$this->assertNotSame('refunded',$o->fresh()->status);$this->assertNull($r->fresh()->refunded_at);$this->assertNull($r->fresh()->refund_id);$this->assertTrue(in_array($r->fresh()->status,['pending','approved','processing','needs_attention'],true));}
    public function test_CR_REFUND_06_repeated_request_while_pending_does_not_issue_twice(): void
    {[$o,$r]=$this->returned();$this->fakeRefund('pending');$this->approve($r);$this->approve($r);Http::assertSentCount(1);$this->assertSame('pending',$r->fresh()->refund_status);$this->assertNull($r->fresh()->refunded_at);}
    public function test_CR_REFUND_07_over_refund_request_is_rejected_before_gateway(): void
    {[$o,$r]=$this->returned();$this->fakeRefund();$response=$this->approve($r,['amount_ngn'=>10001,'refund_amount'=>10001]);Http::assertNothingSent();$this->assertTrue($response->status()===422||session()->has('errors'));$this->assertSame('delivered',$o->fresh()->status);}
    public function test_CR_REFUND_08_delayed_processed_event_and_duplicate_update_one_refund(): void
    {[$o,$r]=$this->returned();$this->fakeRefund('pending');$this->approve($r);$data=['id'=>91001,'status'=>'processed','amount'=>1000000,'currency'=>'NGN','transaction_reference'=>$o->reference,'transaction'=>['reference'=>$o->reference],'refunded_at'=>now()->toIso8601String()];$this->travel(2)->days();$this->webhook('refund.processed',$data)->assertOk();$this->assertSame('processed',$r->fresh()->refund_status);$this->assertSame('delivered',$o->fresh()->status);$this->assertNotNull($r->fresh()->refunded_at);$at=$r->fresh()->refunded_at->toIso8601String();$this->webhook('refund.processed',$data)->assertOk();$this->assertSame($at,$r->fresh()->refunded_at->toIso8601String());Http::assertSentCount(1);$this->travelBack();}
}
