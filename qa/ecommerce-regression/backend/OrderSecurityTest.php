<?php
namespace Tests\Regression;
class OrderSecurityTest extends RegressionTestCase
{
    /** @dataProvider privatePages */
    public function test_other_customer_cannot_read_order(string $suffix): void
    {
        $order=$this->order($this->customer());
        $this->actingAs($this->customer());
        $this->assertDenied($this->get("/account/orders/{$order->id}/{$suffix}"));
    }
    public static function privatePages(): array { return [['invoice'],['invoice/download'],['return'],['cancel']]; }
    /** @dataProvider orderActions */
    public function test_other_customer_cannot_mutate_order(string $action, string $table): void
    {
        $order=$this->order($this->customer(),'delivered');
        $this->actingAs($this->customer());
        $this->assertDenied($this->postJson("/account/orders/{$order->id}/{$action}",['reason'=>str_repeat('Dummy reason ',3)]));
        $this->assertDatabaseMissing($table,['order_id'=>$order->id]);
        $this->assertSame('delivered',$order->fresh()->status);
    }
    public static function orderActions(): array { return [['return','order_returns'],['cancel','order_cancellations']]; }
    /** @dataProvider adminPaths */
    public function test_customer_cannot_open_admin(string $path): void
    {
        $this->actingAs($this->customer()); $this->assertDenied($this->get($path));
    }
    public static function adminPaths(): array { return [['/admin/orders'],['/admin/returns'],['/admin/cancellations'],['/admin/reviews'],['/admin/users']]; }
}
