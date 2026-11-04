<?php
namespace App\Http\Controllers;
use App\Models\{Product,Brand,CatalogueRevision};
use App\Services\{CatalogueWorkflow,CatalogueProductData};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ManagerCatalogueSubmissionController extends Controller {
    public function submit(Request $request,Product $product,CatalogueWorkflow $workflow) {
        abort_unless(auth()->user()->role === 'manager' && auth()->user()->status !== 'banned',403);
        DB::transaction(function () use ($request,$product,$workflow) {
            $brand=Brand::whereKey($product->brand_id)->lockForUpdate()->firstOrFail();
            $product=Product::lockForUpdate()->findOrFail($product->id);
            abort_unless($product->manager_id === auth()->id() && $brand->manager_id === auth()->id(),404);
            abort_if($product->is_active,409,'This product is already published.');
            abort_if(CatalogueRevision::where('editor_id',auth()->id())->where('product_id',$product->id)->where('status','pending')->exists(),409,'This product is already awaiting approval.');
            $data=$workflow->productPayload($product);
            $input=$data; $input['description_blocks']=isset($data['description_blocks']) ? json_encode($data['description_blocks']) : null;
            $input+=['version'=>0,'intent'=>'submit'];
            app(CatalogueProductData::class)->validate(new Request($input));
            $snapshot=$workflow->snapshot($product);
            $revision=CatalogueRevision::create(['editor_id'=>auth()->id(),'product_id'=>$product->id,'payload'=>$data,'status'=>'pending','version'=>1,
                'base_snapshot'=>$snapshot,'base_fingerprint'=>$workflow->fingerprint($snapshot),'submitted_at'=>now()]);
            $workflow->record($revision,'submitted','Brand manager requested publication.');
        });
        return redirect()->route('manager.products.edit',$product)->with('success','Sent to admin for approval. The product remains unpublished.');
    }
}
