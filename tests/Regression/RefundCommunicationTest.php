<?php
namespace Tests\Regression;
use App\Models\{OrderCancellation, OrderReturn};
use App\Mail\{OrderRefunded, OrderRefundStatusUpdated, OrderCancellationRequestedUser};
use App\Support\RefundProgress;
use Illuminate\Support\Facades\{DB, Mail};
use App\Services\PaystackRefundService;

class RefundCommunicationTest extends CriticalTestCase
{
    /** @dataProvider states */
    public function test_email_and_account_use_provider_state_even_when_order_says_refunded($status, $label, $type): void
    {
        $o=$this->order($this->customer(), 'refunded', 'paystack');
        $class=$type==='return'?OrderReturn::class:OrderCancellation::class;
        $r=$class::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'reason'=>'Dummy cancellation','status'=>'approved','refund_status'=>$status]);
        $o->load('cancellation','return');
        $this->assertSame($label, RefundProgress::forOrder($o)['label']);
        $email=new OrderRefunded($o);
        $this->assertStringContainsString($label, $email->envelope()->subject);
        $this->assertStringContainsString($label, view('emails.order-refunded',['order'=>$o,'refundProgress'=>$email->progress()])->render());
        $this->assertStringNotContainsString('funds have been returned', strtolower(view('emails.order-refunded',['order'=>$o,'refundProgress'=>$email->progress()])->render()));
        $this->actingAs($o->user)->get('/account/orders')->assertOk()->assertSee($label);
        if ($type==='cancellation') $this->assertStringContainsString($label, view('emails.orders.cancellation-user',['order'=>$o,'cancellation'=>$r])->render());
    }
    public static function states(): array
    { $cases=[]; foreach (['cancellation','return'] as $type) foreach ([['pending','Refund requested'],['processing','Refund processing'],['processed','Refund processed'],['needs-attention','Refund needs attention'],['failed','Refund failed'],['unknown','Refund awaiting confirmation']] as $state) $cases[]=array_merge($state,[$type]); return $cases; }

    public function test_transitions_send_matching_email_once_and_old_events_do_not_regress(): void
    {
        $o=$this->order($this->customer(), 'cancelled', 'paystack');
        $r=OrderCancellation::create(['order_id'=>$o->id,'user_id'=>$o->user_id,'reason'=>'Dummy cancellation','status'=>'approved','refund_status'=>'requesting']);
        $id=DB::table('paystack_refunds')->insertGetId(['order_id'=>$o->id,'request_type'=>'cancellation','request_id'=>$r->id,'transaction_reference'=>$o->reference,'amount'=>1000000,'currency'=>'NGN','status'=>'requesting','created_at'=>now(),'updated_at'=>now()]);
        $svc=app(PaystackRefundService::class);
        foreach(['pending','processing','processed'] as $status){
            $data=['id'=>123,'transaction_reference'=>$o->reference,'amount'=>1000000,'currency'=>'NGN','status'=>$status];
            $this->assertTrue($svc->apply($data,$id));
            $this->assertTrue($svc->apply($data,$id));
            Mail::assertQueued($status==='processed'?OrderRefunded::class:OrderRefundStatusUpdated::class, fn($m)=>$m->refundStatus===$status);
            $this->assertSame($status,$r->fresh()->refund_status);
            $this->assertSame($status==='processed'?'refunded':'cancelled',$o->fresh()->status);
        }
        Mail::assertQueued(OrderRefunded::class,1);
        Mail::assertQueued(OrderRefundStatusUpdated::class,2);
        $svc->apply(['id'=>123,'transaction_reference'=>$o->reference,'amount'=>1000000,'currency'=>'NGN','status'=>'pending'],$id);
        $this->assertSame('processed',$r->fresh()->refund_status);
        Mail::assertQueued(OrderRefunded::class,1);
        Mail::assertQueued(OrderRefundStatusUpdated::class,2);
    }
}
