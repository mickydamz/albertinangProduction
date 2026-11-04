<?php
namespace Tests\Regression;
use App\Models\{PendingCheckout, Order};
use App\Support\DeliveryMobile;
use Illuminate\Support\Facades\Http;
class DeliveryMobileTest extends CriticalTestCase
{
    /** @dataProvider invalidNumbers */
    public function test_invalid_mobile_blocks_checkout($value): void {
        $this->actingAs($this->customer());
        $p=$this->product();
        $this->postJson('/paystack/save-checkout', ['delivery_mobile'=>$value,'items'=>[['product_id'=>$p->id,'quantity'=>1]]])
            ->assertStatus(422)->assertJsonValidationErrors('delivery_mobile');
        $this->assertSame(0, PendingCheckout::count());
        $this->assertSame(0, Order::count());
        Http::assertNothingSent();
    }
    public static function invalidNumbers(): array {
        return [[null],[''],['123'],['0801234567'],['02012345678'],['+2342012345678'],['08012345678 ext 1'],[['08012345678']],['+0000000000'],['<script>'],['+447700900123'],['+12025550123'],[str_repeat('1',100)]];
    }
    /** @dataProvider validNumbers */
    public function test_checkout_normalizes_and_saves_contact($input, $expected): void {
        $this->actingAs($this->customer()); $p=$this->product();
        $response=$this->postJson('/paystack/save-checkout', ['delivery_mobile'=>$input,'items'=>[['product_id'=>$p->id,'quantity'=>1]],'total_ngn'=>1,'fulfillment'=>['method'=>'pickup','pickup_point_id'=>$this->shoppingPoint->id]]);
        $response->assertOk();
        $pending=PendingCheckout::where('reference',$response->json('reference'))->firstOrFail();
        $this->assertSame($expected,$pending->delivery_mobile);
        $this->assertEquals(10000,$pending->total_ngn);
    }
    public static function validNumbers(): array { return [['0801 234 5678','+2348012345678'],['+234 901 234 5678','+2349012345678']]; }
    public function test_paystack_order_and_retry_preserve_snapshot_contact(): void {
        $p=$this->pending($this->customer(),['delivery_mobile'=>'+2348012345678']);
        $this->fakePayment($p); $this->assertTrue($this->fulfil($p)['success']);
        $this->assertSame('+2348012345678',$this->assertOneOrder($p)->delivery_mobile);
        $this->assertTrue($this->fulfil($p)['duplicate']); $this->assertSame(1,Order::count());
    }
    public function test_stripe_order_preserves_snapshot_contact(): void {
        $p=$this->pending($this->customer(),['delivery_mobile'=>'+2348012345678']);
        $service=\Mockery::mock(\App\Services\StripeOrderService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('retrievePaymentIntent')->once()->andReturn(['status'=>'succeeded','amount_received'=>$service->expectedUsdCents(10000),'currency'=>'usd','metadata'=>['reference'=>$p->reference]]);
        $result=$service->fulfil($p->reference,'pi_mobile',$p->user_id);
        $this->assertTrue($result['success'], $result['message']);
        $this->assertSame('+2348012345678',Order::findOrFail($result['order_id'])->delivery_mobile);
        $this->assertTrue($service->fulfil($p->reference,'pi_mobile',$p->user_id)['duplicate']);
    }

    public function test_legacy_initiation_endpoints_cannot_bypass_contact(): void {
        $this->actingAs($this->customer());
        foreach (['/initiate-payment','/paystack/initiate-payment'] as $url) $this->postJson($url,['amount'=>10000,'email'=>'test@example.com'])->assertStatus(422);
        Http::assertNothingSent();
    }
    public function test_legacy_checkout_cannot_start_stripe_without_mobile(): void {
        $u=$this->customer(); $this->actingAs($u); $p=$this->pending($u);
        config(['services.stripe.secret'=>'sk_test_synthetic']);
        $this->postJson('/stripe/create-payment-intent',['reference'=>$p->reference])->assertStatus(422);
        Http::assertNothingSent();
    }
    public function test_admin_sees_delivery_mobile_for_registered_customer(): void {
        $order=$this->order($this->customer());
        $order->update(['delivery_mobile'=>'+2348012345678']);
        $this->actingAs($this->customer('admin'))->get('/admin/orders/'.$order->id)
            ->assertOk()->assertSee('Delivery mobile:')->assertSee('+2348012345678');
    }
    public function test_admin_sees_delivery_mobile_for_guest_order(): void {
        $order=$this->order($this->customer());
        $order->update(['user_id'=>null,'customer_email'=>'guest@example.com','delivery_mobile'=>'+2349012345678']);
        $this->actingAs($this->customer('admin'))->get('/admin/orders/'.$order->id)
            ->assertOk()->assertSee('Delivery mobile:')->assertSee('+2349012345678');
    }

    public function test_missing_order_mobile_is_restored_from_its_fulfilled_checkout_on_callback_retry(): void {
        $p=$this->pending($this->customer(),['delivery_mobile'=>'+2348012345678']);
        $this->fakePayment($p); $first=$this->fulfil($p);
        Order::whereKey($first['order_id'])->update(['delivery_mobile'=>null]);
        $retry=$this->fulfil($p);
        $this->assertTrue($retry['success']); $this->assertTrue($retry['duplicate']);
        $this->assertSame('+2348012345678',Order::findOrFail($first['order_id'])->delivery_mobile);
        $this->assertSame(1,Order::count());
    }
    /** @dataProvider unsafeSnapshots */
    public function test_contact_recovery_rejects_unmatched_or_unfulfilled_snapshot(string $case): void {
        $u=$this->customer(); $p=$this->pending($u,['delivery_mobile'=>'+2348012345678','fulfilled_at'=>now()]);
        $order=$this->order($u); $order->update(['reference'=>$p->reference]);
        if ($case==='user') $p->update(['user_id'=>$this->customer()->id]);
        if ($case==='amount') $p->update(['total_ngn'=>1,'fulfillment'=>['method'=>'pickup','pickup_point_id'=>$this->shoppingPoint->id]]);
        if ($case==='unfulfilled') $p->update(['fulfilled_at'=>null]);
        if ($case==='invalid') $p->update(['delivery_mobile'=>'+447700900123']);
        $this->assertFalse(app(\App\Services\OrderDeliveryContact::class)->restore($order->id));
        $this->assertNull($order->fresh()->delivery_mobile);
    }
    public static function unsafeSnapshots(): array {return [['user'],['amount'],['unfulfilled'],['invalid']];}
    public function test_recovery_never_overwrites_an_existing_order_contact(): void {
        $u=$this->customer();$p=$this->pending($u,['delivery_mobile'=>'+2348012345678','fulfilled_at'=>now()]);
        $order=$this->order($u);$order->update(['reference'=>$p->reference,'delivery_mobile'=>'+2349012345678']);
        $this->assertFalse(app(\App\Services\OrderDeliveryContact::class)->restore($order->id));
        $this->assertSame('+2349012345678',$order->fresh()->delivery_mobile);
    }

}
