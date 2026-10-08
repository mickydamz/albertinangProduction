<?php
namespace Tests\Regression;
use App\Models\Order;
class PaymentIntegrityTest extends RegressionTestCase
{
    /** @dataProvider gateways */
    public function test_unverified_payment_never_creates_paid_order(string $gateway): void
    {
        $u=$this->customer();$p=$this->product();$this->actingAs($u);
        $before=Order::count();
        $response=$this->postJson($gateway === 'paystack' ? '/paystack/confirm-order' : '/orders/stripe',[
            'payment_intent_id'=>'regression_NOT_VERIFIED','reference'=>'regression_NOT_VERIFIED','total_ngn'=>1,'total_usd'=>0.01,
            'items'=>[['id'=>$p->id,'name'=>$p->name,'basePriceNgn'=>1,'effective_price_ngn'=>1,'quantity'=>1]]
        ]);
        $this->assertContains($response->status(),[400,402,403,409,422,502,503]);
        $this->assertSame($before,Order::count(),'Unverified client values must not become a paid order.');
        $this->assertEquals(10,$p->fresh()->stock);
    }
    public static function gateways(): array {return [['paystack'],['stripe']];}
    /** @dataProvider gateways */
    public function test_empty_order_is_rejected(string $gateway): void
    {
        $this->actingAs($this->customer());
        $this->postJson('/paystack/save-checkout',['items'=>[]])->assertStatus(422)->assertJsonValidationErrors('items');
        $this->assertSame(0,Order::count());
    }
}
