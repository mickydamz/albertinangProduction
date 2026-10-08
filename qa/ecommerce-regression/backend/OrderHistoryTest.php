<?php
namespace Tests\Regression;
class OrderHistoryTest extends CriticalTestCase
{
    public function test_full_history_pagination_dates_search_and_customer_isolation(): void
    {
        $user=$this->customer();
        for($i=0;$i<12;$i++) $this->order($user,'paid','paystack')->forceFill(['created_at'=>now()->subDays($i)])->save();
        $old=$this->order($user,'delivered','paystack'); $old->forceFill(['created_at'=>now()->subMonths(4)])->save();
        $other=$this->order($this->customer(),'paid','paystack');
        $this->actingAs($user);
        $this->get('/account/orders')->assertOk()->assertViewHas('orders',fn($o)=>$o->total()===13 && $o->count()===10)->assertSee('Order history pages');
        $this->get('/account/orders?page=2')->assertOk()->assertSee($old->order_number)->assertDontSee($other->order_number);
        foreach([1=>12,3=>12,6=>13,12=>13] as $months=>$count) $this->get('/account/orders?period='.$months)->assertOk()->assertViewHas('orders',fn($o)=>$o->total()===$count);
        $this->get('/account/orders?search='.$old->order_number)->assertOk()->assertViewHas('orders',fn($o)=>$o->total()===1)->assertSee($old->order_number);
        $this->get('/account/orders?sort=oldest')->assertOk()->assertViewHas('orders',fn($o)=>$o->first()->id===$old->id);
        $this->get('/account/orders?status=delivered')->assertOk()->assertViewHas('orders',fn($o)=>$o->total()===1);
    }
}
