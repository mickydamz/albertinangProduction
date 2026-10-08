<?php
namespace Tests\Regression;

class ReturnWindowPolicyTest extends RegressionTestCase
{
    /** @dataProvider receivedStatuses */
    public function test_thirty_day_window_uses_receipt_and_cannot_reset(string $method,string $status): void
    {
        $this->travelTo(now()->startOfSecond());
        $order=$this->order($this->customer(),'paid');
        $order->update(['fulfillment_method'=>$method,'status'=>$status]);
        $received=$order->fresh()->received_at;
        $this->assertNotNull($received);
        $order->update(['created_at'=>now()->subMonths(3)]);
        $this->assertTrue($order->fresh()->canReturn());
        $this->travel(30)->days();
        $this->assertTrue($order->fresh()->canReturn());
        $this->travel(1)->seconds();
        $this->assertFalse($order->fresh()->canReturn());
        $order->update(['shipping_cost'=>15]);
        $this->assertTrue($received->eq($order->fresh()->received_at));
        $this->assertFalse($order->fresh()->canReturn());
    }
    public function test_legacy_receipt_without_date_is_not_guessed_from_later_edits(): void
    {
        $order=$this->order($this->customer(),'delivered');
        \Illuminate\Support\Facades\DB::table('orders')->where('id',$order->id)->update(['received_at'=>null]);
        $order->refresh()->update(['shipping_cost'=>20]);
        $this->assertNull($order->fresh()->received_at);
        $this->assertTrue($order->fresh()->canReturn());
    }
    public static function receivedStatuses(): array { return [['delivery','delivered'],['pickup','completed']]; }
}
