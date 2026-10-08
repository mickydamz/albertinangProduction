<?php
namespace Tests\Regression;

use App\Models\Cart;

class UnlimitedStockPolicyTest extends RegressionTestCase
{
    public function test_cart_accepts_zero_stock_and_can_increase_quantity(): void
    {
        $user=$this->customer(); $product=$this->product();
        $product->update(['stock'=>0]);
        $this->actingAs($user)->post('/cart/add/'.$product->id)->assertSessionHasNoErrors();
        $cart=Cart::where('user_id',$user->id)->where('product_id',$product->id)->firstOrFail();
        $cart->update(['quantity'=>150]);
        $this->patch('/cart/increase/'.$cart->id)->assertSessionHasNoErrors();
        $this->assertEquals(151,$cart->fresh()->quantity);
        $this->assertEquals(0,$product->fresh()->stock);
    }

    public function test_checkout_accepts_more_than_one_hundred_units_without_stock_limit(): void
    {
        $product=$this->product(); $product->update(['stock'=>0]);
        $this->actingAs($this->customer())->postJson('/paystack/save-checkout',[
            'items'=>[['product_id'=>$product->id,'quantity'=>150]],
            'fulfillment'=>['method'=>'pickup'],
        ])->assertOk()->assertJsonPath('total_ngn',1500000);
        $this->assertEquals(0,$product->fresh()->stock);
    }

    public function test_product_page_does_not_cap_quantity_to_recorded_stock(): void
    {
        $product=$this->product(); $product->update(['stock'=>0]);
        $response=$this->get('/product/'.$product->id);
        $response->assertOk()->assertDontSee('const maxStock',false);
        $response->assertSee('qty = Math.max(1, n)',false);
    }
}
