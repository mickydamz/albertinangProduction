<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};
return new class extends Migration {
 public function up(): void {
  Schema::table('pickup_points',function(Blueprint $t){$t->boolean('is_active')->default(true);$t->unsignedBigInteger('store_location_id')->nullable();});
  Schema::create('product_pickup_point',function(Blueprint $t){$t->unsignedBigInteger('product_id');$t->unsignedBigInteger('pickup_point_id');$t->primary(['product_id','pickup_point_id']);});
  Schema::create('origin_delivery_areas',function(Blueprint $t){$t->id();$t->unsignedBigInteger('pickup_point_id');$t->unsignedBigInteger('location_id');$t->boolean('is_active')->default(true);$t->unique(['pickup_point_id','location_id']);});
  foreach(['orders'] as $table) Schema::table($table,function(Blueprint $t){$t->unsignedBigInteger('shopping_location_id')->nullable();$t->string('shopping_location_name')->nullable();$t->unsignedBigInteger('dispatch_point_id')->nullable();$t->string('dispatch_point_name')->nullable();$t->string('dispatch_point_address',500)->nullable();});
  // Preserve existing assignments. Only an unambiguous single collection branch
  // can inherit an area's products. Multi-branch areas require explicit assignment.
  foreach(DB::table('product_location')->distinct()->pluck('location_id') as $id){
   $points=DB::table('pickup_points')->where('location_id',$id)->get();
   if($points->count()===1) foreach(DB::table('product_location')->where('location_id',$id)->pluck('product_id') as $product) DB::table('product_pickup_point')->insertOrIgnore(['product_id'=>$product,'pickup_point_id'=>$points[0]->id]);
  }
  // No guessed delivery routes. Existing Enugu HQ service is explicitly mapped
  // by the reviewed deployment data step, not inferred from sharing a state.
 }
 public function down(): void {
  Schema::dropIfExists('origin_delivery_areas');Schema::dropIfExists('product_pickup_point');
  Schema::table('pickup_points',fn(Blueprint $t)=>$t->dropColumn(['is_active','store_location_id']));
  Schema::table('orders',fn(Blueprint $t)=>$t->dropColumn(['shopping_location_id','shopping_location_name','dispatch_point_id','dispatch_point_name','dispatch_point_address']));
 }
};
