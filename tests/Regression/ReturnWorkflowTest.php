<?php
namespace Tests\Regression;
use App\Models\OrderReturn;
use App\Services\ReturnWorkflowService;
use App\Mail\ReturnStageUpdated;
use Illuminate\Support\Facades\{Http,Mail,DB};
class ReturnWorkflowTest extends CriticalTestCase
{
    public function test_inspection_requires_notes_and_returns_page_shows_failure(): void
    {
        $o=$this->order($this->customer(),'completed','paystack');
        $r=OrderReturn::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'reason'=>'Dummy return','status'=>'approved','workflow_stage'=>'received']);
        $this->actingAs($this->customer('admin'));
        $this->from('/admin/returns')->patch('/admin/returns/'.$r->id.'/review',['action'=>'inspect'])->assertSessionHasErrors('admin_notes');
        $this->assertSame('received',$r->fresh()->stage());
        Mail::assertNothingQueued();
        $this->get('/admin/returns')->assertOk()->assertSee('The return action was not saved.')->assertSee('Record the reason or inspection outcome.');
        $this->patch('/admin/returns/'.$r->id.'/review',['action'=>'inspect','admin_notes'=>'Test inspection: item and accessories checked; inspection passed.'])->assertSessionHasNoErrors();
        $this->assertSame('inspected',$r->fresh()->stage());
        $this->get('/admin/returns')->assertSee('Request full Paystack refund');
        Mail::assertQueued(ReturnStageUpdated::class,1);
        Http::assertNothingSent();
    }

    public function test_approval_receipt_inspection_and_refund_are_separate_and_audited(): void
    {
        $o=$this->order($this->customer(),'delivered','paystack');
        $r=OrderReturn::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'reason'=>'Dummy damaged goods','status'=>'pending']);
        $this->actingAs($this->customer('admin'));
        $this->get('/admin/returns')->assertOk()->assertSee('Approve return and send instructions');
        $svc=app(ReturnWorkflowService::class);
        $this->patch('/admin/returns/'.$r->id.'/review',['action'=>'refund'])->assertSessionHasErrors('action');
        $svc->transition($r,'approve',null,'Bring the goods and order number to our collection point.');
        $this->assertSame('approved',$r->fresh()->stage()); $this->assertNull($r->fresh()->refund_status); Http::assertNothingSent();
        $svc->transition($r,'receive','Goods received',null);
        $svc->transition($r,'inspect','Goods checked; full refund approved',null);
        Mail::assertQueued(ReturnStageUpdated::class,3);
        $this->actingAs($o->user)->get('/account/orders')->assertOk()->assertSee('A refund has not yet been requested.')->assertDontSee('Please follow the return instructions sent to you.');
        $this->actingAs($this->customer('admin'));
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::fake(['api.paystack.co/refund'=>Http::response(['status'=>true,'data'=>['id'=>88001,'status'=>'processing','amount'=>1000000,'currency'=>'NGN']],200)]);
        $svc->transition($r,'refund',null,null);
        $this->assertSame('processing',$r->fresh()->refund_status);
        $this->assertSame('delivered',$o->fresh()->status);
        $this->assertSame(5,DB::table('order_request_events')->where('order_id',$o->id)->count());
        $this->patch('/admin/returns/'.$r->id.'/review',['action'=>'refund'])->assertSessionHasErrors('action');
        Http::assertSentCount(1);
        $this->actingAs($o->user)->get('/account/orders')->assertOk()->assertSee('Return refund requested')->assertSee('Refund processing');
    }
    public function test_approval_requires_instructions_and_customer_cannot_review(): void
    {
        $o=$this->order($this->customer(),'completed','paystack');
        $r=OrderReturn::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'reason'=>'Dummy return','status'=>'pending']);
        $this->actingAs($this->customer('admin'))->patch('/admin/returns/'.$r->id.'/review',['action'=>'approve'])->assertSessionHasErrors('return_instructions');
        $this->assertSame('requested',$r->fresh()->stage());
        $this->actingAs($o->user)->patch('/admin/returns/'.$r->id.'/review',['action'=>'approve','return_instructions'=>'Return to store'])->assertForbidden();
        Http::assertNothingSent();
    }
    public function test_refund_workspace_is_admin_only_and_cancellation_cannot_be_rejected(): void
    {
        $o=$this->order($this->customer(),'cancelled','paystack');
        $c=\App\Models\OrderCancellation::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'reason'=>'Dummy cancellation','status'=>'approved']);
        $this->actingAs($this->customer('admin'))->get('/admin/refunds')->assertOk()->assertSee('Check');
        $this->patch('/admin/cancellations/'.$c->id.'/review',['status'=>'rejected'])->assertSessionHasErrors('action');
        $this->assertSame('cancelled',$o->fresh()->status);
        $this->actingAs($o->user)->get('/admin/refunds')->assertForbidden();
    }
    public function test_refund_without_return_requires_a_reason_and_preserves_collection_history(): void
    {
        $o=$this->order($this->customer(),'completed','paystack');
        $r=OrderReturn::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'reason'=>'Dummy return exception','status'=>'pending']);
        $this->actingAs($this->customer('admin'))->patch('/admin/returns/'.$r->id.'/review',['action'=>'refund_without_return'])->assertSessionHasErrors('admin_notes');
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::fake(['api.paystack.co/refund'=>Http::response(['status'=>true,'data'=>['id'=>88002,'status'=>'processed','amount'=>1000000,'currency'=>'NGN']],200)]);
        $this->patch('/admin/returns/'.$r->id.'/review',['action'=>'refund_without_return','admin_notes'=>'Low value damaged item; return waived'])->assertSessionHasNoErrors();
        $this->assertSame('processed',$r->fresh()->refund_status);
        $this->assertSame('completed',$o->fresh()->status);
        $this->assertDatabaseHas('order_request_events',['order_id'=>$o->id,'action'=>'refund_without_return','notes'=>'Low value damaged item; return waived']);
    }

    public function test_return_stage_email_renders_instructions_without_reserved_mail_variable_collision(): void
    {
        $o = $this->order($this->customer(), 'delivered', 'paystack');
        $r = OrderReturn::create(['order_id'=>$o->id, 'user_id'=>$o->user_id, 'reason'=>'Dummy return', 'status'=>'approved']);
        app()->instance('mailer', new \Illuminate\Mail\Mailer('regression', app('view'), new \Symfony\Component\Mailer\Transport\NullTransport(), app('events')));
        $html = (new ReturnStageUpdated($r, 'Return approved', 'Bring the goods with your order number.'))->render();
        $this->assertSame(1, substr_count($html, 'Bring the goods with your order number.'));
        $this->assertStringNotContainsString('<p class="header-sub">', $html);
        $this->assertStringContainsString('<div class="header-title">Return approved</div>', $html);
        $this->assertStringContainsString($o->order_number, $html);
        $this->assertStringContainsString('does not confirm a completed refund', $html);
        $this->assertStringContainsString('email-container', $html);
        $this->assertStringContainsString('support@albertinang.com', $html);
    }

    public function test_order_detail_approval_uses_action_and_requires_instructions(): void
    {
        $o = $this->order($this->customer(), 'completed', 'paystack');
        $r = OrderReturn::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'reason'=>'Dummy collection return','status'=>'pending']);
        $this->actingAs($this->customer('admin'));
        $this->get('/admin/orders/'.$o->id)->assertOk()->assertSee('name="action"', false)->assertSee('name="return_instructions"', false)->assertSee('Approve return and send instructions');
        $this->from('/admin/orders/'.$o->id)->patch('/admin/returns/'.$r->id.'/review', ['status'=>'approved'])->assertSessionHasErrors('action');
        $this->get('/admin/orders/'.$o->id)->assertSee('The action was not saved.');
        Mail::assertNothingQueued();
        $this->patch('/admin/returns/'.$r->id.'/review',['action'=>'approve','return_instructions'=>"Return to the Enugu office.\nInclude your order reference."])->assertSessionHasNoErrors();
        $this->assertSame('approved', $r->fresh()->stage());
        Mail::assertQueued(ReturnStageUpdated::class, fn($mail) => $mail->hasTo($o->user->email) && str_contains($mail->stageMessage, 'Enugu office'));
        Http::assertNothingSent();
        $this->actingAs($o->user)->get('/account/orders')->assertSee('Return to the Enugu office.');
    }

}
