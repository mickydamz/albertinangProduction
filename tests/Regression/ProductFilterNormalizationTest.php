<?php
namespace Tests\Regression;
use App\Support\ProductFilterNormalizer as N;
class ProductFilterNormalizationTest extends CriticalTestCase
{
    public function test_equivalent_labels_merge_without_losing_detailed_versions(): void
    {
        $this->assertSame('4K Ultra HD',N::value('Resolution','3840 x 2160 (4K)'));
        $this->assertSame('4K Ultra HD',N::value('Display Resolution','4K Ultra HD (3840 x 2160)'));
        $this->assertSame('2 ports',N::value('USB Ports','2 x USB 2.0'));
        $this->assertSame('60 Hz',N::value('Refresh Rate','60Hz Native'));
        $this->assertSame('Silver',N::value('Colour','Premium Metallic Silver'));
        $this->assertSame(N::value('Refrigerant Gas','R600a'),N::value('Refrigerant Gas','R600a (Eco-Friendly / Environmentally Safe)'));
        $this->assertSame('configuration',N::key('Type'));
        $this->assertSame('10 kg',N::value('Load Capacity','10.0kg'));
        $this->assertSame('200 litres',N::value('Capacity','200 L'));
        $this->assertSame('10.2 kg',N::value('Load Capacity','10.2kg'));
        $this->assertSame('VIDAA U8.5',N::value('Operating System','VIDAA U8.5'));
        $this->assertSame('Silver / Titanium Gray',N::value('Colour','Silver / Titanium Gray'));
    }
    public function test_merged_options_count_products_once_and_match_legacy_links(): void
    {
        $a=$this->product(); $a->update(['name'=>'Facet first TV','custom_attributes'=>['Resolution'=>['3840 x 2160 (4K)','4K Ultra HD (3840 x 2160)'],'USB Ports'=>['2 x USB','2 x USB 2.0'],'Refresh Rate'=>['60Hz Native']]]);
        $b=$this->product(); $b->update(['name'=>'Facet second TV','custom_attributes'=>['display_resolution'=>['4K'],'usb_ports'=>['2'],'refresh_rate'=>['60 Hz']]]);
        foreach (['Third','Fourth'] as $name) { $p=$this->product(); $p->update(['name'=>$name.' TV','custom_attributes'=>['Resolution'=>['Full HD (1920 x 1080)']]]); }
        $response=$this->get('/products')->assertOk();
        $options=$response->viewData('customOptionsForFilter');
        $counts=$response->viewData('customCounts');
        $this->assertSame(['4K Ultra HD','Full HD (1080p)'],$options['resolution']);
        $this->assertSame(2,$counts['resolution']['4K Ultra HD']);
        $this->assertSame(2,$counts['usb_ports']['2 ports']);
        $this->get('/products?options[Resolution][]=3840%20x%202160%20(4K)')->assertOk()->assertSee('Facet first TV')->assertSee('Facet second TV');
        $this->assertArrayHasKey('Resolution',$a->fresh()->custom_attributes);
        $this->assertSame('3840 x 2160 (4K)',$a->fresh()->custom_attributes['Resolution'][0]);
    }

