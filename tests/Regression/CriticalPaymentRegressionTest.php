<?php
namespace Tests\Regression;
use App\Models\{Order,Coupon,CouponUsage};
use Illuminate\Support\Facades\{Http,Mail};
use App\Mail\OrderConfirmation;
class CriticalPaymentRegressionTest extends CriticalTestCase
{
    public function test_CR_PAY_01_verified_bank_transfer_creates_exact_order(): void
    {$p=$this->pending($this->customer());$this->fakePayment($p,['channel'=>'bank_transfer']);$this->assertTrue($this->fulfil($p)['success']);$this->assertOneOrder($p);}
    public function test_CR_PAY_02_webhook_fulfils_when_browser_is_closed(): void
    {$p=$this->pending($this->customer());$this->fakePayment($p);$this->webhook('charge.success',['reference'=>$p->reference])->assertOk();$this->assertOneOrder($p);}
    public function test_CR_PAY_03_late_transfer_recovers_original_checkout(): void
    {$p=$this->pending($this->customer());$this->fakePayment($p,['status'=>'pending']);$this->assertFalse($this->fulfil($p)['success']);$this->assertDatabaseMissing('orders',['reference'=>$p->reference]);$this->fakePayment($p);$this->webhook('charge.success',['reference'=>$p->reference])->assertOk();$this->assertOneOrder($p);}
    public function test_CR_PAY_04_unfunded_transfer_never_creates_paid_order(): void
    {$p=$this->pending($this->customer());$this->fakePayment($p,['status'=>'abandoned']);$this->assertFalse($this->fulfil($p)['success']);$this->assertDatabaseMissing('orders',['reference'=>$p->reference]);$this->assertNull($p->fresh()->fulfilled_at);}
    public function test_CR_PAY_05_gateway_timeout_is_retryable(): void
    {$p=$this->pending($this->customer());Http::swap(new \Illuminate\Http\Client\Factory());Http::fake(fn()=>throw new \Illuminate\Http\Client\ConnectionException('Synthetic timeout'));$r=$this->fulfil($p);$this->assertFalse($r['success']);$this->assertSame('transient',$r['error_type']);$this->assertDatabaseMissing('orders',['reference'=>$p->reference]);$this->fakePayment($p);$this->assertTrue($this->fulfil($p)['success']);$this->assertOneOrder($p);}
    public function test_CR_PAY_06_database_failure_rolls_back_and_can_recover(): void
    {$p=$this->pending($this->customer());$this->fakePayment($p);$fail=true;Order::creating(function()use(&$fail){if($fail)throw new \RuntimeException('Synthetic save failure');});$r=$this->fulfil($p);$this->assertFalse($r['success']);$this->assertSame('transient',$r['error_type']);$this->assertDatabaseMissing('orders',['reference'=>$p->reference]);$this->assertNull($p->fresh()->fulfilled_at);$fail=false;$this->assertTrue($this->fulfil($p)['success']);$this->assertOneOrder($p);}
    public function test_CR_PAY_07_underpayment_is_rejected(): void
    {$p=$this->pending($this->customer());$this->fakePayment($p,['amount'=>999999]);$r=$this->fulfil($p);$this->assertFalse($r['success']);$this->assertSame('fraud',$r['error_type']);$this->assertDatabaseMissing('orders',['reference'=>$p->reference]);}
    public function test_CR_PAY_08_wrong_currency_or_missing_checkout_creates_no_order(): void
    {$p=$this->pending($this->customer());$this->fakePayment($p,['currency'=>'USD']);$this->assertFalse($this->fulfil($p)['success']);$p->delete();$this->fakePayment($p);$this->assertFalse($this->fulfil($p)['success']);$this->assertDatabaseMissing('orders',['reference'=>$p->reference]);}
    public function test_CR_DUP_01_duplicate_webhook_does_not_repeat_side_effects(): void
    {$p=$this->pending($this->customer());$this->fakePayment($p);for($i=0;$i<2;$i++)$this->webhook('charge.success',['reference'=>$p->reference])->assertOk();$this->assertOneOrder($p);Mail::assertSent(OrderConfirmation::class,1);}
    public function test_CR_DUP_02_repeated_browser_confirmation_returns_same_order(): void
    {$p=$this->pending($this->customer());$this->fakePayment($p);$this->actingAs($p->user_id?\App\Models\User::findOrFail($p->user_id):$this->customer());$ids=[];for($i=0;$i<3;$i++){$r=$this->postJson('/paystack/confirm-order',['reference'=>$p->reference])->assertOk();$ids[]=$r->json('order_id');}$this->assertCount(1,array_unique($ids));$this->assertOneOrder($p);Mail::assertSent(OrderConfirmation::class,1);}
    public function test_CR_DUP_03_concurrent_browser_and_webhook_service_calls_create_one_order(): void
    {$p=$this->pending($this->customer());$r=$this->race([$p,$p]);$this->assertCount(1,$r['tables']['orders']);$this->assertCount(1,$r['tables']['order_items']);$this->assertEquals(1,array_sum(array_column($r['workers'],'confirmation_count')));$this->assertTrue($r['workers'][0]['success']);$this->assertTrue($r['workers'][1]['success']);}
    public function test_CR_DUP_04_retry_existing_order_does_not_redeem_coupon_twice(): void
    {$u=$this->customer();$c=Coupon::create(['code'=>'CRIT10','discount_type'=>'percent','value'=>10,'is_active'=>true,'multi_use'=>true,'used_count'=>0]);$p=$this->pending($u,['total_ngn'=>9000,'coupon'=>['id'=>$c->id,'code'=>$c->code,'discount_ngn'=>1000]]);$this->fakePayment($p);$first=$this->fulfil($p);$again=$this->fulfil($p);$this->assertTrue($again['duplicate']);$this->assertSame($first['order_id'],$again['order_id']);$this->assertOneOrder($p);$this->assertEquals(1,$c->fresh()->used_count);$this->assertSame(1,CouponUsage::where('coupon_id',$c->id)->count());}
    public function test_CR_DUP_05_two_customers_cannot_both_buy_last_stock(): void
    {$a=$this->pending($this->customer());$id=$a->items[0]['id'];\App\Models\Product::whereKey($id)->update(['stock'=>1]);$b=$this->pending($this->customer(),['items'=>$a->items]);$r=$this->race([$a,$b]);$sold=0;foreach($r['tables']['order_items'] as $item)if((int)$item['product_id']===$id)$sold+=(int)$item['quantity'];$this->assertLessThanOrEqual(1,$sold,'Concurrent purchases must not oversell the last item.');foreach($r['tables']['products'] as $p)if((int)$p['id']===$id)$this->assertEquals(1-$sold,$p['stock']);}
    public function test_CR_DUP_06_last_coupon_use_cannot_discount_two_concurrent_orders(): void
    {$c=Coupon::create(['code'=>'LAST10','discount_type'=>'percent','value'=>10,'is_active'=>true,'multi_use'=>true,'max_uses'=>1,'used_count'=>0]);$attrs=['total_ngn'=>9000,'coupon'=>['id'=>$c->id,'code'=>$c->code,'discount_ngn'=>1000]];$a=$this->pending($this->customer(),$attrs);$b=$this->pending($this->customer(),$attrs);$r=$this->race([$a,$b]);$discounted=array_filter($r['tables']['orders'],fn($o)=>(int)$o['coupon_id']===$c->id&&(float)$o['coupon_discount_ngn']>0);$this->assertLessThanOrEqual(1,count($discounted),'Exhausted coupon must not silently remain on a second discounted order.');$this->assertCount(1,$r['tables']['coupon_usages']);}
}
