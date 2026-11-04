<?php
namespace Tests\Regression;
use App\Models\Order;

class ManagerSalesTest extends RegressionTestCase
{
 private $manager; private $mine;
 protected function setUp(): void {
  parent::setUp(); $this->manager=$this->customer('manager'); $this->mine=$this->product();
  $this->mine->forceFill(['manager_id'=>$this->manager->id,'name'=>'Managed appliance','brand'=>'Managed brand','sku'=>'MINE-001'])->save(); $this->actingAs($this->manager);
 }
 private function sale(string $status='paid'): Order { return $this->order($this->customer(),$status,'cash',$this->mine); }
 public function test_all_history_shows_only_managed_lines_even_on_unassigned_mixed_orders(): void {
  $past=$this->sale('completed'); $current=$this->sale('processing'); $foreign=$this->product(); $foreign->update(['name'=>'Secret product']);
  $past->items()->create(['product_id'=>$foreign->id,'name'=>$foreign->name,'price'=>999999,'quantity'=>3]);
  $past->update(['total'=>3019997,'customer_email'=>'private@example.test','shipping_address'=>'Private address']);
  $other=$this->order($this->customer(),'paid','cash',$foreign);
  $this->get('/manager/sales')->assertOk()->assertSee($past->order_number)->assertSee($current->order_number)->assertDontSee($other->order_number)->assertDontSee('Secret product')->assertDontSee('private@example.test')->assertDontSee('Private address')->assertDontSee('3,019,997')
   ->assertViewHas('summary',fn($s)=>$s->orders_count==2 && $s->confirmed_units==2 && $s->confirmed_value==20000 && $s->current_orders==1);
  $this->get('/manager/orders/'.$past->id)->assertOk();
 }
 public function test_unpaid_closed_and_unverified_manual_orders_excluded_from_confirmed_totals(): void {
  foreach(['pending','cancelled','refunded','failed','paid','delivered'] as $status) $this->sale($status);
  $manual=$this->sale('processing'); $manual->forceFill(['manager_created'=>true])->save();
  $this->get('/manager/sales')->assertOk()->assertViewHas('summary',fn($s)=>$s->orders_count==7 && $s->confirmed_value==20000 && $s->confirmed_units==2);
  $manual->forceFill(['payment_verified_at'=>now()])->save();
  $this->get('/manager/sales')->assertOk()->assertViewHas('summary',fn($s)=>$s->confirmed_value==30000);
 }
 public function test_historical_prices_and_quantities_used_without_installation(): void {
  $order=$this->sale(); $order->items()->update(['price'=>1250.50,'quantity'=>3,'installation_extra_ngn'=>500]); $this->mine->update(['price'=>99999]);
  $this->get('/manager/sales')->assertOk()->assertSee('3,751.50')->assertViewHas('summary',fn($s)=>$s->confirmed_value==3751.50 && $s->confirmed_units==3);
 }
 public function test_filters_apply_to_rows_and_summary_with_inclusive_end_date(): void {
  $old=$this->sale('completed'); $old->forceFill(['created_at'=>'2024-01-01 10:00:00'])->save();
  $end=$this->sale(); $end->forceFill(['created_at'=>'2024-01-31 23:59:59'])->save();
  $this->get('/manager/sales?from=2024-01-02&to=2024-01-31&status=paid&search=MINE-001&brand=Managed%20brand')->assertOk()->assertSee($end->order_number)->assertDontSee($old->order_number)->assertViewHas('summary',fn($s)=>$s->orders_count==1);
  $this->get('/manager/sales?brand=foreign')->assertOk()->assertViewHas('summary',fn($s)=>$s->orders_count==0);
  $this->getJson('/manager/sales?from=2024-02-01&to=2024-01-01')->assertUnprocessable();
  $this->getJson('/manager/sales?status=invalid')->assertUnprocessable(); $this->getJson('/manager/sales?search[]=x')->assertUnprocessable();
  $this->get('/manager/sales?to=2024-01-31')->assertOk();
 }
 public function test_legacy_matches_require_unique_identity_and_never_override_a_different_id(): void {
  $sku=$this->sale(); $sku->items()->update(['product_id'=>null,'name'=>'Old name','sku'=>'MINE-001']);
  $name=$this->sale(); $name->items()->update(['product_id'=>null,'sku'=>null]);
  $this->get('/manager/sales')->assertOk()->assertSee($sku->order_number)->assertSee($name->order_number)->assertViewHas('summary',fn($s)=>$s->orders_count==2);
  $duplicate=$this->product(); $duplicate->forceFill(['name'=>$this->mine->name,'sku'=>$this->mine->sku])->save();
  $conflict=$this->sale(); $conflict->items()->update(['product_id'=>$duplicate->id,'sku'=>$this->mine->sku]);
  $this->get('/manager/sales')->assertOk()->assertDontSee($sku->order_number)->assertDontSee($name->order_number)->assertDontSee($conflict->order_number)->assertViewHas('summary',fn($s)=>$s->orders_count==0);
 }
 public function test_reassignment_revokes_old_manager_history(): void {
  $sale=$this->sale(); $other=$this->customer('manager'); $this->mine->update(['manager_id'=>$other->id]);
  $this->get('/manager/sales')->assertOk()->assertDontSee($sale->order_number)->assertSee('No sales match');
  $this->actingAs($other)->get('/manager/sales')->assertOk()->assertSee($sale->order_number);
 }
 public function test_pagination_and_role_boundaries(): void {
  for($i=0;$i<27;$i++) $this->sale();
  $this->get('/manager/sales?status=paid')->assertOk()->assertViewHas('sales',fn($s)=>$s->total()==27 && $s->count()==25)->assertSee('status=paid',false);
  $this->get('/manager/sales?page=2')->assertOk()->assertViewHas('sales',fn($s)=>$s->count()==2);
  $this->actingAs($this->customer())->get('/manager/sales')->assertForbidden(); auth()->logout(); $this->get('/manager/sales')->assertRedirect('/login');
 }
}