    public function test_standard_colours_and_specifications_share_one_count_and_size_edit_works(): void
    {
        $colour=\App\Models\Color::create(['name'=>'Silver']);
        $a=$this->product();$a->update(['custom_attributes'=>['Color / Finish'=>['Premium Silver Finish'],'Operating System'=>['VIDAA U8.5']]]);$a->colors()->attach($colour->id);
        $b=$this->product();$b->update(['custom_attributes'=>['colour'=>['Sleek Silver'],'Operating System'=>['VIDAA U7']]]);
        $data=$this->get('/products')->assertOk()->viewData('customCounts');
        $this->assertSame(2,$data['colour']['Silver']);
        $this->assertSame(1,$data['operating_system']['VIDAA U8.5']);
        $this->assertSame(1,$data['operating_system']['VIDAA U7']);
        $size=\App\Models\Size::create(['name'=>'Legacy medium']);
        $this->actingAs($this->customer('admin'))->put('/admin/sizes/'.$size->id,['name'=>'Legacy large'])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('Legacy large',$size->fresh()->name);
    }
    public function test_sound_and_vision_filters_follow_product_type_and_keep_screen_sizes(): void
    {
        $category=\App\Models\Category::create(['name'=>'Sound and Vision','is_active'=>true]);
        $tv=\App\Models\Subcategory::create(['name'=>'Televisions','category_id'=>$category->id]);
        $audio=\App\Models\Subcategory::create(['name'=>'Sound Bar','category_id'=>$category->id]);
        foreach (['55 inches','65 inches'] as $size) {
            $p=$this->product(); $p->update(['category_id'=>$category->id,'subcategory_id'=>$tv->id,'brand'=>'TV Brand','custom_attributes'=>['Screen Size'=>[$size],'Refresh Rate'=>['60Hz Base Architecture']]]);
        }
        foreach (['Audio one','Audio two'] as $name) {
            $p=$this->product(); $p->update(['name'=>$name,'category_id'=>$category->id,'subcategory_id'=>$audio->id,'brand'=>'Audio Brand','custom_attributes'=>['Channels'=>['2.1']]]);
        }
        $legacy=$this->product(); $legacy->update(['name'=>'Legacy TV 55 Inch','category_id'=>$category->id,'subcategory_id'=>null,'custom_attributes'=>['Product Type'=>['Long television description'],'Screen Size'=>['55-Inch (139 cm)']]]);
        $r=$this->get('/category/sound-and-vision')->assertOk();
        $this->assertSame(3,$r->viewData('customCounts')['product_type']['Televisions']);
        $this->assertSame(['55 inches','65 inches'],$r->viewData('customOptionsForFilter')['screen_size']);
        $r=$this->get('/category/sound-and-vision?options[product_type][]=Sound%20Bar')->assertOk()->assertSee('Audio one');
        $this->assertArrayNotHasKey('screen_size',$r->viewData('customOptionsForFilter'));
        $this->assertCount(2,$r->viewData('categoryProducts'));
        $r=$this->get('/category/sound-and-vision?options[product_type][]=Televisions&options[screen_size][]=55%20inches')->assertOk();
        $this->assertCount(2,$r->viewData('categoryProducts'));
        $this->assertSame('Long television description',$legacy->fresh()->custom_attributes['Product Type'][0]);
        $this->assertSame('60 Hz',N::value('Refresh Rate','60Hz Base Architecture'));
        $this->assertSame('1 port',N::value('USB Ports','1 x USB'));
    }
    public function test_all_products_search_and_brand_share_type_filters_without_mixing_capacity_units(): void
    {
        $brand=\App\Models\Brand::create(['name'=>'Facet Brand','slug'=>'facet-brand']);
        $category=\App\Models\Category::create(['name'=>'Appliances','is_active'=>true]);
        $washer=\App\Models\Subcategory::create(['name'=>'Washing Machines','category_id'=>$category->id]);
        $freezer=\App\Models\Subcategory::create(['name'=>'Chest Freezers','category_id'=>$category->id]);
        foreach ([$washer,$freezer] as $type) {
            foreach ([1,2] as $n) {
                $p=$this->product(); $p->update(['name'=>'Facet appliance '.$type->name.' '.$n,'category_id'=>$category->id,'subcategory_id'=>$type->id,'brand'=>$brand->name,'brand_id'=>$brand->id,'custom_attributes'=>[($type->id===$washer->id && $n===2 ? 'Washing Capacity' : 'Capacity')=>[$type->id===$washer->id ? ($n===2 ? '8.0kg' : '8 kg') : '200 litres'],'Product Type'=>['Misleading free text']]]);
            }
        }
        foreach (['/products','/search?searchTerm=Facet','/brand/facet-brand'] as $url) {
            $r=$this->get($url)->assertOk();
            $this->assertSame(2,$r->viewData('customCounts')['product_type']['Washing Machines']);
            $this->assertNotContains('Misleading free text',$r->viewData('customOptionsForFilter')['product_type']);
            $url .= str_contains($url,'?') ? '&' : '?';
            $r=$this->get($url.'options[product_type][]=Washing%20Machines')->assertOk();
            $this->assertSame(['8 kg'],$r->viewData('customOptionsForFilter')['load_capacity']);
            $this->assertArrayNotHasKey('capacity',$r->viewData('customOptionsForFilter'));
        }
    }
    public function test_publishing_requires_valid_product_type_but_drafts_remain_allowed(): void
    {
        $category=\App\Models\Category::create(['name'=>'Typed appliances','is_active'=>true]);
        $type=\App\Models\Subcategory::create(['name'=>'Washing Machines','category_id'=>$category->id]);
        $this->actingAs($this->customer('admin'));
        $input=['name'=>'New typed washer','price'=>45000,'stock'=>3,'category_id'=>$category->id,'is_active'=>1];
        $this->post('/admin/products',$input)->assertSessionHasErrors('subcategory_id');
        $this->post('/admin/products',array_merge($input,['is_active'=>0]))->assertSessionHasNoErrors();
        $p=\App\Models\Product::where('name',$input['name'])->firstOrFail();
        $this->patch('/admin/products/'.$p->id.'/toggle-active')->assertSessionHasErrors('subcategory_id');
        $this->assertFalse((bool)$p->fresh()->is_active);
        $this->put('/admin/products/'.$p->id,array_merge($input,['subcategory_id'=>$type->id]))->assertSessionHasNoErrors();
        $this->assertTrue((bool)$p->fresh()->is_active);
    }
}
