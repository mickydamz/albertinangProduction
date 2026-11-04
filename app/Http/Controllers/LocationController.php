<?php
namespace App\Http\Controllers;
use App\Models\{Location,PickupPoint,State};
use App\Services\ShoppingLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class LocationController extends Controller {
 private function areas(){
  $state=ShoppingLocation::state();
  return Location::where('is_active',true)->where('state_id',$state?->id ?? 0)->whereHas('state',fn($q)=>$q->where('is_active',true)->where('delivery_enabled',true));
 }
 public function index(Request $r){
  if($r->filled('for_pickup')){ $s=ShoppingLocation::state();return response()->json($s?[['id'=>$s->id,'name'=>$s->name]]:[]); }
  $q=$this->areas();if($r->filled('state_id'))$q->where('state_id',$r->query('state_id'));
  return response()->json($q->orderBy('name')->get(['id','name','shipping_cost','truck_shipping_cost']));
 }
 public function states(){return response()->json(State::whereIn('id',$this->areas()->select('state_id'))->orderBy('name')->get(['id','name']));}
 public function pickupPoints(Request $r){
  $v=$r->validate(['items'=>'nullable|array|max:100','items.*'=>'integer']);$ids=$v['items']??[];
  return response()->json(PickupPoint::where('is_active',true)->where('state_id',ShoppingLocation::state()?->id ?? 0)->whereHas('state',fn($q)=>$q->where('is_active',true))->with('state')->orderBy('name')->get()->map(function($p)use($ids){
   $quotes=[];$message=null;
   foreach($ids as $id){try{$q=\App\Services\CollectionTransfer::quote((int)$id,$p);$product=\App\Models\Product::withoutGlobalScope('shopping_location')->find($id);$quotes[]=$q+['product_id'=>(int)$id,'product_name'=>$product->name];}catch(\Illuminate\Validation\ValidationException $e){$message=collect($e->errors())->flatten()->first();break;}}
   return ['id'=>$p->id,'location_id'=>$p->location_id,'name'=>$p->state->name.' — '.$p->name,'address'=>$p->address,'hours'=>$p->hours,'available'=>$message===null,'unavailable_message'=>$message,'transfers'=>$quotes];
  }))->header('Cache-Control','private, no-store');
 }
}
