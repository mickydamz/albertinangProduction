<?php
namespace App\Services;
use App\Models\{Product,Location,PickupPoint,Setting};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShoppingLocation {
 public static function point(?int $id=null): ?PickupPoint {
  $id=$id ?? (request()->hasSession() ? (int)request()->session()->get('shopping_point_id') : 0);
  if(!$id)return null;
  return PickupPoint::whereKey($id)->where('is_active',true)->whereHas('state',fn($q)=>$q->where('is_active',true))->with('state','location')->first();
 }
 public static function state(): ?\App\Models\State {
  if(!request()->hasSession())return null;
  $id=(int)request()->session()->get('shopping_state_id');
  // Retain an existing visitor's state when moving from branch-based browsing.
  if(!$id)$id=(int)self::point()?->state_id;
  return $id ? \App\Models\State::where('is_active',true)->find($id) : null;
 }
 public static function applyState($query,?int $stateId=null): void {
  $stateId=$stateId ?? self::state()?->id;
  $query->where('products.is_active',true);

 }
 public static function unavailable(array $ids): array {
  $ids=array_values(array_unique(array_map('intval',$ids)));
  $q=Product::withoutGlobalScope('shopping_location')->whereIn('products.id',$ids);self::applyState($q);
  return array_values(array_diff($ids,$q->pluck('products.id')->map(fn($id)=>(int)$id)->all()));
 }
 public static function candidates(array $ids){
  $q=PickupPoint::where('is_active',true)->where('state_id',self::state()?->id)->with('state')->orderBy('name');
  foreach(array_unique(array_map('intval',$ids)) as $id)$q->whereHas('products',fn($p)=>$p->where('products.id',$id)->where('products.is_active',true));
  return $q->get();
 }
 public static function apply($query,?PickupPoint $point): void {
  $query->where('products.is_active',true);
  if(!$point){$query->whereRaw('1=0');return;}
  $query->whereHas('pickupPoints',fn($q)=>$q->where('pickup_points.id',$point->id));
 }
 public static function assertItems(array $ids,PickupPoint $point): void {
  $ids=array_values(array_unique(array_map('intval',$ids)));
  if(!$ids)self::fail('items','Your cart is empty.');
  $q=Product::withoutGlobalScope('shopping_location')->whereIn('products.id',$ids);self::apply($q,$point);
  if($q->count()!==count($ids))self::fail('items','Some products are hidden or unavailable from your selected store. Review your cart or change location.');
 }
 public static function resolve(array $f,array $ids,bool $truck,?PickupPoint $point=null): array {
  $method=$f['method']??null;
  if($method==='pickup'){
   if(Setting::get('pickup_enabled','1')!=='1')self::fail('fulfillment.method','Collection is currently unavailable.');
   $staffPoint=$point!==null;
   $state=self::state();if(!$staffPoint && !$state)self::fail('shopping_location','Select your collection state before choosing a collection point.');
   $point=self::point((int)($f['pickup_point_id']??0));
   if(!$staffPoint && $point && (int)$point->state_id!==(int)$state->id)self::fail('fulfillment.pickup_point_id','Choose a collection point in your selected collection state.');
   if(!$point || !trim((string)$point->address))self::fail('fulfillment.pickup_point_id','Choose an active collection point with a configured address.');
   $transfers=[];foreach(array_unique($ids) as $id)$transfers[$id]=CollectionTransfer::quote((int)$id,$point);
   return ['method'=>'pickup','shopping_location_id'=>$point->location_id,'shopping_location_name'=>$point->state->name,'dispatch_point_id'=>$point->id,'dispatch_point_name'=>$point->name,'dispatch_point_address'=>$point->address,'pickup_point_id'=>$point->id,'pickup_point_name'=>$point->name,'pickup_point_address'=>$point->address,'pickup_location_id'=>$point->location_id,'pickup_location'=>$point->state->name,'delivery_fee_ngn'=>0,'collection_transfers'=>$transfers];
  }
  if(!$point){
   if(!self::state())self::fail('shopping_location','Select your state before checkout.');
   if(self::unavailable($ids))self::fail('items','Some products are unavailable in your selected state. Remove the affected products to proceed.');
   if($method==='pickup')$point=self::candidates($ids)->firstWhere('id',(int)($f['pickup_point_id']??0));
   elseif($method==='delivery')$point=!empty($f['dispatch_point_id'])?self::point((int)$f['dispatch_point_id']):self::candidates($ids)->first();
   if($point && (int)$point->state_id!==(int)self::state()->id)self::fail('fulfillment.dispatch_point_id','Choose a store in your selected state.');
   if(!$point)self::fail('fulfillment.pickup_point_id',$method==='pickup'?'Choose a collection point in your state that has every item in your basket.':'No store in your selected state can deliver this basket to the chosen area.');
  }
  $transfers=[];foreach(array_unique($ids) as $id)$transfers[$id]=CollectionTransfer::quote((int)$id,$point);
  if(!in_array($method,['pickup','delivery'],true))self::fail('fulfillment.method','Choose collection or delivery.');
  if(Setting::get($method==='pickup'?'pickup_enabled':'delivery_enabled','1')!=='1')self::fail('fulfillment.method','This fulfilment method is currently unavailable.');
  $data=['method'=>$method,'shopping_location_id'=>$point->location_id,'shopping_location_name'=>$point->state->name,'dispatch_point_id'=>$point->id,'dispatch_point_name'=>$point->name,'dispatch_point_address'=>$point->address,'delivery_fee_ngn'=>0];
  if($method==='pickup'){
   if((int)($f['pickup_point_id']??0)!==(int)$point->id)self::fail('fulfillment.pickup_point_id','Choose a collection point that can fulfil this basket.');
   if(!trim((string)$point->address))self::fail('fulfillment.pickup_point_id','The store collection address has not been configured.');
   return $data+['pickup_point_id'=>$point->id,'pickup_point_name'=>$point->name,'pickup_point_address'=>$point->address,'pickup_location_id'=>$point->location_id,'pickup_location'=>$point->state->name];
  }
  $destination=Location::whereKey((int)($f['delivery_location_id']??0))->where('is_active',true)->whereHas('state',fn($q)=>$q->where('is_active',true)->where('delivery_enabled',true))->with('state')->first();
  if(!$destination || (int)$destination->state_id!==(int)$point->state_id)self::fail('fulfillment.delivery_location_id','This destination is not served by your selected store.');
  $rate=$truck?$destination->truck_shipping_cost:$destination->shipping_cost;
  if($rate===null || $rate==='' || !is_numeric($rate) || (float)$rate<0)self::fail('fulfillment.delivery_location_id','The delivery charge is not configured. Please choose another destination.');
  $address=trim((string)($f['shipping_address']??''));if($address==='')self::fail('fulfillment.shipping_address','Enter your delivery address.');
  $data['delivery_fee_ngn']=round((float)$rate,2);$data['area_delivery_fee_ngn']=$data['delivery_fee_ngn'];$data['collection_transfers']=$transfers;
  return $data+['delivery_location_id'=>$destination->id,'delivery_location_name'=>$destination->name,'delivery_state_id'=>$destination->state_id,'delivery_state_name'=>$destination->state->name,'shipping_address'=>$address];
 }
 public static function validateAssignments(array $locations,array $points,bool $publishing=false): array {
  $points=array_values(array_unique(array_map('intval',$points)));
  if(count($points)!==PickupPoint::whereIn('id',$points)->whereNotNull('state_id')->count())self::fail('pickup_points','Choose existing stores with a state assigned.');
  if($publishing && !$points)self::fail('pickup_points','Assign at least one store before publication.');
  return $points;
 }
 public static function fail(string $key,string $message): void {throw ValidationException::withMessages([$key=>$message]);}
}
