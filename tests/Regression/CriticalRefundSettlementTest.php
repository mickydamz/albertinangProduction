<?php
namespace Tests\Regression;
use App\Models\OrderReturn;
use App\Services\PaystackRefundService;
use Illuminate\Support\Facades\{DB,Http,Mail};
use App\Mail\OrderRefunded;
class CriticalRefundSettlementTest extends CriticalTestCase
{
    private function start(): array
    {
        $order=$this->order($this->customer(),'delivered','paystack');
        $request=OrderReturn::create(['order_id'=>$order->id,'user_id'=>$order->user_id,'status'=>'approved','reason'=>'Dummy refund']);
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::fake(['*'=>Http::response(['status'=>true,'data'=>['id'=>99001,'status'=>'pending','amount'=>1000000,'currency'=>'NGN']],200)]);
        $this->assertTrue(app(PaystackRefundService::class)->initiate($order,$request)['success']);
        return [$order,$request,['id'=>99001,'amount'=>1000000,'currency'=>'NGN','transaction_reference'=>$order->reference]];
    }
    public function test_CR_SETTLE_01_invalid_signature_cannot_settle(): void
    {
        [$o,$r,$data]=$this->start();
        $this->postJson('/webhooks/paystack',['event'=>'refund.processed','data'=>$data],['X-Paystack-Signature'=>'invalid'])->assertStatus(400);
        $this->assertNull($r->fresh()->refunded_at);$this->assertSame('delivered',$o->fresh()->status);
    }
    public function test_CR_SETTLE_02_wrong_reference_amount_currency_or_id_cannot_settle(): void
    {
        [$o,$r,$data]=$this->start();
        foreach([['transaction_reference'=>'other'],['amount'=>1],['currency'=>'USD'],['id'=>99999]] as $override) {
            $this->webhook('refund.processed',array_merge($data,$override))->assertOk();
            $this->assertNull($r->fresh()->refunded_at);
        }
        $this->assertSame('delivered',$o->fresh()->status);
    }
    public function test_CR_SETTLE_03_real_documented_payload_without_id_settles_once(): void
    {
        [$o,$r,$data]=$this->start();unset($data['id']);
        $this->webhook('refund.processed',$data)->assertOk();$at=$r->fresh()->refunded_at->toIso8601String();
        $this->webhook('refund.processed',$data)->assertOk();$this->webhook('refund.pending',$data)->assertOk();
        $this->webhook('refund.failed',$data)->assertOk();
        $this->assertSame('processed',$r->fresh()->refund_status);$this->assertSame($at,$r->fresh()->refunded_at->toIso8601String());
        Mail::assertQueued(OrderRefunded::class,1);
    }
    public function test_CR_SETTLE_04_needs_attention_and_failed_are_not_settlement(): void
    {
        [$o,$r,$data]=$this->start();
        foreach(['needs-attention','failed'] as $state) {
            $this->webhook('refund.'.$state,$data)->assertOk();$this->assertSame($state,$r->fresh()->refund_status);$this->assertNull($r->fresh()->refunded_at);
        }
        $this->assertSame('delivered',$o->fresh()->status);Mail::assertNotQueued(OrderRefunded::class);
    }
    public function test_CR_SETTLE_05_missed_webhook_is_recovered_by_read_only_lookup(): void
    {
        [$o,$r,$data]=$this->start();
        Http::swap(new \Illuminate\Http\Client\Factory());Http::fake(['*'=>Http::response(['status'=>true,'data'=>$data+['status'=>'processed']],200)]);
        $this->artisan('paystack:reconcile-refunds')->assertExitCode(0);
        $this->assertSame('refunded',$o->fresh()->status);$this->assertNotNull($r->fresh()->refunded_at);
        Http::assertSent(fn($req)=>$req->method()==='GET' && $req->url()==='https://api.paystack.co/refund/99001');Http::assertSentCount(1);
    }
    public function test_CR_SETTLE_06_unknown_timeout_does_not_resend_and_can_be_reconciled(): void
    {
        $o=$this->order($this->customer(),'delivered','paystack');
        $r=OrderReturn::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'status'=>'approved','reason'=>'Dummy refund']);
        Http::swap(new \Illuminate\Http\Client\Factory());Http::fake(fn()=>throw new \Illuminate\Http\Client\ConnectionException('Dummy timeout'));
        $service=app(PaystackRefundService::class);$this->assertFalse($service->initiate($o,$r)['success']);
        Http::swap(new \Illuminate\Http\Client\Factory());Http::fake(['api.paystack.co/transaction/verify/*'=>Http::response(['status'=>true,'data'=>['id'=>123,'reference'=>$o->reference,'status'=>'success','amount'=>1000000,'currency'=>'NGN']],200),'*'=>Http::response(['status'=>true,'data'=>[['id'=>99111,'status'=>'processed','transaction'=>123,'amount'=>1000000,'currency'=>'NGN']]],200)]);
        $this->assertFalse($service->initiate($o,$r)['success']);Http::assertNothingSent();
        $this->artisan('paystack:reconcile-refunds')->assertExitCode(0);Http::assertSent(fn($req)=>$req->method()==='GET');
        $this->assertSame('processed',$r->fresh()->refund_status);$this->assertSame(1,DB::table('paystack_refunds')->count());
    }
    public function test_CR_SETTLE_07_admin_cannot_manually_mark_unconfirmed_refund_complete(): void
    {
        [$o,$r]=$this->start();
        $this->actingAs($this->customer('admin'))->patchJson('/admin/orders/'.$o->id,['status'=>'refunded'])->assertUnprocessable();
        $this->assertSame('delivered',$o->fresh()->status);
    }
    public function test_CR_SETTLE_08_legacy_refund_is_never_issued_again(): void
    {
        $o=$this->order($this->customer(),'refunded','paystack');$r=OrderReturn::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'status'=>'refunded','refund_id'=>'legacy-1','reason'=>'Dummy legacy']);
        $this->actingAs($this->customer('admin'))->patchJson('/admin/returns/'.$r->id.'/review',['status'=>'approved'])->assertUnprocessable();
        Http::assertNothingSent();$this->assertSame(0,DB::table('paystack_refunds')->count());
    }
    public function test_CR_SETTLE_09_numeric_transaction_lookup_is_verified_before_settlement(): void
    {
        [$o,$r]=$this->start();
        Http::swap(new \Illuminate\Http\Client\Factory());Http::fake([
            'api.paystack.co/refund/99001'=>Http::response(['status'=>true,'data'=>['id'=>99001,'transaction'=>123,'status'=>'processed','amount'=>1000000,'currency'=>'NGN']],200),
            'api.paystack.co/transaction/verify/*'=>Http::response(['status'=>true,'data'=>['id'=>123,'reference'=>$o->reference,'status'=>'success','amount'=>1000000,'currency'=>'NGN']],200),
            '*'=>Http::response(['status'=>false],503),
        ]);
        $this->artisan('paystack:reconcile-refunds')->assertExitCode(0);
        $this->assertSame('processed',$r->fresh()->refund_status);Http::assertSentCount(2);
    }
    public function test_CR_SETTLE_10_processing_cannot_regress_to_pending(): void
    {
        [$o,$r,$data]=$this->start();$this->webhook('refund.processing',$data)->assertOk();$this->webhook('refund.pending',$data)->assertOk();
        $this->assertSame('processing',$r->fresh()->refund_status);$this->assertNull($r->fresh()->refunded_at);
    }
    public function test_CR_SETTLE_11_signed_incomplete_payload_cannot_settle(): void
    {
        [$o,$r,$data]=$this->start();unset($data['amount']);$this->webhook('refund.processed',$data)->assertOk();
        $this->assertSame('pending',$r->fresh()->refund_status);$this->assertNull($r->fresh()->refunded_at);
    }
    public function test_CR_SETTLE_12_staging_readiness_refuses_non_test_key(): void
    {
        $this->actingAs($this->customer('admin'))->getJson('/admin/paystack/refund-test-readiness')->assertStatus(409);
        config(['services.paystack.secret'=>'sk_test_synthetic']);
        $this->getJson('/admin/paystack/refund-test-readiness')->assertOk()->assertJson(['test_mode'=>true]);
        Http::assertNothingSent();
    }
    public function test_CR_SETTLE_13_customer_cannot_access_gateway_refund_lookup(): void
    {
        $o=$this->order($this->customer(),'delivered','paystack');
        $this->actingAs($this->customer())->getJson('/admin/orders/'.$o->id.'/paystack-refund-status')->assertForbidden();
        Http::assertNothingSent();
    }
    public function test_CR_SETTLE_14_gateway_status_endpoint_is_read_only_and_sanitized(): void
    {
        [$o,$r,$data]=$this->start();config(['services.paystack.secret'=>'sk_test_synthetic']);
        Http::swap(new \Illuminate\Http\Client\Factory());Http::fake(['*'=>Http::response(['status'=>true,'data'=>$data+['status'=>'processed','domain'=>'test','customer'=>['email'=>'private@example.test']]],200)]);
        $response=$this->actingAs($this->customer('admin'))->getJson('/admin/orders/'.$o->id.'/paystack-refund-status')->assertOk();
        $response->assertJsonPath('gateway.status','processed');
        $this->assertStringNotContainsString('private@example.test',$response->getContent());
        $this->assertStringNotContainsString('sk_test_',$response->getContent());
        $this->assertSame('pending',$r->fresh()->refund_status);$this->assertNull($r->fresh()->refunded_at);
        $this->assertSame('delivered',$o->fresh()->status);Http::assertSentCount(1);
    }
}
