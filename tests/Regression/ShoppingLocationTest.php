<?php
namespace Tests\Regression;
use App\Models\{Location,State,PickupPoint,Product,Category,Brand,PendingCheckout,Setting};
use App\Services\ShoppingLocation;
use Illuminate\Support\Facades\{DB,Http};
class ShoppingLocationTest extends RegressionTestCase {
 private $area;private $one;private $two;private $product;private $other;private $destination;
 protected function setUp():void {
  parent::setUp();
  $state=State::create(['name'=>'Fictional State','is_active'=>true,'delivery_enabled'=>true]);
  $this->area=Location::create(['name'=>'Fictional City','state_id'=>$state->id,'is_active'=>true,'shipping_cost'=>null,'truck_shipping_cost'=>null]);
  $this->one=PickupPoint::create(['location_id'=>$this->area->id,'name'=>'Branch Alpha','address'=>'1 Fictional Road','hours'=>'9–5','is_active'=>true]);
  $this->two=PickupPoint::create(['location_id'=>$this->area->id,'name'=>'Branch Beta','address'=>'2 Fictional Road','is_active'=>true]);
  $category=Category::create(['name'=>'Test appliances','is_active'=>true]);
  $brand=Brand::create(['name'=>'Fictional Brand','slug'=>'fictional-brand','is_active'=>true]);
  $this->product=Product::create(['name'=>'Alpha visible appliance','price'=>1000,'stock'=>0,'is_active'=>true,'category_id'=>$category->id,'brand'=>$brand->name,'brand_id'=>$brand->id,'weight_kg'=>1]);
  $this->other=Product::create(['name'=>'Beta exclusive appliance','price'=>1000,'stock'=>0,'is_active'=>true,'category_id'=>$category->id,'brand'=>$brand->name,'brand_id'=>$brand->id]);
  foreach([$this->product,$this->other] as $p)$p->locations()->sync([$this->area->id]);
  $this->product->pickupPoints()->sync([$this->one->id]);$this->other->pickupPoints()->sync([$this->two->id]);
  $this->destination=Location::create(['name'=>'Delivery neighbourhood','state_id'=>$state->id,'is_active'=>true,'shipping_cost'=>2500,'truck_shipping_cost'=>7000]);
  DB::table('origin_delivery_areas')->insert(['pickup_point_id'=>$this->one->id,'location_id'=>$this->destination->id,'is_active'=>true]);
 }
 private function choose($point=null){return $this->withSession(['shopping_point_id'=>($point??$this->one)->id]);}
 private function checkout(array $f,array $items=[]){return $this->choose()->actingAs($this->customer())->postJson('/paystack/save-checkout',['delivery_mobile'=>'08012345678','items'=>$items?:[['product_id'=>$this->product->id,'quantity'=>5]],'fulfillment'=>$f]);}
 public function test_first_visit_can_browse_all_published_products(){ $this->get('/')->assertOk()->assertDontSee('id="shoppingLocationBar"',false)->assertSee($this->product->name)->assertSee($this->other->name); }
 public function test_state_selector_does_not_request_a_store(){
  $this->getJson('/shopping/locations')->assertOk()->assertJsonCount(1,'states')->assertJsonMissingPath('stores');
  $this->postJson('/shopping/location',['state_id'=>$this->area->state_id])->assertOk();$this->assertEquals($this->area->state_id,session('shopping_state_id'));
  $this->get('/')->assertDontSee('id="shoppingLocationBar"',false);$this->actingAs($this->customer())->get('/checkout')->assertSee('Choose your state')->assertDontSee('Store / collection branch');
 }
 public function test_all_storefront_surfaces_include_all_stores_in_selected_state(){
  foreach(['/','/products','/search?searchTerm=appliance','/category/test-appliances','/brand/fictional-brand'] as $path)$this->choose()->get($path)->assertOk()->assertSee($this->product->name)->assertSee($this->other->name);
  $this->choose()->get('/product/'.$this->other->id)->assertOk();
  $elsewhere=State::create(['name'=>'Different State','is_active'=>true]);$this->withSession(['shopping_state_id'=>$elsewhere->id])->get('/product/'.$this->other->id)->assertOk()->assertSee($this->other->name);
 }
 public function test_old_url_name_cannot_override_session_state(){ $this->choose()->get('/?location=Other')->assertOk()->assertSee($this->product->name)->assertSee($this->other->name); }
 public function test_hidden_product_is_not_visible_even_when_assigned(){ $this->product->update(['is_active'=>false]);$this->choose()->get('/')->assertDontSee($this->product->name);$this->choose()->postJson('/shopping/cart-check',['items'=>[$this->product->id]])->assertUnprocessable(); }
 public function test_zero_stock_does_not_block_collection_and_origin_is_snapshotted(){
  $res=$this->checkout(['method'=>'pickup','pickup_point_id'=>$this->one->id])->assertOk();$p=PendingCheckout::where('reference',$res->json('reference'))->firstOrFail();$this->assertEquals(5,$p->items[0]['quantity']);$this->assertEquals($this->one->id,$p->fulfillment['dispatch_point_id']);$this->assertEquals('Branch Alpha',$p->fulfillment['dispatch_point_name']);
 }
 public function test_foreign_cart_item_and_other_collection_point_are_rejected(){
  $this->checkout(['method'=>'pickup','pickup_point_id'=>$this->one->id],[['product_id'=>$this->other->id,'quantity'=>1]])->assertUnprocessable();
  $this->checkout(['method'=>'pickup','pickup_point_id'=>$this->two->id])->assertUnprocessable();
 }
 public function test_missing_fulfilment_and_legacy_plain_text_are_rejected(){foreach([[],['method'=>'pickup'],['method'=>'pickup','pickup_location'=>'anything'],['method'=>'delivery']] as $f)$this->checkout($f)->assertUnprocessable();}
 public function test_supported_delivery_uses_server_rate_and_requires_address(){
  $this->checkout(['method'=>'delivery','delivery_location_id'=>$this->destination->id,'shipping_address'=>'3 Fictional Lane','delivery_fee_ngn'=>0])->assertOk();$p=PendingCheckout::latest('id')->first();$this->assertEquals(2500,$p->fulfillment['delivery_fee_ngn']);
  $this->checkout(['method'=>'delivery','delivery_location_id'=>$this->destination->id])->assertUnprocessable();
 }
 public function test_unsupported_and_unconfigured_routes_fail_but_deliberate_zero_passes(){
  $this->checkout(['method'=>'delivery','delivery_location_id'=>$this->area->id,'shipping_address'=>'test'])->assertUnprocessable();
  $this->destination->update(['shipping_cost'=>null]);$this->checkout(['method'=>'delivery','delivery_location_id'=>$this->destination->id,'shipping_address'=>'test'])->assertUnprocessable();
  $this->destination->update(['shipping_cost'=>0]);$this->checkout(['method'=>'delivery','delivery_location_id'=>$this->destination->id,'shipping_address'=>'test'])->assertOk();
 }
 public function test_truck_rate_is_charged_and_missing_truck_rate_is_rejected(){
  $this->product->update(['requires_truck'=>true]);$this->checkout(['method'=>'delivery','delivery_location_id'=>$this->destination->id,'shipping_address'=>'test'])->assertOk();$this->assertEquals(7000,PendingCheckout::latest('id')->first()->fulfillment['delivery_fee_ngn']);
  $this->destination->update(['truck_shipping_cost'=>null]);$this->checkout(['method'=>'delivery','delivery_location_id'=>$this->destination->id,'shipping_address'=>'test'])->assertUnprocessable();
 }
 public function test_inactive_branch_area_state_and_disabled_method_are_rejected(){
  $this->one->update(['is_active'=>false]);$this->checkout(['method'=>'pickup','pickup_point_id'=>$this->one->id])->assertUnprocessable();$this->one->update(['is_active'=>true]);
  $this->area->update(['is_active'=>false]);$this->checkout(['method'=>'pickup','pickup_point_id'=>$this->one->id])->assertOk();$this->area->update(['is_active'=>true]);
  $this->area->state->update(['is_active'=>false]);$this->checkout(['method'=>'pickup','pickup_point_id'=>$this->one->id])->assertUnprocessable();$this->area->state->update(['is_active'=>true]);
  Setting::set('pickup_enabled','0');$this->checkout(['method'=>'pickup','pickup_point_id'=>$this->one->id])->assertUnprocessable();
 }
 public function test_location_api_is_scoped_to_origin_routes_and_collection_point(){
  $this->choose()->getJson('/api/locations')->assertOk()->assertJsonCount(2)->assertJsonFragment(['name'=>$this->destination->name]);
  $this->choose()->getJson('/api/pickup-points?items[]='.$this->product->id)->assertOk()->assertJsonCount(2)->assertJsonFragment(['name'=>'Fictional State — '.$this->one->name]);
 }
 public function test_state_switch_keeps_unavailable_items_and_reports_ids_without_removal(){
  $state=State::create(['name'=>'Empty state','is_active'=>true]);
  $user=$this->customer();$this->actingAs($user);\App\Models\Cart::create(['user_id'=>$user->id,'product_id'=>$this->product->id,'quantity'=>1]);
  $this->choose()->postJson('/shopping/location',['state_id'=>$state->id,'items'=>[$this->product->id]])->assertOk()->assertJsonFragment(['incompatible_ids'=>[]]);
  $this->assertEquals($state->id,session('shopping_state_id'));$this->assertDatabaseHas('carts',['product_id'=>$this->product->id,'user_id'=>$user->id]);
  $this->postJson('/shopping/cart-check',['items'=>[$this->product->id]])->assertOk()->assertJsonFragment(['incompatible_ids'=>[]]);
  $this->postJson('/paystack/save-checkout',['delivery_mobile'=>'08012345678','items'=>[['product_id'=>$this->product->id,'quantity'=>1]],'fulfillment'=>['method'=>'pickup','pickup_point_id'=>$this->one->id]])->assertUnprocessable();
 }
 public function test_collection_choice_is_deferred_and_requires_full_basket_at_chosen_point(){
  $this->product->pickupPoints()->attach($this->two->id);
  $this->choose()->getJson('/api/pickup-points?items[]='.$this->product->id)->assertOk()->assertJsonFragment(['id'=>$this->two->id,'available'=>true]);
  $this->checkout(['method'=>'pickup','pickup_point_id'=>$this->two->id])->assertOk();
  $this->assertEquals($this->two->id,PendingCheckout::latest('id')->first()->fulfillment['dispatch_point_id']);
  $this->choose()->getJson('/api/pickup-points?items[]='.$this->product->id.'&items[]='.$this->other->id)->assertJsonFragment(['id'=>$this->one->id,'available'=>false]);
 }
 public function test_state_switch_reloads_current_url_without_home_redirect(){
  $this->choose()->actingAs($this->customer())->get('/checkout')->assertSee('location.reload();',false)->assertDontSee("location.href='/';",false);
 }
 public function test_admin_is_unscoped_and_branch_assignments_are_validated(){
  $this->actingAs($this->customer('admin'))->get('/admin/shopping-locations')->assertOk()->assertSee('Branch Alpha')->assertSee('Branch Beta');
  $this->assertSame([$this->one->id],ShoppingLocation::validateAssignments([],[$this->one->id],true));
 }
 public function test_two_branches_require_explicit_product_assignment(){ $this->expectException(\Illuminate\Validation\ValidationException::class);ShoppingLocation::validateAssignments([$this->area->id],[],true); }
 public function test_admin_destination_blanks_are_not_free(){ $this->actingAs($this->customer('admin'))->put('/admin/shipping',['shipping'=>[$this->destination->id=>'']])->assertRedirect();$this->assertNull($this->destination->fresh()->shipping_cost); }
 public function test_cart_post_cannot_bypass_product_scope(){ $this->other->update(['is_active'=>false]);$this->choose()->actingAs($this->customer())->postJson('/cart/add/'.$this->other->id)->assertUnprocessable(); }
 public function test_new_delivery_areas_work_without_store_coverage(){
 DB::table('origin_delivery_areas')->delete();
 $new=Location::create(['state_id'=>$this->area->state_id,'name'=>'New neighbourhood','is_active'=>true,'shipping_cost'=>1200]);
 $this->choose()->getJson('/api/locations')->assertJsonFragment(['name'=>$new->name]);
 $this->checkout(['method'=>'delivery','delivery_location_id'=>$new->id,'shipping_address'=>'Fictional address'])->assertOk();
 $this->assertEquals(1200,PendingCheckout::latest('id')->first()->fulfillment['delivery_fee_ngn']);
 }
 public function test_state_visibility_and_delivery_are_independent(){
 $state=$this->area->state;$state->update(['delivery_enabled'=>false]);
 $this->getJson('/shopping/locations')->assertJsonFragment(['id'=>$state->id]);
 $this->choose()->getJson('/api/locations')->assertExactJson([]);
 $this->checkout(['method'=>'pickup','pickup_point_id'=>$this->one->id])->assertOk();
 $state->update(['is_active'=>false,'delivery_enabled'=>true]);
 $this->getJson('/shopping/locations')->assertJsonMissing(['id'=>$state->id]);
 $this->postJson('/shopping/location',['state_id'=>$state->id])->assertUnprocessable();
 $this->choose()->getJson('/api/locations')->assertExactJson([]);
 }
}
