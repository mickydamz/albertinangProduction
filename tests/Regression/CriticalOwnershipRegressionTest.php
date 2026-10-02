<?php
namespace Tests\Regression;
use App\Models\{OrderReturn,OrderCancellation,Review,Ticket,TicketReply};
class CriticalOwnershipRegressionTest extends CriticalTestCase
{
    private function otherOrder(string $status='paid')
    {$o=$this->order($this->customer(),$status,'paystack');$this->actingAs($this->customer());return $o;}
    public function test_CR_ACCESS_01_other_customer_invoice_is_denied(): void
    {$o=$this->otherOrder();$this->assertDenied($this->get("/account/orders/{$o->id}/invoice"));}
    public function test_CR_ACCESS_02_other_customer_pdf_is_denied(): void
    {$o=$this->otherOrder();$r=$this->get("/account/orders/{$o->id}/invoice/download");$this->assertDenied($r);$this->assertNotSame('application/pdf',$r->headers->get('Content-Type'));}
    public function test_CR_ACCESS_03_other_customer_cancellation_page_is_denied(): void
    {$o=$this->otherOrder();$this->assertDenied($this->get("/account/orders/{$o->id}/cancel"));}
    public function test_CR_ACCESS_04_other_customer_cancellation_submission_changes_nothing(): void
    {$o=$this->otherOrder();$this->assertDenied($this->postJson("/account/orders/{$o->id}/cancel",['reason'=>'REGRESSION cross-customer cancellation attempt']));$this->assertSame('paid',$o->fresh()->status);$this->assertSame(0,OrderCancellation::where('order_id',$o->id)->count());\Illuminate\Support\Facades\Http::assertNothingSent();}
    public function test_CR_ACCESS_05_other_customer_return_page_is_denied(): void
    {$o=$this->otherOrder('delivered');$this->assertDenied($this->get("/account/orders/{$o->id}/return"));}
    public function test_CR_ACCESS_06_other_customer_return_submission_changes_nothing(): void
    {$o=$this->otherOrder('delivered');$this->assertDenied($this->postJson("/account/orders/{$o->id}/return",['reason'=>'REGRESSION cross-customer damaged item attempt']));$this->assertSame('delivered',$o->fresh()->status);$this->assertSame(0,OrderReturn::where('order_id',$o->id)->count());}
    public function test_CR_ACCESS_07_other_customer_review_cannot_be_edited(): void
    {$owner=$this->customer();$p=$this->product();$r=Review::create(['user_id'=>$owner->id,'product_id'=>$p->id,'rating'=>4,'content'=>'REGRESSION original review content']);$this->actingAs($this->customer());$this->assertDenied($this->putJson("/reviews/{$r->id}",['rating'=>1,'comment'=>'REGRESSION unauthorized change']));$this->assertSame(4,(int)$r->fresh()->rating);$this->assertSame('REGRESSION original review content',$r->fresh()->content);}
    public function test_CR_ACCESS_08_other_customer_ticket_cannot_be_read_edited_or_replied(): void
    {$t=Ticket::create(['user_id'=>$this->customer()->id,'subject'=>'REGRESSION private ticket','description'=>'Private original description','priority'=>'low','status'=>'open']);$this->actingAs($this->customer());foreach(["/tickets/{$t->id}","/tickets/{$t->id}/edit"] as $url)$this->assertDenied($this->get($url));$this->assertDenied($this->putJson("/tickets/{$t->id}",['subject'=>'Stolen','description'=>'Changed','status'=>'closed']));$this->assertDenied($this->postJson("/tickets/{$t->id}/reply",['message'=>'Unauthorized reply']));$this->assertSame('Private original description',$t->fresh()->description);$this->assertSame(0,TicketReply::where('ticket_id',$t->id)->count());}
    public function test_CR_ACCESS_09_customer_cannot_open_admin_or_change_order(): void
    {$o=$this->order($this->customer(),'paid','paystack');$this->actingAs($this->customer());foreach(['/admin/orders','/admin/returns','/admin/cancellations','/admin/reviews','/admin/users'] as $url)$this->assertDenied($this->get($url));$this->assertDenied($this->patchJson("/admin/orders/{$o->id}",['status'=>'refunded']));$this->assertSame('paid',$o->fresh()->status);}
}
