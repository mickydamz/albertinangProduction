<?php
namespace Tests\Regression;
use App\Models\{OrderReturn,OrderCancellation};
use Illuminate\Support\Facades\Http;
class CancellationReturnTest extends RegressionTestCase
{
    /** @dataProvider cancellable */
    public function test_customer_cancels_unshipped_order(string $status): void
    {
        $user=$this->customer(); $order=$this->order($user,$status); $this->actingAs($user);
        $this->post("/account/orders/{$order->id}/cancel",['reason'=>str_repeat('Regression cancellation ',2)])->assertSessionHasNoErrors();
        $this->assertSame('cancelled',$order->fresh()->status);
        $this->assertDatabaseHas('order_cancellations',['order_id'=>$order->id,'user_id'=>$user->id]);
        Http::assertNothingSent();
    }
    public static function cancellable(): array { return [['pending'],['paid'],['processing']]; }
    /** @dataProvider nonCancellable */
    public function test_cancellation_rejects_ineligible_status(string $status): void
    {
        $u=$this->customer();$o=$this->order($u,$status);$this->actingAs($u);
        $this->post("/account/orders/{$o->id}/cancel",['reason'=>str_repeat('Regression reason ',2)]);
        $this->assertSame($status,$o->fresh()->status);
        $this->assertDatabaseMissing('order_cancellations',['order_id'=>$o->id]);
        Http::assertNothingSent();
    }
    public static function nonCancellable(): array { return [['shipped'],['delivered'],['completed'],['cancelled'],['refunded']]; }
    public function test_duplicate_cancellation_creates_only_one_request(): void
    {
        $u=$this->customer();$o=$this->order($u);$this->actingAs($u);
        for($i=0;$i<2;$i++) $this->post("/account/orders/{$o->id}/cancel",['reason'=>str_repeat('Regression reason ',2)]);
        $this->assertSame(1,OrderCancellation::where('order_id',$o->id)->count());
    }
    /** @dataProvider invalidReasons */
    public function test_return_and_cancel_validate_reason(string $reason): void
    {
        $u=$this->customer();$o=$this->order($u);$this->actingAs($u);
        foreach(['return','cancel'] as $action) $this->postJson("/account/orders/{$o->id}/{$action}",['reason'=>$reason])->assertStatus(422)->assertJsonValidationErrors('reason');
        $this->assertDatabaseMissing('order_returns',['order_id'=>$o->id]);
        $this->assertDatabaseMissing('order_cancellations',['order_id'=>$o->id]);
    }
    public static function invalidReasons(): array { return [[''],['short'],[str_repeat('x',1001)]]; }
    public function test_eligible_return_persists_pending_request(): void
    {
        $u=$this->customer();$o=$this->order($u,'delivered');$this->actingAs($u);
        $this->post("/account/orders/{$o->id}/return",['reason'=>str_repeat('Regression damaged item ',2)])->assertRedirect('/account/orders');
        $this->assertDatabaseHas('order_returns',['order_id'=>$o->id,'user_id'=>$u->id,'status'=>'pending']);
    }
    public function test_duplicate_return_is_not_created(): void
    {
        $u=$this->customer();$o=$this->order($u,'delivered');$this->actingAs($u);
        for($i=0;$i<2;$i++) $this->post("/account/orders/{$o->id}/return",['reason'=>str_repeat('Regression reason ',2)]);
        $this->assertSame(1,OrderReturn::where('order_id',$o->id)->count());
    }
    public function test_expired_return_is_rejected_by_server(): void
    {
        $u=$this->customer();$o=$this->order($u,'delivered');$o->forceFill(['received_at'=>now()->subDays(30)->subSecond()])->save();$this->actingAs($u);
        $this->post("/account/orders/{$o->id}/return",['reason'=>str_repeat('Regression reason ',2)]);
        $this->assertDatabaseMissing('order_returns',['order_id'=>$o->id]);
    }
    public function test_unshipped_order_cannot_be_returned(): void
    {
        $u=$this->customer();$o=$this->order($u,'paid');$this->actingAs($u);
        $this->post("/account/orders/{$o->id}/return",['reason'=>str_repeat('Regression reason ',2)]);
        $this->assertDatabaseMissing('order_returns',['order_id'=>$o->id]);
    }
    public function test_failed_refund_does_not_mark_order_refunded(): void
    {
        $u=$this->customer();$o=$this->order($u,'delivered','paystack');
        $r=OrderReturn::create(['order_id'=>$o->id,'user_id'=>$u->id,'status'=>'approved','workflow_stage'=>'inspected','reason'=>str_repeat('Regression reason ',2)]);
        $this->actingAs($this->customer('admin'));
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::fake(['api.paystack.co/*'=>Http::response(['status'=>false,'message'=>'declined'],400)]);
        $this->patch("/admin/returns/{$r->id}/review",['action'=>'refund']);
        $this->assertNotSame('refunded',$o->fresh()->status);
        $this->assertNull($r->fresh()->refund_id);
        Http::assertSentCount(1);
    }
    public function test_successful_refund_has_reference_and_cannot_be_issued_twice(): void
    {
        $u=$this->customer();$o=$this->order($u,'delivered','paystack');
        $r=OrderReturn::create(['order_id'=>$o->id,'user_id'=>$u->id,'status'=>'approved','workflow_stage'=>'inspected','reason'=>str_repeat('Regression reason ',2)]);
        $this->actingAs($this->customer('admin'));
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::fake(['api.paystack.co/*'=>Http::response(['status'=>true,'data'=>['id'=>991,'status'=>'processed','amount'=>1000000,'currency'=>'NGN']],200)]);
        $this->patch("/admin/returns/{$r->id}/review",['action'=>'refund'])->assertSessionHasNoErrors();
        $this->assertSame('delivered',$o->fresh()->status);
        $this->assertSame('processed',$r->fresh()->refund_status);
        $this->assertEquals(991,$r->fresh()->refund_id);
        $this->patch("/admin/returns/{$r->id}/review",['action'=>'refund']);
        Http::assertSentCount(1);
        Http::assertSent(fn($req)=>$req['amount']===1000000 && $req['transaction']===$o->reference && $req['currency']==='NGN');
    }
}
