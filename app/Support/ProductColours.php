<?php
namespace App\Support;
use App\Models\Product;
use Illuminate\Validation\ValidationException;
class ProductColours {
 public static function options(Product $product): array {
  return $product->colors->sortBy('name')->map(fn($c)=>['id'=>(int)$c->id,'name'=>$c->name,'swatch'=>self::swatch($c->name)])->values()->all();
 }
 public static function resolve(Product $product, ?int $id): ?array {
  $options=self::options($product);
  if(!$id && count($options)===1)return $options[0];
  if(!$id && !$options)return null;
  foreach($options as $option)if($option['id']===$id)return $option;
  throw ValidationException::withMessages(['items'=>'Choose an available colour for '.$product->name.'. Open the product to select its colour.']);
 }
 public static function swatch(string $name): string {
  $map=['black'=>'#202124','white'=>'#ffffff','red'=>'#a81824','burgundy'=>'#800020','maroon'=>'#800000','silver'=>'#bdc3c7','grey'=>'#808080','gray'=>'#808080','blue'=>'#2563eb','navy'=>'#172554','green'=>'#15803d','yellow'=>'#facc15','gold'=>'#d4af37','rose gold'=>'#b76e79','pink'=>'#ec4899','purple'=>'#9333ea','orange'=>'#f97316','brown'=>'#795548','cream'=>'#fffdd0','beige'=>'#f5f5dc','stainless steel'=>'#b4bdc2','inox'=>'#b4bdc2'];
  return $map[strtolower(trim($name))] ?? 'linear-gradient(135deg,#eee 45%,#777 46%,#777 54%,#eee 55%)';
 }
}
