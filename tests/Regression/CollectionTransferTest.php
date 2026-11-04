<?php
namespace Tests\Regression;
use App\Models\{PickupPoint,PendingCheckout};
use App\Services\CollectionTransfer;
use Illuminate\Support\Facades\DB;
class CollectionTransferTest extends RegressionTestCase {
 public function test_directional_product_override_and_quantity_are_server_priced(){
  $product=$this->product();$dest=PickupPoint::create(['name'=>'Destination','address'=>'Fictional street','location_id'=>$this->shoppingArea->id,'is_active'=>true]);
  DB::table('collection_transfer_rates')->insert(['origin_id'=>$this->shoppingPoint->id,'destination_id'=>$dest->id,'product_id'=>null,'fee_ngn'=>500]);
  DB::table('collection_transfer_rates')->insert(['origin_id'=>$this->shoppingPoint->id,'destination_id'=>$dest->id,'product_id'=>$product->id,'fee_ngn'=>125]);
  $this->assertEquals(125,CollectionTransfer::quote($product->id,$dest)['transfer_unit_fee_ngn']);
  $res=$this->actingAs($this->customer())->postJson('/paystack/save-checkout',['delivery_mobile'=>'08012345678','items'=>[['product_id'=>$product->id,'quantity'=>3]],'fulfillment'=>['method'=>'pickup','pickup_point_id'=>$dest->id,'delivery_fee_ngn'=>0]])->assertOk();
  $pending=PendingCheckout::where('reference',$res->json('reference'))->firstOrFail();
  $this->assertEquals(375,$pending->fulfillment['collection_transfer_total_ngn']);$this->assertEquals(3*(float)$product->sell_price+375,$pending->total_ngn);
  $this->assertEquals($this->shoppingPoint->id,$pending->items[0]['supplying_point_id']);
  CollectionTransfer::revalidate($pending);
  DB::table('collection_transfer_rates')->where('product_id',$product->id)->update(['fee_ngn'=>200]);
  $this->expectException(\Illuminate\Validation\ValidationException::class);CollectionTransfer::revalidate($pending);
 }
 public function test_catalogue_needs_no_state_and_routes_are_directional(){
  $product=$this->product();$this->withSession(['shopping_point_id'=>null,'shopping_state_id'=>null])->get('/product/'.$product->id)->assertOk();
  $dest=PickupPoint::create(['name'=>'Destination','address'=>'Fictional road','location_id'=>$this->shoppingArea->id,'is_active'=>true]);
  DB::table('collection_transfer_rates')->insert(['origin_id'=>$dest->id,'destination_id'=>$this->shoppingPoint->id,'fee_ngn'=>10]);
  $this->expectException(\Illuminate\Validation\ValidationException::class);CollectionTransfer::quote($product->id,$dest);
 }
 public function test_collection_requires_manual_state_and_point_in_that_state(){
  $product=$this->product();$user=$this->customer();
  $this->actingAs($user)->withSession(['shopping_point_id'=>null,'shopping_state_id'=>null])->postJson('/paystack/save-checkout',['delivery_mobile'=>'08012345678','items'=>[['product_id'=>$product->id,'quantity'=>1]],'fulfillment'=>['method'=>'pickup','pickup_point_id'=>$this->shoppingPoint->id]])->assertUnprocessable()->assertJsonValidationErrors('shopping_location');
  $other=\App\Models\State::create(['name'=>'Other collection state','is_active'=>true]);
  $this->withSession(['shopping_state_id'=>$other->id])->getJson('/api/pickup-points?items[]='.$product->id)->assertExactJson([]);
  $this->postJson('/paystack/save-checkout',['delivery_mobile'=>'08012345678','items'=>[['product_id'=>$product->id,'quantity'=>1]],'fulfillment'=>['method'=>'pickup','pickup_point_id'=>$this->shoppingPoint->id]])->assertUnprocessable()->assertJsonValidationErrors('fulfillment.pickup_point_id');
 }
 public function test_delivery_adds_per_unit_transfer_and_area_cost_and_revalidates_both(){
  $product=$this->product();$this->shoppingArea->update(['shipping_cost'=>2500,'truck_shipping_cost'=>7000]);
  $dest=PickupPoint::create(['name'=>'Delivery dispatch store','address'=>'Fictional road','location_id'=>$this->shoppingArea->id,'is_active'=>true]);
  DB::table('collection_transfer_rates')->insert(['origin_id'=>$this->shoppingPoint->id,'destination_id'=>$dest->id,'product_id'=>$product->id,'fee_ngn'=>125]);
  $res=$this->actingAs($this->customer())->postJson('/paystack/save-checkout',['delivery_mobile'=>'08012345678','items'=>[['product_id'=>$product->id,'quantity'=>3]],'fulfillment'=>['method'=>'delivery','dispatch_point_id'=>$dest->id,'delivery_location_id'=>$this->shoppingArea->id,'shipping_address'=>'Fictional road','delivery_fee_ngn'=>0]])->assertOk();
  $pending=PendingCheckout::where('reference',$res->json('reference'))->firstOrFail();
  $this->assertEquals(2500,$pending->fulfillment['area_delivery_fee_ngn']);$this->assertEquals(375,$pending->fulfillment['collection_transfer_total_ngn']);$this->assertEquals(2875,$pending->fulfillment['delivery_fee_ngn']);$this->assertEquals(3*(float)$product->sell_price+2875,$pending->total_ngn);$this->assertEquals($this->shoppingPoint->id,$pending->items[0]['supplying_point_id']);
  CollectionTransfer::revalidate($pending);$this->shoppingArea->update(['shipping_cost'=>3000]);
  $this->expectException(\Illuminate\Validation\ValidationException::class);CollectionTransfer::revalidate($pending);
 }
 public function test_delivery_without_a_transfer_route_is_blocked(){
  $product=$this->product();$this->shoppingArea->update(['shipping_cost'=>2500]);
  $dest=PickupPoint::create(['name'=>'Unpriced dispatch store','address'=>'Fictional road','location_id'=>$this->shoppingArea->id,'is_active'=>true]);
  $this->actingAs($this->customer())->postJson('/paystack/save-checkout',['delivery_mobile'=>'08012345678','items'=>[['product_id'=>$product->id,'quantity'=>1]],'fulfillment'=>['method'=>'delivery','dispatch_point_id'=>$dest->id,'delivery_location_id'=>$this->shoppingArea->id,'shipping_address'=>'Fictional road']])->assertUnprocessable()->assertJsonValidationErrors('items');
  $this->assertEquals(0,PendingCheckout::count());
 }
 public function test_missing_route_blocks_payment_and_local_collection_is_free(){
  $product=$this->product();$this->assertEquals(0,CollectionTransfer::quote($product->id,$this->shoppingPoint)['transfer_unit_fee_ngn']);
  $dest=PickupPoint::create(['name'=>'Unpriced destination','address'=>'Fictional street','location_id'=>$this->shoppingArea->id,'is_active'=>true]);
  $this->expectException(\Illuminate\Validation\ValidationException::class);CollectionTransfer::quote($product->id,$dest);
 }
}
