<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(){
 Schema::create('collection_transfer_rates',function(Blueprint $t){$t->id();$t->foreignId('origin_id')->constrained('pickup_points');$t->foreignId('destination_id')->constrained('pickup_points');$t->foreignId('product_id')->nullable()->constrained('products');$t->decimal('fee_ngn',12,2);$t->timestamps();});
 Schema::table('order_items',function(Blueprint $t){$t->unsignedBigInteger('supplying_point_id')->nullable();$t->string('supplying_point_name')->nullable();$t->decimal('transfer_unit_fee_ngn',12,2)->default(0);$t->decimal('transfer_total_ngn',12,2)->default(0);});
 }
 public function down(){Schema::table('order_items',function(Blueprint $t){$t->dropColumn(['supplying_point_id','supplying_point_name','transfer_unit_fee_ngn','transfer_total_ngn']);});Schema::dropIfExists('collection_transfer_rates');}
};
