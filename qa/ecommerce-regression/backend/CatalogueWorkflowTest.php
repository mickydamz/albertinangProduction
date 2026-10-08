<?php
namespace Tests\Regression;
use App\Models\{Category, Subcategory, Product};
class CatalogueWorkflowTest extends CriticalTestCase
{
    private function preview($response, string $name): void
    {
        if ($directory=getenv('CATALOGUE_PREVIEW_DIR')) {
            if (!is_dir($directory)) mkdir($directory,0700,true);
            file_put_contents($directory.'/'.$name,str_replace('http://localhost','http://127.0.0.1:8000',$response->getContent()));
        }
    }

    public function test_product_creation_and_editing_validate_category_price_and_preserve_values(): void
    {
        $category=Category::create(['name'=>'Appliances','is_active'=>true]);
        $other=Category::create(['name'=>'Electronics','is_active'=>true]);
        $sub=Subcategory::create(['name'=>'Televisions','category_id'=>$other->id]);
        $this->actingAs($this->customer('admin'));
        $input=['name'=>'Catalogue test blender','price'=>45000,'stock'=>3,'category_id'=>$category->id,'is_active'=>0];
        $this->post('/admin/products',array_merge($input,['price'=>-1,'subcategory_id'=>$sub->id]))->assertSessionHasErrors(['price','subcategory_id']);
        $this->post('/admin/products',$input)->assertSessionHasNoErrors()->assertRedirect();
        $product=Product::where('name',$input['name'])->firstOrFail();
        $this->assertFalse((bool)$product->is_active);
        $this->put('/admin/products/'.$product->id,array_merge($input,['price'=>60000,'stock'=>8,'is_active'=>1]))->assertSessionHasNoErrors();
        $this->assertEquals(60000,$product->fresh()->price);
        $this->assertSame(8,$product->fresh()->stock);
        $this->preview($this->get('/admin/products/create')->assertOk()->assertSee('Product essentials'), 'product-editor.html');
        $this->get('/admin/products/'.$product->id.'/edit')->assertOk();
    }
    public function test_blank_optional_moq_defaults_to_one_on_create_and_edit(): void
    {
        $category=Category::create(['name'=>'MOQ category','is_active'=>true]);
        $this->actingAs($this->customer('admin'));
        $input=['name'=>'Blank MOQ product','price'=>1000,'stock'=>1,'category_id'=>$category->id,'moq'=>'','is_active'=>0];
        $this->post('/admin/products',$input)->assertSessionHasNoErrors()->assertRedirect();
        $product=Product::where('name',$input['name'])->firstOrFail();
        $this->assertEquals(1,$product->moq);
        $this->put('/admin/products/'.$product->id,array_merge($input,['moq'=>3]))->assertSessionHasNoErrors();
        $this->assertEquals(3,$product->fresh()->moq);
        $this->put('/admin/products/'.$product->id,$input)->assertSessionHasNoErrors();
        $this->assertEquals(1,$product->fresh()->moq);
    }

    public function test_keyword_pages_render_inside_admin_layout_with_editor_scripts(): void
    {
        $tag=\App\Models\Tag::create(['name'=>'Layout keyword','slug'=>'layout-keyword']);
        $this->actingAs($this->customer('admin'));
        foreach(['/admin/tags/create','/admin/tags/'.$tag->id.'/edit','/admin/tags/'.$tag->id] as $path) {
            $html=$this->get($path)->assertOk()->getContent();
            $this->assertLessThan(strpos($html,'class="content-header-title'),strpos($html,'id="alHamburger"'));
            $this->assertStringStartsNotWith("\xEF\xBB\xBF",$html);
            if ($path!='/admin/tags/'.$tag->id) $this->assertStringContainsString('function createOptionRow',$html);
        }
    }

    public function test_brand_name_is_consistent_in_customer_pages_and_email_subjects(): void
    {
        foreach(['/about','/terms','/contact','/products'] as $path) {
            $html=$this->get($path)->assertOk()->getContent();
            $this->assertStringContainsString('AlbertinaNG',$html);
            $this->assertStringNotContainsString('Albertina Nigeria',$html);
            $this->assertStringNotContainsString('AlbertinaNG Limited',$html);
        }
        $order=$this->order($this->customer());
        $mail=new \App\Mail\OrderShipped($order);
        $this->assertStringContainsString('AlbertinaNG',$mail->envelope()->subject);
    }

