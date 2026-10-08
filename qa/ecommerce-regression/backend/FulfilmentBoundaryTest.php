<?php
namespace Tests\Regression;
use Illuminate\Support\Facades\Http;
class FulfilmentBoundaryTest extends RegressionTestCase
{
    public static function boundaries(): array
    {
        $rows=[];
        foreach(['pickup','delivery'] as $method) foreach(['pending','paid','processing','ready_for_pickup','shipped','completed','delivered','cancelled','refunded'] as $status) {
            $cancel=in_array($status,['pending','paid','processing'],true)||($method==='pickup'&&$status==='ready_for_pickup');
            $return=$status===($method==='pickup'?'completed':'delivered');
            $rows["$method $status"]=[$method,$status,$cancel,$return];
        }
        return $rows;
    }
    /** @dataProvider boundaries */
    public function test_cancel_and_return_have_separate_boundaries(string $method,string $status,bool $cancel,bool $return): void
    {
        $user=$this->customer();$order=$this->order($user,$status);$order->update(['fulfillment_method'=>$method]);
        $this->assertSame($cancel,$order->canCancel());$this->assertSame($return,$order->canReturn());
        $this->actingAs($user)->post("/account/orders/{$order->id}/return",['reason'=>'REGRESSION completed fulfilment return request']);
        $this->assertSame($return,$order->return()->exists());
        // Use another order so each operation is checked independently.
        $other=$this->order($user,$status);$other->update(['fulfillment_method'=>$method]);
        $this->post("/account/orders/{$other->id}/cancel",['reason'=>'REGRESSION cancellation before receipt']);
        $this->assertSame($cancel,$other->cancellation()->exists());
        $this->assertSame($cancel?'cancelled':$status,$other->fresh()->status);
        Http::assertNothingSent();
    }
}
