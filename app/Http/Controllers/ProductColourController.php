<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Support\ProductColours;
class ProductColourController extends Controller {
 public function index(int $id){$product=Product::with('colors')->findOrFail($id);return response()->json(['product_id'=>$product->id,'colours'=>ProductColours::options($product)]);}
}