    public function test_combined_filters_and_sort_links_preserve_unpublished_status(): void
    {
        $a=$this->product();$a->update(['name'=>'Low stock draft','stock'=>2,'price'=>1000,'is_active'=>false]);
        $b=$this->product();$b->update(['name'=>'Published wrong stock','stock'=>20,'price'=>1000]);
        $this->actingAs($this->customer('admin'));
        $this->preview($this->get('/admin/products?status=0&inventory=low_stock&min_price=500&max_price=1500&per_page=10')->assertOk()->assertSee('Low stock draft')->assertDontSee('Published wrong stock')->assertSee('status=0',false)->assertSee('inventory=low_stock',false), 'product-filters.html');
        $this->get('/admin/products?max_price=1500')->assertOk();
        $this->get('/admin/products?min_price=200&max_price=100')->assertSessionHasErrors('max_price');
    }
    public function test_purchased_products_are_unpublished_instead_of_deleting_history(): void
    {
        $p=$this->product();$o=$this->order($this->customer(),'completed','paystack',$p);
        $this->actingAs($this->customer('admin'))->delete('/admin/products/'.$p->id)->assertRedirect();
        $this->assertDatabaseHas('products',['id'=>$p->id,'is_active'=>false]);
        $this->assertDatabaseHas('order_items',['order_id'=>$o->id,'product_id'=>$p->id]);
    }
    public function test_supplier_and_affiliate_endpoints_are_removed(): void
    {
        $this->actingAs($this->customer('admin'));
        foreach(['/admin/suppliers','/admin/affiliates','/supplier/dashboard','/affiliate/dashboard','/suppliers'] as $path) $this->get($path)->assertNotFound();
        $this->post('/admin/suppliers',['name'=>'Forbidden supplier'])->assertNotFound();
        $this->get('/admin/products')->assertOk()->assertDontSee('Products &amp; suppliers',false);
    }
    public function test_customer_brand_filter_keeps_price_stock_and_sort_instead_of_redirecting(): void
    {
        $a=$this->product();$a->update(['name'=>'Match filtered appliance','brand'=>'Test Brand','price'=>10000,'stock'=>2]);
        $b=$this->product();$b->update(['name'=>'Too expensive appliance','brand'=>'Test Brand','price'=>30000,'stock'=>2]);
        $this->preview($this->get('/products?brands[]=Test%20Brand&max_price=15000&availability[]=in-stock&sort_by=price-asc')->assertOk()->assertSee('Match filtered appliance')->assertDontSee('Too expensive appliance'), 'customer-filters.html');
    }

    public function test_product_edits_do_not_rewrite_previous_order_prices_and_customers_cannot_manage_products(): void
    {
        $category=Category::create(['name'=>'Order price category','is_active'=>true]);
        $p=$this->product();$p->update(['category_id'=>$category->id]);
        $user=$this->customer();$o=$this->order($user,'completed','paystack',$p);
        $this->actingAs($user)->get('/admin/products/create')->assertForbidden();
        $this->actingAs($this->customer('admin'))->put('/admin/products/'.$p->id,['name'=>$p->name,'price'=>25000,'stock'=>5,'category_id'=>$category->id,'is_active'=>1])->assertSessionHasNoErrors();
        $this->assertEquals(10000,$o->items()->first()->price);
        $this->assertEquals(25000,$p->fresh()->price);
        $p->update(['name'=>'Hidden unpublished item','is_active'=>false]);
        $this->get('/products')->assertOk()->assertDontSee('Hidden unpublished item');
    }
    public function test_image_uploads_persist_and_too_many_images_are_rejected(): void
    {
        $category=Category::create(['name'=>'Image test category','is_active'=>true]);
        $this->actingAs($this->customer('admin'));
        $png=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=');
        $image=\Illuminate\Http\UploadedFile::fake()->createWithContent('product.png',$png);
        $input=['name'=>'Image test product','price'=>10000,'stock'=>1,'category_id'=>$category->id,'images'=>[$image]];
        $this->post('/admin/products',$input)->assertSessionHasNoErrors();
        $p=Product::where('name','Image test product')->firstOrFail();
        $this->assertSame(1,$p->images()->count());
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($p->images()->first()->image_url);
        $input['images']=array_fill(0,11,$image);
        $this->post('/admin/products',$input)->assertSessionHasErrors('images');
        $this->assertSame(1,Product::where('name','Image test product')->count());
    }
}
