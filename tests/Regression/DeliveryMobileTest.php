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
        return [[null],[''],['123'],['0801234567'],['02012345678'],['+2342012345678'],['08012345678 ext 1'],[['08012345678']],['+0000000000'],['<script>'],[str_repeat('1',100)]];
    }
    /** @dataProvider validNumbers */
    public function test_checkout_normalizes_and_saves_contact($input, $expected): void {
        $this->actingAs($this->customer()); $p=$this->product();
        $response=$this->postJson('/paystack/save-checkout', ['delivery_mobile'=>$input,'items'=>[['product_id'=>$p->id,'quantity'=>1]],'total_ngn'=>1]);
        $response->assertOk();
        $pending=PendingCheckout::where('reference',$response->json('reference'))->firstOrFail();
        $this->assertSame($expected,$pending->delivery_mobile);
        $this->assertEquals(10000,$pending->total_ngn);
    }
    public static function validNumbers(): array { return [['0801 234 5678','+2348012345678'],['+234 901 234 5678','+2349012345678'],['+44 (7700) 900123','+447700900123']]; }
    public function test_paystack_order_and_retry_preserve_snapshot_contact(): void {
        $p=$this->pending($this->customer(),['delivery_mobile'=>'+2348012345678']);
        $this->fakePayment($p); $this->assertTrue($this->fulfil($p)['success']);
        $this->assertSame('+2348012345678',$this->assertOneOrder($p)->delivery_mobile);
        $this->assertTrue($this->fulfil($p)['duplicate']); $this->assertSame(1,Order::count());
    }
    public function test_stripe_order_preserves_snapshot_contact(): void {
        $p=$this->pending($this->customer(),['delivery_mobile'=>'+447700900123']);
        $service=\Mockery::mock(\App\Services\StripeOrderService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('retrievePaymentIntent')->once()->andReturn(['status'=>'succeeded','amount_received'=>$service->expectedUsdCents(10000),'currency'=>'usd','metadata'=>['reference'=>$p->reference]]);
        $result=$service->fulfil($p->reference,'pi_mobile',$p->user_id);
        $this->assertTrue($result['success'], $result['message']);
        $this->assertSame('+447700900123',Order::findOrFail($result['order_id'])->delivery_mobile);
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
}
