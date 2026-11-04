<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {Schema::table('order_items',function(Blueprint $t){$t->unsignedBigInteger('colour_id')->nullable();$t->string('colour_name')->nullable();});}
 public function down():void {Schema::table('order_items',fn(Blueprint $t)=>$t->dropColumn(['colour_id','colour_name']));}
};
