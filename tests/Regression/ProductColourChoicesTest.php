<?php
namespace Tests\Regression;
use App\Models\{Color,PendingCheckout};
class ProductColourChoicesTest extends CriticalTestCase {
 private function colours($p){$red=Color::create(['name'=>'Red']);$black=Color::create(['name'=>'Black']);$p->colors()->sync([$red->id,$black->id]);return [$red,$black];}
 private function checkout($p,array $items){$this->actingAs($this->customer());return $this->postJson('/paystack/save-checkout',['delivery_mobile'=>'08012345678','items'=>$items,'fulfillment'=>['method'=>'pickup','pickup_point_id'=>$this->shoppingPoint->id]]);}
 public function test_same_price_colour_lines_survive_trusted_checkout_and_paid_order(){
  $p=$this->product();[$red,$black]=$this->colours($p);
  $this->checkout($p,[['product_id'=>$p->id,'quantity'=>1,'colour_id'=>$red->id,'price'=>1],['product_id'=>$p->id,'quantity'=>2,'colour_id'=>$black->id]])->assertOk();
  $pending=PendingCheckout::latest('id')->firstOrFail();$this->assertCount(2,$pending->items);$this->assertEquals($pending->items[0]['effective_price_ngn'],$pending->items[1]['effective_price_ngn']);$this->assertEquals($red->id,$pending->items[0]['colour_id']);
  $this->fakePayment($pending);$this->assertTrue($this->fulfil($pending)['success']);$order=$this->assertOneOrder($pending);$lines=$order->items()->orderBy('id')->get();$this->assertEquals('Red',$lines[0]->colour_name);$this->assertEquals('Black',$lines[1]->colour_name);$this->assertStringContainsString('Colour: Red',$lines[0]->name);
  $red->update(['name'=>'Changed colour label']);$this->assertEquals('Red',$lines[0]->fresh()->colour_name);
 }
 public function test_missing_unknown_and_unassigned_colours_are_rejected(){
  $p=$this->product();$this->colours($p);$other=Color::create(['name'=>'Green']);foreach([null,99999,$other->id] as $id)$this->checkout($p,[['product_id'=>$p->id,'quantity'=>1,'colour_id'=>$id]])->assertUnprocessable();$this->assertSame(0,PendingCheckout::count());
 }
 public function test_single_colour_autoselects_and_products_without_colours_work(){
  $p=$this->product();$c=Color::create(['name'=>'White']);$p->colors()->attach($c->id);$this->checkout($p,[['product_id'=>$p->id,'quantity'=>1]])->assertOk();$this->assertEquals('White',PendingCheckout::latest('id')->first()->items[0]['colour_name']);$p->colors()->detach();$this->checkout($p,[['product_id'=>$p->id,'quantity'=>1]])->assertOk();$this->assertNull(PendingCheckout::latest('id')->first()->items[0]['colour_name']);
 }
 public function test_swatches_selector_and_filter_use_assigned_colours(){
  $p=$this->product();$this->colours($p);$other=$this->product();$other->update(['name'=>'White-only fictional product']);$white=Color::create(['name'=>'White']);$other->colors()->attach($white->id);
  $this->get('/product/'.$p->id)->assertOk()->assertSee('productColourChoices')->assertSee('Same price for every colour.');foreach(['Red','Black'] as $name)$this->get('/products?'.http_build_query(['options'=>['colour'=>[$name]]]))->assertOk()->assertSee($p->name)->assertDontSee($other->name);$this->get('/products')->assertOk()->assertSee('product-colour-swatches')->assertSee('options[colour][]',false);
 }
 public function test_dark_grey_colour_remains_a_distinct_filter_choice(){
  $p=$this->product();$c=Color::create(['name'=>'Dark Grey']);$p->colors()->attach($c->id);
  $this->get('/products?'.http_build_query(['options'=>['colour'=>['Dark Grey']]]))->assertOk()->assertSee($p->name);
 }
 public function test_colour_options_obey_store_scope_and_publication(){
  $p=$this->product();$this->colours($p);$this->getJson('/products/'.$p->id.'/colour-options')->assertOk()->assertJsonCount(2,'colours');$p->update(['is_active'=>false]);$this->getJson('/products/'.$p->id.'/colour-options')->assertNotFound();
 }
}
