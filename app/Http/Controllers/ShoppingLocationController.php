<?php
namespace App\Http\Controllers;
use App\Models\{State,Cart};
use App\Services\ShoppingLocation;
use Illuminate\Http\Request;
class ShoppingLocationController extends Controller {
 public function options(){return response()->json(['selected'=>ShoppingLocation::state()?->id,'states'=>State::where('is_active',true)->orderBy('name')->get(['id','name'])]);}
 public function select(Request $request){
  $v=$request->validate(['state_id'=>'required|integer|exists:states,id','items'=>'nullable|array|max:100','items.*'=>'integer']);
  if(!State::whereKey($v['state_id'])->where('is_active',true)->exists())ShoppingLocation::fail('state_id','This state is currently unavailable. Choose another state.');
  $ids=$v['items']??[];
  if(auth()->check())$ids=array_unique(array_merge($ids,Cart::where('user_id',auth()->id())->pluck('product_id')->all()));
  $request->session()->put('shopping_state_id',(int)$v['state_id']);$request->session()->forget('shopping_point_id');
  \Illuminate\Support\Facades\Cookie::queue(cookie('shopping_state_id',(string)$v['state_id'],43200,null,null,$request->isSecure(),true,false,'lax'));
  \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('shopping_point_id'));
  $bad=ShoppingLocation::unavailable($ids);
  return response()->json(['selected'=>(int)$v['state_id'],'incompatible_ids'=>$bad,'name'=>State::findOrFail($v['state_id'])->name,'message'=>$bad?'Some basket items are unavailable in this state. Remove the marked products at checkout to proceed.':null]);
 }
 public function cart(Request $request){
  $v=$request->validate(['items'=>'required|array|max:100','items.*'=>'integer']);
  $bad=ShoppingLocation::unavailable($v['items']);
  return response()->json(['valid'=>!$bad,'incompatible_ids'=>$bad,'message'=>$bad?'Remove the marked products that are unavailable in your selected state to proceed.':null],$bad?422:200);
 }
}
