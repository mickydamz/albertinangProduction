<?php
namespace Tests\Regression;
use Illuminate\Support\Facades\DB;
use App\Models\PickupPoint;
class TransferRateAdminTest extends RegressionTestCase {
 private function rate(){
  $p=PickupPoint::create(['name'=>'Fictional destination','address'=>'Test road','location_id'=>$this->shoppingArea->id,'is_active'=>true]);
  return DB::table('collection_transfer_rates')->insertGetId(['origin_id'=>$this->shoppingPoint->id,'destination_id'=>$p->id,'fee_ngn'=>150]);
 }
 public function test_admin_can_edit_and_delete_a_rate_and_missing_ids_are_404(){
  $id=$this->rate();$this->actingAs($this->customer('admin'));
  $this->get('/admin/shopping-locations?tab=transfers')->assertOk()->assertSee('Save changes')->assertSee('Route default');
  $this->put('/admin/collection-transfer-rates/'.$id,['fee_ngn'=>275])->assertRedirect('/admin/shopping-locations?tab=transfers');
  $this->assertDatabaseHas('collection_transfer_rates',['id'=>$id,'fee_ngn'=>275]);
  $this->putJson('/admin/collection-transfer-rates/'.$id,['fee_ngn'=>-1])->assertUnprocessable();
  $this->assertDatabaseHas('collection_transfer_rates',['id'=>$id,'fee_ngn'=>275]);
  $this->delete('/admin/collection-transfer-rates/'.$id)->assertRedirect('/admin/shopping-locations?tab=transfers');
  $this->assertDatabaseMissing('collection_transfer_rates',['id'=>$id]);
  $this->put('/admin/collection-transfer-rates/'.$id,['fee_ngn'=>1])->assertNotFound();
  $this->delete('/admin/collection-transfer-rates/'.$id)->assertNotFound();
 }
 public function test_customers_and_managers_cannot_change_transfer_prices(){
  $id=$this->rate();foreach(['user','manager'] as $role){$this->actingAs($this->customer($role));$this->assertDenied($this->put('/admin/collection-transfer-rates/'.$id,['fee_ngn'=>1]));$this->assertDenied($this->delete('/admin/collection-transfer-rates/'.$id));}
  $this->assertDatabaseHas('collection_transfer_rates',['id'=>$id,'fee_ngn'=>150]);
 }
}
