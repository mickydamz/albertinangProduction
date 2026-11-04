<?php
namespace App\Http\Middleware;
use Closure;
use App\Services\ShoppingLocation;
class ShoppingContext {
 public function handle($request,Closure $next){
  // Request-local marker: staff screens, webhooks and historical order fulfilment
  // remain unscoped. The model closure reads this marker at query time.
  $scoped=$request->is('/','products','products/*','product/*','category/*','brand/*','search','search/*','search-suggestions','cart','cart/*','checkout','api/cart/*','api/product/*','api/installation-options');
  $request->attributes->set('shopping_catalogue',$scoped);
  if (!$request->session()->has('shopping_point_id') && $request->cookie('shopping_point_id')) $request->session()->put('shopping_point_id',(int)$request->cookie('shopping_point_id'));
  if (!$request->session()->has('shopping_state_id') && $request->cookie('shopping_state_id')) $request->session()->put('shopping_state_id',(int)$request->cookie('shopping_state_id'));
  $state=ShoppingLocation::state();if($state)$request->session()->put('shopping_state_id',$state->id);
  $point=ShoppingLocation::point();$request->attributes->set('shopping_point',$point);
  if(!$point)$request->session()->forget('shopping_point_id');
  $response=$next($request);
  if($scoped || $request->is('shopping/*','api/locations','api/pickup-points','api/states'))$response->headers->set('Cache-Control','private, no-store');
  return $response;
 }
}
