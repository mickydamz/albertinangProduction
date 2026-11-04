<?php
namespace Tests\Regression;
use App\Models\{Brand,Category,Subcategory,Product,ManagerApprovalRequest};
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Middleware\ThrottleRequests;
class ManagerProductCreationTest extends RegressionTestCase {
 private $manager;private $brand;private $category;
 protected function setUp():void {parent::setUp();$this->withoutMiddleware(ThrottleRequests::class);$this->manager=$this->customer('manager');$this->brand=Brand::create(['name'=>'Assigned brand','slug'=>'assigned-brand','manager_id'=>$this->manager->id]);$this->category=Category::create(['name'=>'Test category']);$this->actingAs($this->manager);}
 private function data(array $extra=[]):array {return array_merge(['name'=>'Fictional new product','brand_id'=>$this->brand->id,'category_id'=>$this->category->id,'price'=>25000,'stock'=>3],$extra);}
 public function test_assigned_empty_brand_can_create_draft_set_price_but_not_publish():void {
  $this->get('/manager/products')->assertOk()->assertSee('Add product');$this->get('/manager/products/create')->assertOk()->assertSee('Assigned brand');
  $this->post('/manager/products',$this->data())->assertRedirect();$p=Product::latest('id')->firstOrFail();$this->assertSame($this->manager->id,$p->manager_id);$this->assertSame($this->brand->id,$p->brand_id);$this->assertSame('Assigned brand',$p->brand);$this->assertFalse($p->is_active);$this->assertEquals(25000,$p->price);$this->assertEquals(1,$p->moq);$this->assertSame(0,ManagerApprovalRequest::count());$this->patchJson('/manager/products/'.$p->id.'/toggle-active')->assertForbidden();$this->assertFalse($p->fresh()->is_active);
 }
 public function test_foreign_brand_and_tampered_ownership_publication_are_denied():void {
  $b=Brand::create(['name'=>'Other brand','slug'=>'other-brand','manager_id'=>$this->customer('manager')->id]);$this->get('/manager/products/create')->assertDontSee('Other brand');$this->postJson('/manager/products',$this->data(['brand_id'=>$b->id]))->assertStatus(422);
  foreach(['manager_id'=>$this->manager->id,'supplier_id'=>1,'brand'=>'Other brand'] as $key=>$value)$this->postJson('/manager/products',$this->data([$key=>$value]))->assertStatus(422);$this->assertSame(0,Product::count());
 }
 public function test_brand_reassignment_revokes_creation_and_no_brand_explained():void {
  $this->brand->update(['manager_id'=>$this->customer('manager')->id]);$this->get('/manager/products/create')->assertOk()->assertSee('No brands are assigned');$this->postJson('/manager/products',$this->data())->assertStatus(422);
 }
 public function test_customer_cannot_create_manager_products():void {$this->actingAs($this->customer());$this->get('/manager/products/create')->assertForbidden();$this->postJson('/manager/products',$this->data())->assertForbidden();}
 public function test_subcategory_must_belong_to_selected_category():void {
  $type=Subcategory::create(['name'=>'Valid type','category_id'=>$this->category->id]);$this->postJson('/manager/products',$this->data())->assertStatus(422);$other=Category::create(['name'=>'Other category']);$this->postJson('/manager/products',$this->data(['category_id'=>$other->id,'subcategory_id'=>$type->id]))->assertStatus(422);$this->post('/manager/products',$this->data(['subcategory_id'=>$type->id]))->assertRedirect();$this->assertSame(1,Product::count());
 }
 public function test_images_and_description_are_saved_safely():void {
  $this->post('/manager/products',$this->data(['description'=>'<script>bad</script>Product details','images'=>[UploadedFile::fake()->image('product.jpg')]]))->assertRedirect();$p=Product::latest('id')->firstOrFail();$this->assertStringNotContainsString('<script>',$p->description);$this->assertCount(1,$p->images);\Illuminate\Support\Facades\Storage::disk('public')->assertExists($p->images->first()->image_url);
 }
 public function test_complete_admin_style_details_are_saved_unpublished():void {
  $tag=\App\Models\Tag::create(['name'=>'Finish','slug'=>'finish']);$size=\App\Models\Size::create(['name'=>'Large']);$color=\App\Models\Color::create(['name'=>'Silver']);
  $state=\App\Models\State::create(['name'=>'Test State','is_active'=>true]);$location=\App\Models\Location::create(['name'=>'Test showroom','state_id'=>$state->id,'is_active'=>true]);
  $this->post('/manager/products',$this->data([
   'tags'=>[(string)$tag->id,'Energy Saver'],'new_tag_options_data'=>[json_encode(['name'=>'Finish','options'=>['Silver','Black']])],
   'sizes'=>[$size->id],'colors'=>[$color->id],'locations'=>[$location->id],
   'attr_groups'=>[['group'=>'Dimensions','attrs'=>[['key'=>'Width','value'=>'60 cm']]]],
   'installation_options'=>[['label'=>'Install','price'=>3500,'description'=>'Fit and test']],
   'requires_truck_set'=>'1','requires_truck'=>1,'weight_kg'=>35,
   'description_blocks'=>json_encode([['type'=>'heading','text'=>'Highlights','level'=>2],['type'=>'paragraph','text'=>'Product details']]),
  ]))->assertRedirect();
  $p=Product::latest('id')->firstOrFail();$this->assertFalse($p->is_active);$this->assertEquals(25000,$p->price);
  $this->assertCount(2,$p->tags);$this->assertEquals(['Silver','Black'],$p->custom_attributes['tag_options']['Finish']);
  $this->assertEquals('60 cm',$p->custom_attributes['Width']);$this->assertEquals('Dimensions',$p->custom_attribute_groups[0]['group']);
  $this->assertEquals('Install',$p->installation_options[0]['label']);$this->assertEquals(3500,$p->installation_options[0]['price']);
  $this->assertCount(1,$p->sizes);$this->assertCount(1,$p->colors);$this->assertCount(1,$p->locations);$this->assertTrue($p->requires_truck);$this->assertEquals(35,$p->weight_kg);$this->assertEquals('Highlights',$p->description_blocks[0]['text']);
 }
 public function test_invalid_related_data_and_unsafe_description_urls_roll_back():void {
  $this->postJson('/manager/products',$this->data(['sizes'=>[999999]]))->assertStatus(422);
  $this->postJson('/manager/products',$this->data(['description_blocks'=>json_encode([['type'=>'button','label'=>'Click','url'=>'javascript:alert(1)']])]))->assertStatus(422);
  $this->postJson('/manager/products',$this->data(['tags'=>['Test tag'],'new_tag_options_data'=>[json_encode(['name'=>'Unselected tag','options'=>['Test']])]]))->assertStatus(422);
  $this->assertSame(0,Product::count());
 }
 public function test_form_has_tags_specifications_installation_and_manager_upload_route():void {
  $this->get('/manager/products/create')->assertOk()->assertSee('Tags &amp; Variants',false)->assertSee('attr-groups-container')->assertSee('json-paste-btn')->assertSee('installation_options')->assertSee('/manager/products/block-image',false)->assertDontSee('/admin/upload-block-image',false);
 }
 public function test_manager_block_image_upload_requires_assigned_brand():void {
  $this->post('/manager/products/block-image',['image'=>UploadedFile::fake()->image('description.jpg')])->assertOk()->assertJsonStructure(['url']);
  $this->brand->update(['manager_id'=>null]);$this->postJson('/manager/products/block-image',['image'=>UploadedFile::fake()->image('description.jpg')])->assertForbidden();
 }

}
