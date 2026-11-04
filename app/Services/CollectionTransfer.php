<?php
namespace App\Services;
use App\Models\{Product,PickupPoint};
use Illuminate\Support\Facades\DB;
class CollectionTransfer {
 // Availability is an assignment flag, never a stock quantity. Prefer local,
 // otherwise the cheapest configured supplying point; stable ID breaks ties.
 public static function quote(int $productId, PickupPoint $destination): array {
  $product=Product::withoutGlobalScope('shopping_location')->where('is_active',true)->find($productId);
  if(!$product)ShoppingLocation::fail('items','A basket product is no longer published.');
  $sources=$product->pickupPoints()->where('pickup_points.is_active',true)->whereHas('state',fn($q)=>$q->where('is_active',true))->orderBy('pickup_points.id')->get();
  $local=$sources->firstWhere('id',$destination->id);
  if($local)return self::snapshot($local,0,true);
  $best=null;
  foreach($sources as $source){
   $rates=DB::table('collection_transfer_rates')->where('origin_id',$source->id)->where('destination_id',$destination->id)->where(fn($q)=>$q->whereNull('product_id')->orWhere('product_id',$productId))->orderByRaw('product_id IS NULL')->orderByDesc('id')->first();
   if(!$rates || !is_numeric($rates->fee_ngn) || $rates->fee_ngn<0)continue;
   if($best===null || (float)$rates->fee_ngn<$best['transfer_unit_fee_ngn'])$best=self::snapshot($source,(float)$rates->fee_ngn,false);
  }
  if(!$best)ShoppingLocation::fail('items', $product->name.': transfer to '.$destination->name.' requires a quote. Choose another collection point or contact us before payment.');
  return $best;
 }
 public static function applyItems(array &$items,array &$fulfillment): void {
  $total=0;foreach($items as &$item){$q=$fulfillment['collection_transfers'][$item['product_id']??$item['id']]??[];$unit=(int)round(($q['transfer_unit_fee_ngn']??0)*100);$item=array_merge($item,$q,['transfer_total_ngn'=>$unit*$item['quantity']/100]);$total+=$unit*$item['quantity'];}unset($item);
  $fulfillment['delivery_fee_ngn']+=$total/100;
  unset($fulfillment['collection_transfers']);
 }
 public static function revalidate($pending): void {
  if(!in_array($pending->fulfillment['method']??null,['pickup','delivery'],true))return;
  $f=ShoppingLocation::resolve($pending->fulfillment,array_column($pending->items,'id'),(bool)($pending->fulfillment['requires_truck']??false));
  foreach($pending->items as $item){$q=$f['collection_transfers'][$item['id']];
   if((int)($item['supplying_point_id']??0)!==(int)$q['supplying_point_id'] || (int)round(($item['transfer_unit_fee_ngn']??0)*100)!==(int)round($q['transfer_unit_fee_ngn']*100))ShoppingLocation::fail('fulfillment','Collection availability or transfer prices changed. Please restart checkout.');
  }
  $items=$pending->items;self::applyItems($items,$f);
  if((int)round($f['delivery_fee_ngn']*100)!==(int)round(($pending->fulfillment['delivery_fee_ngn']??0)*100))ShoppingLocation::fail('fulfillment','Transport charges changed. Please restart checkout.');
 }
 private static function snapshot($point,$fee,$local){return ['supplying_point_id'=>$point->id,'supplying_point_name'=>$point->name,'transfer_unit_fee_ngn'=>round($fee,2),'available_locally'=>$local];}
}
