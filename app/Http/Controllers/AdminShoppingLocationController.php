<?php
namespace App\Http\Controllers;
use App\Models\{PickupPoint,Location,Product,StoreLocation};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AdminShoppingLocationController extends Controller {
 public function __construct(){$this->middleware(['auth','role:admin']);}
 public function index(){
  $points=PickupPoint::with('state')->orderBy('location_id')->orderBy('name')->get();$areas=Location::with('state')->orderBy('name')->get();$stores=StoreLocation::orderBy('name')->get();
  $routes=DB::table('origin_delivery_areas')->where('is_active',true)->get()->groupBy('pickup_point_id');
  $unassigned=Product::where('is_active',true)->whereDoesntHave('pickupPoints')->with('locations')->orderBy('name')->get();
  $rates=DB::table('collection_transfer_rates')->orderBy('origin_id')->orderBy('destination_id')->get();
  $products=Product::withoutGlobalScope('shopping_location')->orderBy('name')->get(['id','name']);
  return view('admin.shopping-locations',compact('points','areas','routes','stores','unassigned','rates','products'));
 }
 public function transferRate(Request $r){
  $v=$r->validate(['origin_id'=>'required|integer|exists:pickup_points,id','destination_id'=>'required|integer|different:origin_id|exists:pickup_points,id','product_id'=>'nullable|integer|exists:products,id','fee_ngn'=>'required|numeric|min:0|max:9999999999.99']);
  DB::transaction(function()use($v){$q=DB::table('collection_transfer_rates')->where('origin_id',$v['origin_id'])->where('destination_id',$v['destination_id']);empty($v['product_id'])?$q->whereNull('product_id'):$q->where('product_id',$v['product_id']);$q->delete();DB::table('collection_transfer_rates')->insert($v+['created_at'=>now(),'updated_at'=>now()]);});
  return redirect()->route('admin.shopping-locations.index',['tab'=>'transfers'])->with('success','Directional transfer price per unit saved.');
 }
 public function updateTransferRate(Request $r,int $rate){
  abort_unless(DB::table('collection_transfer_rates')->where('id',$rate)->exists(),404);
  $v=$r->validate(['fee_ngn'=>'required|numeric|min:0|max:9999999999.99']);
  DB::table('collection_transfer_rates')->where('id',$rate)->update($v+['updated_at'=>now()]);
  return redirect()->route('admin.shopping-locations.index',['tab'=>'transfers'])->with('success','Transfer price updated. Existing orders keep their original charges.');
 }
 public function deleteTransferRate(int $rate){
  abort_unless(DB::table('collection_transfer_rates')->where('id',$rate)->delete(),404);
  return redirect()->route('admin.shopping-locations.index',['tab'=>'transfers'])->with('success','Transfer price deleted. Routes without a price require a quote. Existing orders are unchanged.');
 }
 public function update(Request $r,PickupPoint $point){
  $v=$r->validate(['name'=>'required|string|max:255','address'=>'required|string|max:500','hours'=>'nullable|string|max:255','state_id'=>'required|integer|exists:states,id','is_active'=>'required|boolean','store_location_id'=>'nullable|integer|exists:store_locations,id','destinations'=>'nullable|array|max:1000','destinations.*'=>'integer|distinct|exists:locations,id']);
  DB::transaction(function()use($v,$point){
   if((int)$point->state_id!==(int)$v['state_id'] && DB::table('product_pickup_point')->where('pickup_point_id',$point->id)->exists()) \App\Services\ShoppingLocation::fail('state_id','Create a new store and review product assignments before changing state.');
   $point->update(collect($v)->except('destinations')->all());
   if($point->store_location_id)StoreLocation::whereKey($point->store_location_id)->update(collect($v)->only('name','address','hours')->all());

  });
  return back()->with('success','Store details saved. Delivery areas are managed in Delivery settings.');
 }
}
