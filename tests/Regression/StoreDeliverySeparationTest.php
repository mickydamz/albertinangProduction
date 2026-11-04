<?php
namespace Tests\Regression;
use App\Models\{State,PickupPoint,Location,Product,StoreLocation,PendingCheckout};
use App\Services\ShoppingLocation;
use Illuminate\Support\Facades\DB;
class StoreDeliverySeparationTest extends RegressionTestCase {
 public function test_create_store_requires_state_and_creates_collection_without_area(){
  $s=State::create(['name'=>'Collection only state','is_active'=>false]);$this->actingAs($this->customer('admin'));
  $this->post('/admin/pickup-points',['name'=>'New collection branch','address'=>'Fictional address'])->assertSessionHasErrors('state_id');
  $before=Location::count();$this->post('/admin/pickup-points',['state_id'=>$s->id,'name'=>'New collection branch','address'=>'Fictional address','hours'=>'9 to 5'])->assertRedirect(route('admin.shopping-locations.index'));
  $p=PickupPoint::where('name','New collection branch')->firstOrFail();$this->assertNull($p->location_id);$this->assertEquals($s->id,$p->state_id);$this->assertTrue((bool)$p->is_active);$this->assertNotNull($p->store_location_id);$this->assertEquals($before,Location::count());$this->assertNull(ShoppingLocation::point($p->id));
 }
 public function test_new_store_without_area_can_show_product_and_collect_in_disabled_delivery_state(){
  $s=State::create(['name'=>'Collection state','is_active'=>true,'delivery_enabled'=>false]);$p=PickupPoint::create(['name'=>'Collection branch','state_id'=>$s->id,'address'=>'Fictional address','hours'=>'9 to 5','is_active'=>true]);
  $product=$this->product();$product->pickupPoints()->sync([$p->id]);$product->locations()->detach();$this->withSession(['shopping_point_id'=>$p->id]);
  $this->get('/')->assertOk()->assertSee($product->name);$this->getJson('/shopping/locations')->assertJsonFragment(['name'=>'Collection state']);
  $this->getJson('/api/states')->assertExactJson([]);$this->actingAs($this->customer())->get('/checkout')->assertOk()->assertSee('Collection state')->assertDontSee('id="shoppingArea"',false);
  $f=ShoppingLocation::resolve(['method'=>'pickup','pickup_point_id'=>$p->id],[$product->id],false);$this->assertEquals($p->id,$f['pickup_point_id']);$this->assertSame(0,$f['delivery_fee_ngn']);
 }
 public function test_delivery_switch_does_not_disable_collection_but_rejects_delivery(){
  $s=$this->shoppingArea->state;$product=$this->product();$this->shoppingArea->update(['shipping_cost'=>2500]);DB::table('origin_delivery_areas')->insert(['pickup_point_id'=>$this->shoppingPoint->id,'location_id'=>$this->shoppingArea->id,'is_active'=>true]);
  $this->actingAs($this->customer('admin'))->put('/admin/shipping',['states'=>[$s->id=>0],'shipping'=>[$this->shoppingArea->id=>''],'areas'=>[$this->shoppingArea->id=>1]])->assertRedirect();
  $this->assertFalse((bool)$s->fresh()->delivery_enabled);$this->assertNull($this->shoppingArea->fresh()->shipping_cost);$this->assertNotNull(ShoppingLocation::point($this->shoppingPoint->id));
  $this->expectException(\Illuminate\Validation\ValidationException::class);ShoppingLocation::resolve(['method'=>'delivery','delivery_location_id'=>$this->shoppingArea->id,'shipping_address'=>'Fictional address'],[$product->id],false);
 }
 public function test_forms_offer_stores_without_area_selector_and_delivery_screen_is_unified(){
  $p=$this->product();$this->actingAs($this->customer('admin'))->get('/admin/products/'.$p->id.'/edit')->assertOk()->assertSee('Available at stores')->assertDontSee('id="loc-inp"',false);
  $this->get('/admin/pickup-points/create')->assertOk()->assertSee('name="state_id"',false)->assertDontSee('name="location_id"',false);
  $this->get('/admin/shipping')->assertOk()->assertSee('Delivery settings')->assertSee('Delivery enabled in')->assertSee('Standard charge');
 }
 public function test_migration_preserves_previously_hidden_store_availability(){
  $state=State::create(['name'=>'Legacy disabled delivery state','is_active'=>false]);$area=Location::create(['name'=>'Legacy area','state_id'=>$state->id,'is_active'=>true]);$point=PickupPoint::create(['name'=>'Legacy hidden store','address'=>'Fictional address','location_id'=>$area->id,'is_active'=>true]);
  DB::table('pickup_points')->where('id',$point->id)->update(['state_id'=>null]);
  (require database_path('migrations/2026_11_03_120000_separate_stores_from_delivery_areas.php'))->up();
  $this->assertEquals($state->id,$point->fresh()->state_id);$this->assertFalse((bool)$point->fresh()->is_active);$this->assertTrue((bool)$this->shoppingPoint->fresh()->is_active);
 }
 public function test_state_with_stores_cannot_be_deleted(){
  $this->actingAs($this->customer('admin'))->delete('/admin/states/'.$this->shoppingPoint->state_id)->assertSessionHasErrors('state');$this->assertNotNull($this->shoppingPoint->fresh()->state);
 }
}
