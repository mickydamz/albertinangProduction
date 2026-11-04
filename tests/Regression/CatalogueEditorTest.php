<?php
namespace Tests\Regression;
use App\Models\{User,Brand,Category,Subcategory,Product,Tag,Size,Color,Location,State,CatalogueRevision,CatalogueRevisionEvent,CatalogueUpload};
use App\Services\CatalogueWorkflow;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\{Storage,Mail};

class CatalogueEditorTest extends RegressionTestCase
{
    private $editor; private $admin; private $manager; private $brand; private $category;
    protected function setUp(): void {
        parent::setUp(); $this->withoutMiddleware(ThrottleRequests::class);
        $this->editor=$this->customer('catalogue_editor'); $this->admin=$this->customer('admin'); $this->manager=$this->customer('manager');
        $this->brand=Brand::create(['name'=>'Cross brand','slug'=>'cross-brand','manager_id'=>$this->manager->id]);
        $this->category=Category::create(['name'=>'Fictional category']); $this->actingAs($this->editor);
    }
    private function data(array $extra=[]): array { return array_merge(['name'=>'Catalogue fictional appliance','brand_id'=>$this->brand->id,'category_id'=>$this->category->id,'price'=>25000,'stock'=>5,'version'=>0,'intent'=>'draft','publish_after_approval'=>1,'locations'=>[$this->shoppingArea->id],'pickup_points'=>[$this->shoppingPoint->id]],$extra); }
    private function draft(array $extra=[]): CatalogueRevision {
        $this->actingAs($this->editor)->post('/catalogue/submissions',$this->data($extra))->assertRedirect(); return CatalogueRevision::latest('id')->firstOrFail();
    }
    private function approve(CatalogueRevision $r,array $extra=[]) {
        return $this->actingAs($this->admin)->post('/admin/catalogue/'.$r->id.'/review',array_merge(['version'=>$r->fresh()->version,'decision'=>'approve','notes'=>'Checked full product details'],$extra));
    }
    private function live(): Product {
        $p=Product::create(['name'=>'Original fictional product','brand_id'=>$this->brand->id,'brand'=>$this->brand->name,'manager_id'=>$this->manager->id,'category_id'=>$this->category->id,'price'=>10000,'stock'=>3,'is_active'=>true]);$p->locations()->sync([$this->shoppingArea->id]);$p->pickupPoints()->sync([$this->shoppingPoint->id]);return $p;
    }
    public function test_repeated_new_form_submission_reuses_its_draft(): void {
        $key=(string)\Illuminate\Support\Str::uuid(); $this->draft(['creation_key'=>$key]);
        $this->post('/catalogue/submissions',$this->data(['creation_key'=>$key]))->assertRedirect();
        $this->assertSame(1,CatalogueRevision::count()); $this->assertSame(1,CatalogueRevisionEvent::count());
    }
    public function test_editor_login_and_two_factor_verification_land_in_catalogue(): void {
        $this->editor->update(['password'=>\Illuminate\Support\Facades\Hash::make('FictionalPassword123!'),'two_factor_enabled'=>false]);
        auth()->logout();
        $this->post('/login',['email'=>$this->editor->email,'password'=>'FictionalPassword123!'])->assertRedirect('/catalogue');
        $this->editor->update(['two_factor_code'=>'123456','two_factor_expires_at'=>now()->addMinutes(10)]);
        $this->actingAs($this->editor)->get('/catalogue')->assertRedirect('/2fa');
        $this->post('/2fa',['two_factor_code'=>'123456'])->assertRedirect('/catalogue');
        $this->assertNull($this->editor->fresh()->two_factor_code);
    }
    public function test_dashboard_forms_navigation_and_all_brand_choices_render(): void {
        Brand::create(['name'=>'Unassigned brand','slug'=>'unassigned']);
        $this->get('/catalogue')->assertOk()->assertSee('My submissions')->assertSee('Change history')->assertDontSee('Refunds');
        foreach(['products','submissions','history'] as $view) $this->get('/catalogue?view='.$view)->assertOk();
        $this->get('/catalogue/products/create')->assertOk()->assertSee('Cross brand')->assertSee('Unassigned brand')->assertSee('Send for approval')->assertSee('description_blocks')->assertSee('installation_options')->assertDontSee('name="is_active"',false);
        $this->get('/admin/users/create')->assertForbidden();
    }
    public function test_draft_is_private_and_can_be_saved_submitted_and_approved(): void {
        $r=$this->draft(); $this->assertSame(0,Product::count()); $this->assertSame('draft',$r->status);
        $this->get('/catalogue/submissions/'.$r->id)->assertOk()->assertSee('Catalogue fictional appliance');
        $this->put('/catalogue/submissions/'.$r->id,$this->data(['version'=>1,'intent'=>'submit','price'=>27000]))->assertRedirect();
        $this->assertSame('pending',$r->fresh()->status); $this->assertSame(0,Product::count());
        $this->actingAs($this->admin)->get('/admin/catalogue')->assertOk()->assertSee('Catalogue fictional appliance');
        $this->get('/admin/catalogue/'.$r->id)->assertOk()->assertSee('Approve and apply');
        $this->approve($r)->assertRedirect(); $p=Product::firstOrFail();
        $this->assertTrue($p->is_active); $this->assertEquals(27000,$p->price); $this->assertSame($this->manager->id,$p->manager_id);
        $this->assertSame($this->brand->id,$p->brand_id); $this->assertSame($this->editor->id,$r->fresh()->editor_id); $this->assertSame($this->admin->id,$r->fresh()->reviewer_id);
        $this->assertSame(3,$r->events()->count()); Mail::assertNothingSent();
    }
    public function test_approval_can_keep_product_unpublished(): void {
        $r=$this->draft(['intent'=>'submit','publish_after_approval'=>0,'moq'=>null]); $this->approve($r)->assertRedirect(); $this->assertFalse(Product::firstOrFail()->is_active);
    }
    public function test_complete_product_details_images_and_relations_survive_review(): void {
        $tag=Tag::create(['name'=>'Finish','slug'=>'finish']); $size=Size::create(['name'=>'Large']); $color=Color::create(['name'=>'Silver']);
        $state=State::create(['name'=>'Fictional state','is_active'=>1]); $loc=Location::create(['name'=>'Fictional showroom','state_id'=>$state->id,'is_active'=>1]);\App\Models\PickupPoint::create(['name'=>'Fictional showroom branch','address'=>'Fictional road','location_id'=>$loc->id]);
        $r=$this->draft(['intent'=>'submit','tags'=>[(string)$tag->id,'Energy Saver'],'new_tag_options_data'=>[json_encode(['name'=>'Finish','options'=>['Silver']])],
            'sizes'=>[$size->id],'colors'=>[$color->id],'locations'=>[$loc->id],'pickup_points'=>[\App\Models\PickupPoint::where('location_id',$loc->id)->value('id')],'attr_groups'=>[['group'=>'Dimensions','attrs'=>[['key'=>'Width','value'=>'60 cm']]]],
            'installation_options'=>[['label'=>'Install','price'=>3500,'description'=>'Fit and test']],'description_blocks'=>json_encode([['type'=>'heading','text'=>'Highlights','level'=>2]]),
            'requires_truck_set'=>1,'requires_truck'=>1,'weight_kg'=>35,'images'=>[UploadedFile::fake()->image('product.jpg')]]);
        $this->assertSame(1,Tag::count()); $this->assertSame(0,Product::count()); $this->assertSame(1,CatalogueUpload::count());
        $this->approve($r)->assertRedirect(); $p=Product::firstOrFail();
        $this->assertCount(2,$p->tags); $this->assertCount(1,$p->sizes); $this->assertCount(1,$p->colors); $this->assertCount(1,$p->locations);
        $this->assertEquals('60 cm',$p->custom_attributes['Width']); $this->assertEquals(['Silver'],$p->custom_attributes['tag_options']['Finish']);
        $this->assertEquals('Install',$p->installation_options[0]['label']); $this->assertEquals(3500,$p->installation_options[0]['price']);
        $this->assertTrue($p->requires_truck); $this->assertEquals(35,$p->weight_kg); $this->assertCount(1,$p->images); Storage::disk('public')->assertExists($p->images->first()->image_url);
    }
    public function test_live_product_unchanged_until_approval_and_current_brand_manager_is_used(): void {
        $p=$this->live(); $this->post('/catalogue/products/'.$p->id.'/draft')->assertRedirect(); $r=CatalogueRevision::firstOrFail();
        $this->post('/catalogue/products/'.$p->id.'/draft')->assertRedirect(); $this->assertSame(1,CatalogueRevision::count());
        $this->put('/catalogue/submissions/'.$r->id,$this->data(['version'=>0,'intent'=>'submit','price'=>32000]))->assertRedirect();
        $this->assertEquals(10000,$p->fresh()->price); $this->assertTrue($p->fresh()->is_active);
        $new=$this->customer('manager'); Brand::whereKey($this->brand->id)->update(['manager_id'=>$new->id]);
        $this->approve($r)->assertRedirect(); $this->assertEquals(32000,$p->fresh()->price); $this->assertSame($new->id,$p->fresh()->manager_id);
    }
    public function test_other_editors_cannot_read_change_or_withdraw_private_submissions(): void {
        $r=$this->draft(); $this->actingAs($this->customer('catalogue_editor'));
        $this->get('/catalogue/submissions/'.$r->id)->assertNotFound(); $this->putJson('/catalogue/submissions/'.$r->id,$this->data(['version'=>1]))->assertNotFound();
        foreach(['archive','withdraw','refresh'] as $action) $this->postJson('/catalogue/submissions/'.$r->id.'/'.$action,['version'=>1])->assertNotFound();
        $this->get('/catalogue?view=submissions')->assertDontSee('Catalogue fictional appliance'); $this->get('/catalogue?view=history')->assertDontSee('Catalogue fictional appliance');
    }
    public function test_wrong_roles_guests_and_banned_staff_cannot_use_workspace(): void {
        foreach(['user','supplier','manager','admin'] as $role) {
            $this->actingAs($this->customer($role)); $this->get('/catalogue')->assertForbidden(); $this->postJson('/catalogue/submissions',$this->data())->assertForbidden();
            $this->postJson('/catalogue/block-image',['image'=>UploadedFile::fake()->image('x.jpg')])->assertForbidden();
        }
        $this->actingAs($this->editor); $this->editor->update(['status'=>'banned']); $this->get('/catalogue')->assertForbidden();
        auth()->logout(); $this->get('/catalogue')->assertRedirect('/login');
    }
    public function test_editor_cannot_access_admin_manager_orders_settings_or_publication_routes(): void {
        $p=$this->live();
        foreach(['/admin','/admin/users','/admin/orders','/admin/settings','/admin/catalogue','/manager/products','/manager/orders'] as $path) $this->get($path)->assertForbidden();
        $this->patchJson('/admin/products/'.$p->id.'/toggle-active')->assertForbidden(); $this->patchJson('/manager/products/'.$p->id.'/toggle-active')->assertForbidden();
        $this->deleteJson('/admin/products/'.$p->id)->assertForbidden(); $this->assertTrue($p->fresh()->is_active);
    }
    public function test_reject_resubmit_withdraw_and_archive_preserve_history(): void {
        $r=$this->draft(['intent'=>'submit']); $this->approve($r,['decision'=>'return','notes'=>'Please add dimensions'])->assertRedirect();
        $this->assertSame('changes_requested',$r->fresh()->status); $this->assertSame(0,Product::count());
        $this->actingAs($this->editor)->get('/catalogue/submissions/'.$r->id)->assertOk()->assertSee('Please add dimensions');
        $this->put('/catalogue/submissions/'.$r->id,$this->data(['version'=>2,'intent'=>'submit']))->assertRedirect();
        $this->post('/catalogue/submissions/'.$r->id.'/withdraw',['version'=>3])->assertRedirect(); $this->assertSame('draft',$r->fresh()->status);
        $this->post('/catalogue/submissions/'.$r->id.'/archive',['version'=>4])->assertRedirect(); $this->assertSame('archived',$r->fresh()->status); $this->assertSame(5,$r->events()->count());
        $this->putJson('/catalogue/submissions/'.$r->id,$this->data(['version'=>5]))->assertStatus(409);
    }
    public function test_stale_saves_and_duplicate_reviews_do_not_overwrite_or_duplicate(): void {
        $r=$this->draft(['intent'=>'submit']); $this->putJson('/catalogue/submissions/'.$r->id,$this->data(['version'=>0]))->assertStatus(409);
        $this->approve($r)->assertRedirect(); $this->approve($r)->assertStatus(409); $this->assertSame(1,Product::count());
        $this->actingAs($this->editor)->get('/catalogue/submissions/'.$r->id)->assertOk()->assertDontSee('id="product-form"',false);
    }
    public function test_concurrent_live_changes_block_approval_and_refresh_recovers(): void {
        $p=$this->live(); $this->post('/catalogue/products/'.$p->id.'/draft'); $r=CatalogueRevision::firstOrFail();
        $this->put('/catalogue/submissions/'.$r->id,$this->data(['intent'=>'submit']))->assertRedirect(); $p->update(['stock'=>1]);
        $this->approve($r)->assertStatus(409); $this->assertEquals(10000,$p->fresh()->price);
        $this->approve($r,['decision'=>'return'])->assertRedirect();
        $this->actingAs($this->editor)->post('/catalogue/submissions/'.$r->id.'/refresh',['version'=>2])->assertRedirect();
        $this->assertEquals(1,$r->fresh()->payload['stock']); $this->assertSame('draft',$r->fresh()->status);
    }
    public function test_invalid_fields_references_and_unsafe_urls_are_rejected_without_partial_data(): void {
        foreach([['brand_id'=>999999],['price'=>-1],['stock'=>-1],['manager_id'=>$this->manager->id],['is_active'=>1],['sizes'=>[999999]],['description_blocks'=>'{"not":"list"}'],['description_blocks'=>json_encode([['url'=>'javascript:alert(1)']])],['images'=>[UploadedFile::fake()->create('bad.svg',1,'image/svg+xml')]]] as $extra) $this->postJson('/catalogue/submissions',$this->data($extra))->assertStatus(422);
        $this->assertSame(0,CatalogueRevision::count()); $this->assertSame(0,CatalogueUpload::count());
        $sub=Subcategory::create(['name'=>'Type','category_id'=>$this->category->id]);
        $this->postJson('/catalogue/submissions',$this->data())->assertStatus(422);
        $this->draft(['subcategory_id'=>$sub->id]);
    }
    public function test_changed_role_or_deleted_reference_blocks_approval(): void {
        $r=$this->draft(['intent'=>'submit']); $this->editor->update(['role'=>'user']); $this->approve($r)->assertStatus(409); $this->assertSame(0,Product::count());
        $this->editor->update(['role'=>'catalogue_editor']); $this->category->delete(); $this->approve($r)->assertSessionHasErrors('category_id'); $this->assertSame(0,Product::count());
    }
    public function test_gallery_removal_is_scoped_and_applied_only_on_approval(): void {
        $p=$this->live(); $image=$p->images()->create(['image_url'=>'fictional/existing.jpg']); $this->post('/catalogue/products/'.$p->id.'/draft'); $r=CatalogueRevision::firstOrFail();
        $this->putJson('/catalogue/submissions/'.$r->id,$this->data(['remove_images'=>[999999]]))->assertStatus(422);
        $this->put('/catalogue/submissions/'.$r->id,$this->data(['intent'=>'submit','remove_images'=>[$image->id]]))->assertRedirect(); $this->assertCount(1,$p->fresh()->images);
        $this->approve($r)->assertRedirect(); $this->assertCount(0,$p->fresh()->images);
    }
    public function test_search_brand_and_status_filters_and_empty_states(): void {
        $this->live(); $r=$this->draft(); $this->get('/catalogue?search=unmatched')->assertOk()->assertSee('No products match');
        $this->get('/catalogue?brand='.$this->brand->id)->assertSee('Original fictional product');
        $this->get('/catalogue?view=submissions&status=pending')->assertOk()->assertSee('No submissions match');
        $this->get('/catalogue?view=submissions&search=fictional&status=draft')->assertSee('Catalogue fictional appliance');
    }
    public function test_block_image_upload_is_recorded_and_unsafe_files_rejected(): void {
        $this->post('/catalogue/block-image',['image'=>UploadedFile::fake()->image('description.png')])->assertOk()->assertJsonStructure(['url']);
        $upload=CatalogueUpload::firstOrFail(); $this->assertSame($this->editor->id,$upload->editor_id); Storage::disk('public')->assertExists($upload->path);
        $this->postJson('/catalogue/block-image',['image'=>UploadedFile::fake()->create('bad.php',1)])->assertStatus(422);
    }
    public function test_admin_can_create_and_assign_catalogue_editor_without_exposing_admin_access(): void {
        $this->actingAs($this->admin)->get('/admin/users/create')->assertOk()->assertSee('Catalogue Editor');
        $this->post('/admin/users',['name'=>'Fictional Staff','email'=>'fictional-editor@example.invalid','password'=>'TestPassword123!','password_confirmation'=>'TestPassword123!','role'=>'catalogue_editor','status'=>'green','verified'=>1])->assertRedirect();
        $staff=User::where('email','fictional-editor@example.invalid')->firstOrFail(); $this->assertSame('catalogue_editor',$staff->role);
        $this->get('/admin/users/'.$staff->id.'/edit')->assertOk()->assertSee('Catalogue Editor');
        $this->get('/admin/users?role=catalogue_editor')->assertOk()->assertSee('Fictional Staff');
    }
    public function test_manager_cannot_publish_through_forms_or_api_and_can_request_admin_publication(): void {
        $this->actingAs($this->manager);
        $this->get('/manager/products/create')->assertOk()->assertDontSee('name="is_active"',false);
        $this->postJson('/manager/products',$this->data(['is_active'=>1]))->assertStatus(422);
        $this->post('/manager/products',$this->data())->assertRedirect(); $p=Product::firstOrFail(); $this->assertFalse($p->is_active);
        $this->get('/manager/products')->assertOk()->assertDontSee('product-toggle',false);
        $this->get('/manager/products/'.$p->id.'/edit')->assertOk()->assertDontSee('product-toggle',false)->assertSee('Send to admin');
        $this->patchJson('/manager/products/'.$p->id.'/toggle-active')->assertForbidden();
        $this->putJson('/manager/products/'.$p->id,$this->data(['is_active'=>1]))->assertStatus(422);
        $this->post('/manager/products/'.$p->id.'/submit')->assertRedirect(); $r=CatalogueRevision::firstOrFail(); $this->assertSame('pending',$r->status);
        $this->postJson('/manager/products/'.$p->id.'/submit')->assertStatus(409);
        $this->approve($r)->assertRedirect(); $this->assertTrue($p->fresh()->is_active);
        $this->actingAs($this->manager)->patchJson('/manager/products/'.$p->id.'/toggle-active')->assertForbidden(); $this->assertTrue($p->fresh()->is_active);
    }
    public function test_manager_cannot_submit_foreign_brand_product(): void {
        $p=$this->live(); $this->actingAs($this->customer('manager'))->postJson('/manager/products/'.$p->id.'/submit')->assertNotFound(); $this->assertSame(0,CatalogueRevision::count());
    }
}
